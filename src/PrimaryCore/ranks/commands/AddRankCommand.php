<?php

namespace PrimaryCore\ranks\commands;

use PrimaryCore\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use PrimaryCore\utils\translate\TranslateManager;
use pocketmine\Player;

class AddRankCommand extends Command
{
    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission("global.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
      
        $target = $sender;
		if (isset($args[0])) {
			$target = Main::getInstance()->getServer()->getPlayerExact($args[0]);
			if (is_null($target)) {
				$sender->sendMessage("§l§cPlayer not found online");
			}
		}

		if (isset($args[1])) {
			$count = $args[1];
        Main::getInstance()->getRankManager()->addRankToUser($target, $count);

		} else {
			$this->sendUsage();
			return;
		}


    }

}