<?php

namespace PrimaryCore\economy\commands;

use PrimaryCore\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use PrimaryCore\utils\translate\TranslateManager;

class ResetCoinsCommand extends Command
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
			}else{
                Main::getInstance()->getEconomyManager()->resetCoins($target);
            }
		}

        
	


    }

}