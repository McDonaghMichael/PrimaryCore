<?php

namespace PrimaryCore\kits;

use pocketmine\player\Player;
use pocketmine\utils\Config;
use PrimaryCore\Main;
use PrimaryCore\kits\commands\KitCommand;

class KitsManager {
    /** @var Kit[] */
    private array $kits = [];
    private Config $cooldownConfig;

    public function __construct() {
        $this->loadKits();
        $this->cooldownConfig = new Config(Main::getInstance()->getDataFolder() . "kit_cooldowns.yml", Config::YAML);
        Main::getInstance()->getServer()->getCommandMap()->registerAll("PrimaryCore", [
            new KitCommand("kit", "Access your kits", "/kit"),
        ]);
    }

    private function loadKits(): void {
        $this->addKit(new Kit(1, "StarterKit", "A starter kit with basic tools.", "global.kit", 3600, [
            // Example items, you should define real item instances
            // new Item(ItemIds::DIAMOND_SWORD, 0, 1),
            // new Item(ItemIds::DIAMOND_CHESTPLATE, 0, 1)
        ]));

        $this->addKit(new Kit(2, "WarriorKit", "A kit for warriors.", "kit.warriorkit", 7200, [
            // Example items, you should define real item instances
            // new Item(ItemIds::IRON_SWORD, 0, 1),
            // new Item(ItemIds::IRON_HELMET, 0, 1)
        ]));
    }

    public function addKit(Kit $kit): void {
        $this->kits[$kit->getId()] = $kit;
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
            $player->sendMessage("Kit not found!");
            return false;
        }

        // Check for permissions
        if (!$player->hasPermission($kit->getPermission())) {
            $player->sendMessage("You do not have permission to use this kit.");
            return false;
        }

        // Check for cooldown
        $cooldownTime = $this->getCooldownTime($player, $kit->getName());
        if ($cooldownTime > 0) {
            $player->sendMessage("You need to wait " . $cooldownTime . " seconds to use this kit again.");
            return false;
        }

        // Give the kit to the player
        foreach ($kit->getItems() as $item) {
            // $player->getInventory()->addItem($item); // Uncomment and use real item instances
        }

        foreach ($kit->getEffects() as $effect) {
            // $player->addEffect($effect); // Uncomment and use real effect instances
        }

        // Set cooldown
        $this->setCooldown($player, $kit->getName(), $kit->getCooldown());

        $player->sendMessage("You have received the " . $kit->getName() . " kit!");
        return true;
    }

    public function getAllKits(): array {
        return $this->kits;
    }

    public function getCooldownTime(Player $player, string $kitName): int {
        $playerName = $player->getName();
        $cooldowns = $this->cooldownConfig->get($playerName, []);
        $currentTimestamp = time();

        if (isset($cooldowns[$kitName])) {
            $remainingTime = $cooldowns[$kitName] - $currentTimestamp;
            return $remainingTime > 0 ? $remainingTime : 0;
        }
        return 0;
    }

    public function setCooldown(Player $player, string $kitName, int $cooldown): void {
        $playerName = $player->getName();
        $currentTimestamp = time();
        $cooldowns = $this->cooldownConfig->get($playerName, []);
        $cooldowns[$kitName] = $currentTimestamp + $cooldown;

        $this->cooldownConfig->set($playerName, $cooldowns);
        $this->cooldownConfig->save();
    }
}
