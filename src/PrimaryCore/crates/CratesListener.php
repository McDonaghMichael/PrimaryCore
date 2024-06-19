<?php

namespace PrimaryCore\crates;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use PrimaryCore\Main;

class CratesListener implements Listener
{
    public function onPlayerInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $action = $event->getAction();
        $block = $event->getBlock();

        if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            foreach (Main::getInstance()->getCratesManager()->getAllCrates() as $crate) {
                $cratePosition = $crate->getPosition();
                if ($block->getPosition()->equals($cratePosition)) {
                    // Check if player has enough keys for this crate
                    $keyType = strtolower($crate->getName()); // Assuming crate name matches key type
                    $requiredKeys = 1; // Example: Assume 1 key is required for interaction

                    if (Main::getInstance()->getCratesManager()->getKeyManager()->hasKeys($player->getName(), $keyType, $requiredKeys)) {
                        // Player has enough keys, perform crate interaction logic here
                        $player->sendMessage(TF::GREEN . "You tapped the crate '{$crate->getName()}'");

                        // Remove keys from player's inventory
                        Main::getInstance()->getCratesManager()->getKeyManager()->removeKeys($player->getName(), $keyType, $requiredKeys);

                        // Perform crate reward logic here
                        // Example: $crate->giveReward($player);

                    } else {
                        // Player does not have enough keys
                        $player->sendMessage(TF::RED . "You do not have enough '{$keyType}' keys to open this crate.");
                    }

                    break;
                }
            }
        }
    }
}
