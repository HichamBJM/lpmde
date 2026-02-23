<?php

namespace App\Controller;

use App\Enum\OrderStatus;
use App\Message\ShippingRequested;
use App\Service\CartService;
use App\Service\OrderService;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

final class CommandeController extends AbstractController
{
    #[Route('/panier', name: 'app_panier', methods: ['GET'])]
    public function panier(CartService $cartService): Response
    {
        return $this->render('commande/panier.html.twig', [
            'catalog' => $cartService->catalog(),
            'items' => $cartService->getDetailedItems(),
            'totalAmount' => $cartService->totalAmount(),
        ]);
    }

    #[Route('/panier/ajouter/{sku}', name: 'app_panier_ajouter', methods: ['POST'])]
    public function ajouter(string $sku, CartService $cartService, Request $request): RedirectResponse
    {
        $cartService->add($sku);
        $this->addFlash('success', 'Produit ajouté au panier.');

        return $this->redirect($request->headers->get('referer') ?: $this->generateUrl('app_panier'));
    }

    #[Route('/panier/mettre-a-jour/{sku}', name: 'app_panier_maj', methods: ['POST'])]
    public function mettreAJour(string $sku, Request $request, CartService $cartService): RedirectResponse
    {
        $quantity = max(0, (int) $request->request->get('quantity', 1));
        $cartService->updateQuantity($sku, $quantity);

        return $this->redirectToRoute('app_panier');
    }

    #[Route('/panier/supprimer/{sku}', name: 'app_panier_supprimer', methods: ['POST'])]
    public function supprimer(string $sku, CartService $cartService): RedirectResponse
    {
        $cartService->remove($sku);
        $this->addFlash('success', 'Produit retiré du panier.');

        return $this->redirectToRoute('app_panier');
    }

    #[Route('/commandes/valider', name: 'app_commande_valider', methods: ['POST'])]
    public function valider(Request $request, CartService $cartService, OrderService $orderService, MessageBusInterface $bus): RedirectResponse
    {
        if (!$request->getSession()->get('is_authenticated')) {
            $this->addFlash('warning', 'Vous devez être connecté pour passer une commande.');

            return $this->redirectToRoute('app_login_keycloak');
        }

        try {
            $order = $orderService->createFromCart($cartService->getDetailedItems(), $cartService->totalAmount());
            $order = $orderService->transition((string) $order['number'], OrderStatus::PENDING_PAYMENT, 'Commande créée, attente de paiement');
            $order = $orderService->transition((string) $order['number'], OrderStatus::PAID, 'Paiement validé');
            $order = $orderService->transition((string) $order['number'], OrderStatus::PREPARING, 'Préparation logistique lancée');

            if (null !== $order) {
                $bus->dispatch(new ShippingRequested((string) $order['number']));
            }

            $cartService->clear();
            $this->addFlash('success', 'Commande validée. Préparation et orchestration d\'expédition lancées.');
        } catch (InvalidArgumentException $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('app_commandes');
    }

    #[Route('/commandes', name: 'app_commandes', methods: ['GET'])]
    public function commandes(OrderService $orderService): Response
    {
        return $this->render('commande/commandes.html.twig', [
            'orders' => $orderService->all(),
        ]);
    }

    #[Route('/commandes/{number}/expedier', name: 'app_commande_expedier', methods: ['POST'])]
    public function expedier(string $number, OrderService $orderService): RedirectResponse
    {
        try {
            $orderService->transition($number, OrderStatus::SHIPPED, 'Colis remis au transporteur');
            $this->addFlash('success', sprintf('Commande %s expédiée.', $number));
        } catch (InvalidArgumentException $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('app_commandes');
    }

    #[Route('/commandes/{number}/livrer', name: 'app_commande_livrer', methods: ['POST'])]
    public function livrer(string $number, OrderService $orderService): RedirectResponse
    {
        try {
            $orderService->transition($number, OrderStatus::DELIVERED, 'Commande livrée au client');
            $this->addFlash('success', sprintf('Commande %s livrée.', $number));
        } catch (InvalidArgumentException $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('app_commandes');
    }
}
