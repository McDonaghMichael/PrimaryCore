<?php

namespace PrimaryCore\listeners\sub;

use PrimaryCore\Main;
use PrimaryCore\database\UserManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\Player\Player;

class onLoginListener implements Listener
{
    public function onPreLogin(PlayerLoginEvent $event)
    {
        $player = $event->getPlayer();
        $playerName = $player->getName();
        
        $staffManager = Main::getInstance()->getStaffManager();
        

        if ($staffManager->isPlayerBanned($playerName)) {
            $banReason = $staffManager->getBanReason($playerName);
            $duration = $staffManager->getBanDuration($playerName);
          
            // Kick the player with a ban message
            $player->kick("You are banned!\nReason: $banReason\nDuration: $duration", false);
            return;
        }
    }
}
