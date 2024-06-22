<?php

namespace PrimaryCore\staff\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use PrimaryCore\Main;

class UnbanCommand extends Command {

    public function __construct(string $name, string $description = "", ?string $usageMessage = null, array $aliases = []) {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission("ban.command"); // Set permission here
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$this->testPermission($sender)) {
            $sender->sendMessage("§cYou do not have permission to use this command.");
            return false;
        }

        if (count($args) < 1) {
            $sender->sendMessage("§cUsage: /unban <player>");
            return false;
        }

        $playerName = $args[0];
        $staffManager = Main::getInstance()->getStaffManager();

        if (!$staffManager->isPlayerBanned($playerName)) {
            $sender->sendMessage("§cPlayer $playerName is not banned.");
            return false;
        }

        // Remove the ban
        $staffManager->removeBan($playerName);
        Main::getInstance()->getLogger()->info("Player $playerName has been unbanned by {$sender->getName()}");

        $sender->sendMessage("§aPlayer $playerName has been unbanned.");
        return true;
    }
}
