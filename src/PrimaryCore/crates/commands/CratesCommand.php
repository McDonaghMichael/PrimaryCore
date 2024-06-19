<?php

namespace PrimaryCore\crates\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use PrimaryCore\crates\KeyManager;
use PrimaryCore\Main;

class CratesCommand extends Command
{
    /** @var KeyManager */
    private $keyManager;

    public function __construct() {
        parent::__construct("crates", "Manage crate keys for players", "/crates <add|remove|set|reset> <player> <keyType> <amount>");
        $this->setPermission("global.command");
        $this->keyManager = new KeyManager();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$this->testPermission($sender)) {
            return true;
        }

        if (count($args) < 4) {
            $sender->sendMessage(TF::RED . "Usage: /crates <add|remove|set|reset> <player> <keyType> <amount>");
            return false;
        }

        $subCommand = strtolower(array_shift($args));
        $playerName = array_shift($args);
        $keyType = strtolower(array_shift($args));
        $amount = (int) array_shift($args);

        $player = Main::getInstance()->getServer()->getPlayerExact($playerName);

        if ($subCommand === "add") {
            if ($player instanceof Player) {
                $this->keyManager->addKeys($player->getName(), $keyType, $amount);
                $sender->sendMessage(TF::GREEN . "Added $amount $keyType key(s) to {$player->getName()}'s inventory.");
            } else {
                $sender->sendMessage(TF::RED . "Player not found or not online.");
            }
        } elseif ($subCommand === "remove") {
            if ($player instanceof Player) {
                $this->keyManager->removeKeys($player->getName(), $keyType, $amount);
                $sender->sendMessage(TF::GREEN . "Removed $amount $keyType key(s) from {$player->getName()}'s inventory.");
            } else {
                $sender->sendMessage(TF::RED . "Player not found or not online.");
            }
        } elseif ($subCommand === "set") {
            if ($player instanceof Player) {
                $this->keyManager->setKeyCounts($player->getName(), [$keyType => $amount]);
                $sender->sendMessage(TF::GREEN . "Set {$player->getName()}'s $keyType key count to $amount.");
            } else {
                $sender->sendMessage(TF::RED . "Player not found or not online.");
            }
        } elseif ($subCommand === "reset") {
            if ($player instanceof Player) {
                $this->keyManager->resetKeys($player->getName());
                $sender->sendMessage(TF::GREEN . "Reset {$player->getName()}'s key counts.");
            } else {
                $sender->sendMessage(TF::RED . "Player not found or not online.");
            }
        } else {
            $sender->sendMessage(TF::RED . "Usage: /crates <add|remove|set|reset> <player> <keyType> <amount>");
            return false;
        }

        return true;
    }
}
