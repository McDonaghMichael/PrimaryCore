<?php

namespace PrimaryCore\commands\sub;

use PrimaryCore\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use PrimaryCore\utils\translate\TranslateManager;

class ExampleCommand extends Command
{
    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission("global.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $translateManager = new TranslateManager();
        $message = $translateManager->translate('errorMessage', ['var1' => 'an unexpected', 'var2' => 'a critical']);
        $message = $translateManager->translate('resetMessage', []);
       // Main::getInstance()->getRankManager()->addRankToUser($sender, 60);
       Main::getInstance()->getRankManager()->removeRankFromUser($sender, 60);
        $sender->sendMessage($message);
    }

}