<?php
namespace PrimaryCore\utils;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\world\particle\FloatingTextParticle;
use pocketmine\world\Position;
use pocketmine\utils\Config;

use PrimaryCore\Main;

class FloatingTextAPI {

    // Array to track floating texts by player UUID
    private static array $playerFloatingTexts = [];

    public static function create(Player $player, Position $position, string $tag, string $text): void {
        $world = $position->getWorld();
        if ($world !== null) {
            $chunk = $world->getOrLoadChunkAtPosition($position);
            if ($chunk !== null) {
                $floatingText = new FloatingTextParticle(str_replace("{line}", "\n", $text));
                self::remove($player, $tag); // Remove existing floating text if any
                
                // Store the floating text specific to the player
                self::$playerFloatingTexts[$player->getUniqueId()->toString()][$tag] = [$position, $floatingText];
                
                // Send the particle to the specific player
                $player->getWorld()->addParticle($position, $floatingText, [$player]);
            } else {
                Server::getInstance()->getLogger()->warning("Chunk not loaded for floating text with tag '$tag' for player '{$player->getName()}'.");
            }
        }
    }

    public static function remove(Player $player, string $tag): void {
        $uuid = $player->getUniqueId()->toString();
        if (!isset(self::$playerFloatingTexts[$uuid][$tag])) {
            return;
        }

        $floatingText = self::$playerFloatingTexts[$uuid][$tag][1];
        $floatingText->setInvisible();
        $position = self::$playerFloatingTexts[$uuid][$tag][0];
        $position->getWorld()->addParticle($position, $floatingText, [$player]);

        // Remove the floating text from the tracking array
        unset(self::$playerFloatingTexts[$uuid][$tag]);
    }

    public static function update(Player $player, string $tag, string $text): void {
        $uuid = $player->getUniqueId()->toString();
        if (!isset(self::$playerFloatingTexts[$uuid][$tag])) {
            return;
        }

        $floatingText = self::$playerFloatingTexts[$uuid][$tag][1];
        $floatingText->setText(str_replace("{line}", "\n", $text));
        $position = self::$playerFloatingTexts[$uuid][$tag][0];
        $position->getWorld()->addParticle($position, $floatingText, [$player]);
    }

    // Optional method to initialize floating text for a player
    public static function initializePlayerFloatingTexts(Player $player): void {
        // Add default floating texts here if needed
        $position = new Position(100, 65, 100, $player->getWorld());
        self::create($player, $position, "welcome", "Welcome {line} to the server, {line}" . $player->getName());
    }

    // Optional method to clean up floating text for a player
    public static function cleanUpPlayerFloatingTexts(Player $player): void {
        $uuid = $player->getUniqueId()->toString();
        if (isset(self::$playerFloatingTexts[$uuid])) {
            foreach (self::$playerFloatingTexts[$uuid] as $tag => $data) {
                self::remove($player, $tag);
            }
            unset(self::$playerFloatingTexts[$uuid]);
        }
    }
}
