<?php

namespace PrimaryCore\staff\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use PrimaryCore\Main;
use pocketmine\lang\Translatable;
use PrimaryCore\utils\translate\TranslateManager;

class BanCommand extends Command {

    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission("ban.command"); // Set permission here
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {

        if (count($args) < 1) {
            $sender->sendMessage("§cUsage: /ban <player> [reason]");
            return false;
        }

        $player = $sender;
        if ($player === null) {
            $sender->sendMessage("§cPlayer not found.");
            return false;
        }

        $reason = isset($args[1]) ? implode(" ", array_slice($args, 1)) : "No reason specified";

        $translateManager = Main::getInstance()->getTranslateManager(); // Access TranslateManager using Main::getInstance()

        // Send ban message to the banned player

        // Send broadcast message to the server
        Main::getInstance()->getServer()->broadcastMessage($translateManager->translate("banBroadcast", ["{PLAYER}" => $player->getName(), "{SENDER}" => $sender->getName(), "{REASON}" => $reason]));
        
        // Log ban information
        Main::getInstance()->getLogger()->info("Player {$player->getName()} has been banned by {$sender->getName()} for reason: $reason");
    

        return true;
    }

   
        
       
}
