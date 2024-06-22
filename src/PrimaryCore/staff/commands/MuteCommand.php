<?php

namespace PrimaryCore\staff\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use PrimaryCore\Main;
use PrimaryCore\staff\StaffManager;

class MuteCommand extends Command {


    public function __construct(string $name, string $description = "", ?string $usageMessage = null, array $aliases = []) {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission("global.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$this->testPermission($sender)) {
            $sender->sendMessage("§cYou do not have permission to use this command.");
            return false;
        }

        if (count($args) < 2) {
            $sender->sendMessage("§cUsage: /mute <player> <reason>");
            return false;
        }

        $target = array_shift($args);
        $player = Main::getInstance()->getServer()->getPlayerExact($target);
        $username = $player ? $player->getName() : $target;

        $reason = implode(" ", $args);

        $mutedBy = $sender instanceof Player ? $sender->getName() : "Console";

        Main::getInstance()->getStaffManager()->addPermanentMute($username, $reason, $mutedBy);

        if ($player) {
            $player->sendMessage("§cYou have been permanently muted. Reason: $reason");
        }

        Main::getInstance()->getLogger()->info("$username has been permanently muted by $mutedBy. Reason: $reason");

        return true;
    }
}
