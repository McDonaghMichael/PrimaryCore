<?php

namespace PrimaryCore\listeners\sub;

use PrimaryCore\Main;
use PrimaryCore\database\UserManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;


class onJoinListener implements Listener
{
    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        $playerName = $player->getName();
        
        $userManager = Main::getInstance()->getUserManager();
        
        // Check if the player exists in the database
        if (!$userManager->playerExists($playerName)) {
            // If the player does not exist, insert them into the database
            $userManager->insertPlayer($playerName);
            // Optionally, you can perform additional actions here, such as setting initial player data
        }
        
        // Set join message
        $event->setJoinMessage(Main::getInstance()->getTranslateManager()->translate('onJoin', ['USERNAME' => $playerName]));
       Main::$bar->addPlayer($player);
    }
}
