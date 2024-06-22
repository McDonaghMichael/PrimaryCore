<?php

namespace PrimaryCore\staff\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use PrimaryCore\Main;
use PrimaryCore\staff\StaffManager;

class UnmuteCommand extends Command {

    public function __construct(string $name, string $description = "", ?string $usageMessage = null, array $aliases = []) {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission("global.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$this->testPermission($sender)) {
            $sender->sendMessage("§cYou do not have permission to use this command.");
            return false;
        }

        if (count($args) !== 1) {
            $sender->sendMessage("§cUsage: /unmute <player>");
            return false;
        }

        $target = array_shift($args);
        $player = Main::getInstance()->getServer()->getPlayerExact($target);
        $username = $player ? $player->getName() : $target;

        if (!Main::getInstance()->getStaffManager()->isPlayerMuted($username)) {
            $sender->sendMessage("§c$username is not muted.");
            return false;
        }

        Main::getInstance()->getStaffManager()->removeMute($username);

        if ($player) {
            $player->sendMessage("§aYou have been unmuted.");
        }

        Main::getInstance()->getLogger()->info("$username has been unmuted by " . ($sender instanceof Player ? $sender->getName() : "Console"));

        return true;
    }
}
