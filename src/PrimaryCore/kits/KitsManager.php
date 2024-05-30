<?php

namespace PrimaryCore\kits;

use pocketmine\Player;
use PrimaryCore\Main;


class KitsManager {
    /** @var Kit[] */
    private array $kits = [];

    public function __construct() {
        $this->loadKits();
    }

    private function loadKits(): void {
      
        $this->addKit(new Kit(1, "StarterKit", "A starter kit with basic tools.",
            3600
        ));

        $this->addKit(new Kit(2,
            "WarriorKit",
            "A kit for warriors.",
            7200
            
        ));
    }

    public function addKit(Kit $kit): void {
        $this->kits[$kit->getId()] = $kit;
        Main::getInstance()->getLogger()->error($kit->getName());
    }

    public function getKitByName(string $name): ?Kit {
        foreach ($this->kits as $kit) {
            if ($kit->getName() === $name) {
                return $kit;
            }
        }
        return null;
    }

    public function getKitById(int $id): ?Kit {
        return $this->kits[$id] ?? null;
    }

    public function giveKit(Player $player, string $name): bool {
        $kit = $this->getKitByName($name);
        if ($kit === null) {
            return false;
        }

        return true;
    }

    public function getAllKits(): array {
        return $this->kits;
    }
}
