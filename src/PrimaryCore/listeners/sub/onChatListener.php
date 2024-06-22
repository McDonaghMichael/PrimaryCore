<?php

namespace PrimaryCore\listeners\sub;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\player\Player;
use PrimaryCore\Main;

class onChatListener implements Listener
{
    public function onPlayerChat(PlayerChatEvent $event): void {
        $player = $event->getPlayer();
        $message = $event->getMessage();

        // Check if player is muted
        if (Main::getInstance()->getStaffManager()->isPlayerMuted($player->getName())) {
            // Allow commands starting with '/' or './'
            if (!($message[0] === '/' || substr($message, 0, 2) === './')) {
                $event->cancel();
                $player->sendMessage("You are muted and cannot send messages.");
                return;
            }
        }

        if (Main::getInstance()->getStaffManager()->isInStaffChat($player)) {
            foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $staff) {
                if ($staff instanceof Player) {
                    if(Main::getInstance()->getStaffManager()->isInStaffChat($staff)){
                    $staff->sendMessage(Main::getInstance()->getRankManager()->getRankForPlayer($player)->getFormat() . " " . $player->getName() . " - " . $message);
                    }
                }
            }
            $event->cancel(); // Cancel the original message
            return;
        }

        // Format chat message
        $event->setFormatter(new LegacyRawChatFormatter(Main::getInstance()->getRankManager()->getChatFormat($player, $message)));
    }
}
