<?php

namespace App\Controller;

use App\Message\GhostAlert;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

final class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(): Response
    {
        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
        ]);
    }

    #[Route('/test-rabbit', name: 'app_test_rabbit')]
    public function testRabbit(MessageBusInterface $bus): Response
    {
        $monstres = ['Vampire', 'Zombi', 'Loup-Garou', 'Poltergeist', 'Banshee'];
        $lieux = ['Cave à vin', 'Grenier', 'Cimetière', 'Cuisine', 'Chambre 217'];

        // ON ENVOIE 50 MESSAGES D'UN COUP (Mitraillette)
        for ($i = 0; $i < 50; $i++) {
            $monstre = $monstres[array_rand($monstres)];
            $lieu = $lieux[array_rand($lieux)];

            $bus->dispatch(new GhostAlert($lieu, $monstre));
        }

        return new Response('50 Alertes envoyées à RabbitMQ ! Regardez le terminal et le graphique.');
    }
}
