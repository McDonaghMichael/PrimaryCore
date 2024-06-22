<?php

namespace PrimaryCore\staff\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use PrimaryCore\Main;
use PrimaryCore\staff\StaffManager;

class TempMuteCommand extends Command {

    public function __construct(string $name, string $description = "", ?string $usageMessage = null, array $aliases = []) {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission("global.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$this->testPermission($sender)) {
            $sender->sendMessage("§cYou do not have permission to use this command.");
            return false;
        }

        if (count($args) < 3) {
            $sender->sendMessage("§cUsage: /tempmute <player> <duration> <reason>");
            return false;
        }

        $target = array_shift($args);
        $player = Main::getInstance()->getServer()->getPlayerExact($target);
        $username = $player ? $player->getName() : $target;

        if (!is_numeric($args[0])) {
            $sender->sendMessage("§cDuration must be a number.");
            return false;
        }

        $duration = (int) array_shift($args);
        $reason = implode(" ", $args);

        $mutedBy = $sender instanceof Player ? $sender->getName() : "Console";

        Main::getInstance()->getStaffManager()->addTemporaryMute($username, $duration, $reason, $mutedBy);

        if ($player) {
            $player->sendMessage("§cYou have been temporarily muted for $duration minutes. Reason: $reason");
        }

        Main::getInstance()->getLogger()->info("$username has been temporarily muted by $mutedBy for $duration minutes. Reason: $reason");

        return true;
    }
}
