<?php

namespace PrimaryCore\crates;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use PrimaryCore\Main;
use PrimaryCore\utils\FloatingTextAPI;
use pocketmine\world\Position;
use pocketmine\world\particle\ExplodeParticle;

class CratesListener implements Listener
{
    public function onPlayerInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $action = $event->getAction();
        $block = $event->getBlock();

        if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            foreach (Main::getInstance()->getCratesManager()->getAllCrates() as $crate) {
                $cratePosition = $crate->getPosition();
                if ($block->getPosition()->equals($cratePosition) && $player->getWorld()->getFolderName() === $crate->getWorld()) {
                    // Check if player has enough keys for this crate
                    $keyType = $crate->getName(); // Assuming crate name matches key type
                    $requiredKeys = 1; // Example: Assume 1 key is required for interaction

                    if (Main::getInstance()->getCratesManager()->getKeyManager()->hasKeys($player->getName(), $keyType, $requiredKeys)) {
                        // Player has enough keys, perform crate interaction logic here
                        $player->sendMessage(TF::GREEN . "You tapped the crate '{$crate->getName()}'");
                        $particle = $player->getWorld()->addParticle($cratePosition, new ExplodeParticle());
                        // Remove keys from player's inventory
                        Main::getInstance()->getCratesManager()->getKeyManager()->removeKeys($player->getName(), $keyType, $requiredKeys);
                        Main::getInstance()->getCratesManager()->updateCrateText($player);

                    } else {
                        // Player does not have enough keys
                        $player->sendMessage(TF::RED . "You do not have enough '{$keyType}' keys to open this crate.");
                    }

                    break;
                }
            }
        }
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $world = Main::getInstance()->getServer()->getWorldManager()->getWorldByName("world");
        $position = new Position(100, 100, 100, $world);
        FloatingTextAPI::create($player, $position, "Test", "Loading...");
        Main::getInstance()->getCratesManager()->updateCrateText($player);

    }
}
