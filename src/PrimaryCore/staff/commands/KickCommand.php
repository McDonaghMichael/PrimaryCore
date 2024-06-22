<?php

namespace PrimaryCore\staff\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use PrimaryCore\Main;
use pocketmine\lang\Translatable;
use PrimaryCore\utils\translate\TranslateManager;

class KickCommand extends Command {

    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission("global.command"); // Set permission here
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {

        if (count($args) < 1) {
            $sender->sendMessage("§cUsage: /kick <player> [reason]");
            return false;
        }

        $player = Main::getInstance()->getServer()->getPlayerExact($args[0]);
        

        if (!$player instanceof Player) {
            $sender->sendMessage("§cPlayer not found.");
            return false;
        }

        $reason = isset($args[1]) ? implode(" ", array_slice($args, 1)) : "No reason specified";

        $translateManager = Main::getInstance()->getTranslateManager(); // Access TranslateManager using Main::getInstance()
        $player->kick($reason);
        Main::getInstance()->getLogger()->info("Player {$player->getName()} has been kicked by {$sender->getName()} for reason: $reason");
    

        return true;
    }

   
        
       
}
