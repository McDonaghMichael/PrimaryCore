<?php

namespace PrimaryCore\staff\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use PrimaryCore\Main;
use PrimaryCore\staff\StaffManager;

class TempBanCommand extends Command {

    public function __construct(string $name, string $description = "", ?string $usageMessage = null, array $aliases = []) {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission("global.command"); // Set permission here
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$this->testPermission($sender)) {
            $sender->sendMessage("§cYou do not have permission to use this command.");
            return false;
        }

        if (count($args) < 2) {
            $sender->sendMessage("§cUsage: /tempban <player> <duration_in_seconds> [reason]");
            return false;
        }

        $playerName = $args[0];
        $duration = (int)$args[1];

        if ($duration <= 0) {
            $sender->sendMessage("§cDuration must be a positive integer.");
            return false;
        }

        $reason = isset($args[2]) ? implode(" ", array_slice($args, 2)) : "No reason specified";

        $staffManager = Main::getInstance()->getStaffManager();
        if ($staffManager->isPlayerBanned($playerName)) {
            $sender->sendMessage("§cPlayer $playerName is already banned.");
            return false;
        }

        $staffManager->addTemporaryBan($playerName, $duration, $reason, $sender->getName());
        Main::getInstance()->getLogger()->info("Player $playerName has been temporarily banned by {$sender->getName()} for $duration seconds for reason: $reason");

        $sender->sendMessage("§aPlayer $playerName has been temporarily banned for $duration seconds.");
        return true;
    }
}
