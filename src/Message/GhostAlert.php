<?php

namespace App\Message;

class GhostAlert
{
    private string $location;
    private string $monsterType;

    public function __construct(string $location, string $monsterType)
    {
        $this->location = $location;
        $this->monsterType = $monsterType;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getMonsterType(): string
    {
        return $this->monsterType;
    }
}