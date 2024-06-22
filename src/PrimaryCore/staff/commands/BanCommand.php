<?php

namespace PrimaryCore\staff\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use PrimaryCore\Main;
use PrimaryCore\staff\StaffManager;

class BanCommand extends Command {

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
            $sender->sendMessage("§cUsage: /ban <player> [reason]");
            return false;
        }

        $playerName = $args[0];
        $reason = isset($args[1]) ? implode(" ", array_slice($args, 1)) : "No reason specified";

        $staffManager = Main::getInstance()->getStaffManager();
        if ($staffManager->isPlayerBanned($playerName)) {
            $sender->sendMessage("§cPlayer $playerName is already banned.");
            return false;
        }

        $staffManager->addPermanentBan($playerName, $reason, $sender->getName());
        Main::getInstance()->getLogger()->info("Player $playerName has been permanently banned by {$sender->getName()} for reason: $reason");

        $sender->sendMessage("§aPlayer $playerName has been permanently banned.");
        return true;
    }
}
