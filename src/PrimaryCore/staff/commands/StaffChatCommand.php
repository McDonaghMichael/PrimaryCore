<?php

namespace PrimaryCore\staff\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrimaryCore\Main;
use PrimaryCore\staff\StaffManager;

class StaffChatCommand extends Command {


    public function __construct(string $name, string $description = "", ?string $usageMessage = null, array $aliases = []) {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission("global.command"); // Set permission here
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$this->testPermission($sender)) {
            $sender->sendMessage("§cYou do not have permission to use this command.");
            return false;
        }

        if (!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can only be used in-game.");
            return false;
        }

        $player = $sender;
        $playerName = $player->getName();

        if (Main::getInstance()->getStaffManager()->isInStaffChat($player)) {
            Main::getInstance()->getStaffManager()->removeFromStaffChat($player);
            $player->sendMessage("§aYou have left the staff chat.");
        } else {
            Main::getInstance()->getStaffManager()->addToStaffChat($player);
            $player->sendMessage("§aYou have joined the staff chat.");
        }

        return true;
    }
}
