<?php

namespace App\MessageHandler;

use App\Message\GhostAlert;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GhostAlertHandler
{
    public function __invoke(GhostAlert $alert)
    {
        // C'est ici qu'on fait le traitement lourd !
        // Pour la démo, on simule une attente de 5 secondes
        sleep(5); 

        // On affiche juste un message (visible dans les logs du worker)
        echo "👻 ALERTE TRAITÉE : Un " . $alert->getMonsterType() . " a été vu dans : " . $alert->getLocation() . "\n";
    }
}