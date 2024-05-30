<?php

namespace PrimaryCore\gangs\commands;

use PrimaryCore\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use PrimaryCore\utils\translate\TranslateManager;
use pocketmine\Player;
use PrimaryCore\gangs\forms\create\CreateGangForm;
use PrimaryCore\gangs\forms\menu\MenuGangForm;

class GangCommand extends Command
{
    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission("global.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
      
        if(Main::getInstance()->getGangManager()->isPlayerInGang($sender->getName())){
            $gangForm = new MenuGangForm();
            $form = $gangForm->load($sender);
            $sender->sendForm($form);
        }else{
            $gangForm = new CreateGangForm();
            $form = $gangForm->load($sender);
            $sender->sendForm($form);
        }
        


    }

}