<?php

namespace PrimaryCore\economy\commands;

use PrimaryCore\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use PrimaryCore\utils\translate\TranslateManager;

class CoinsCommand extends Command
{
    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission("global.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
      
        $sender->sendMessage(Main::getInstance()->getEconomyManager()->getCoins($sender));
    }

}