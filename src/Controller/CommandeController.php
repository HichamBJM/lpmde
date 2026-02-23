<?php

namespace App\Controller;

use App\Enum\OrderStatus;
use App\Message\ShippingRequested;
use App\Repository\UserRepository;
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
            $order = $orderService->createFromCart(
                $cartService->getDetailedItems(),
                $cartService->totalAmount(),
                (string) $request->getSession()->get('user_email', '')
            );
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
    public function commandes(Request $request, OrderService $orderService, UserRepository $userRepository): Response
    {
        if (!$request->getSession()->get('is_authenticated')) {
            $this->addFlash('warning', 'Vous devez être connecté pour consulter vos commandes.');

            return $this->redirectToRoute('app_login_keycloak');
        }

        $isAdmin = $this->isAdmin($request, $userRepository);
        $activeTab = (string) $request->query->get('tab', 'mine');

        if ($isAdmin && 'to-process' === $activeTab) {
            $orders = $orderService->toProcess();
        } else {
            $orders = $orderService->forCustomer((string) $request->getSession()->get('user_email', ''));
            $activeTab = 'mine';
        }

        return $this->render('commande/commandes.html.twig', [
            'orders' => $orders,
            'isAdmin' => $isAdmin,
            'activeTab' => $activeTab,
        ]);
    }

    #[Route('/commandes/{number}/expedier', name: 'app_commande_expedier', methods: ['POST'])]
    public function expedier(Request $request, string $number, OrderService $orderService, UserRepository $userRepository): RedirectResponse
    {
        if (!$this->isAdmin($request, $userRepository)) {
            $this->addFlash('error', 'Action réservée aux administrateurs.');

            return $this->redirectToRoute('app_commandes');
        }

        try {
            $orderService->transition($number, OrderStatus::SHIPPED, 'Colis remis au transporteur');
            $this->addFlash('success', sprintf('Commande %s expédiée.', $number));
        } catch (InvalidArgumentException $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('app_commandes');
    }

    #[Route('/commandes/{number}/livrer', name: 'app_commande_livrer', methods: ['POST'])]
    public function livrer(Request $request, string $number, OrderService $orderService, UserRepository $userRepository): RedirectResponse
    {
        if (!$this->isAdmin($request, $userRepository)) {
            $this->addFlash('error', 'Action réservée aux administrateurs.');

            return $this->redirectToRoute('app_commandes');
        }

        try {
            $orderService->transition($number, OrderStatus::DELIVERED, 'Commande livrée au client');
            $this->addFlash('success', sprintf('Commande %s livrée.', $number));
        } catch (InvalidArgumentException $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('app_commandes');
    }

    private function isAdmin(Request $request, UserRepository $userRepository): bool
    {
        $session = $request->getSession();
        $userId = $session->get('user_id');

        if (null !== $userId) {
            $user = $userRepository->find((int) $userId);
            if (null !== $user) {
                $isAdmin = in_array('ROLE_ADMIN', $user->getRoles(), true);
                $session->set('is_admin', $isAdmin);

                return $isAdmin;
            }
        }

        return (bool) $session->get('is_admin', false);
    }
}
