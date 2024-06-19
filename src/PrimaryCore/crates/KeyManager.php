<?php

namespace PrimaryCore\crates;

use pocketmine\utils\Config;
use PrimaryCore\Main;

class KeyManager {

    /** @var Config */
    private $keyConfig;

    public function __construct() {
        $plugin = Main::getInstance();
        $this->keyConfig = new Config($plugin->getDataFolder() . "keys.yml", Config::YAML);
    }

    /**
     * Get key counts for a player from the config.
     *
     * @param string $playerName
     * @return array
     */
    public function getKeyCounts(string $playerName): array {
        return $this->keyConfig->get($playerName, []);
    }

    /**
     * Set key counts for a player in the config.
     *
     * @param string $playerName
     * @param array $keyCounts
     */
    public function setKeyCounts(string $playerName, array $keyCounts): void {
        $this->keyConfig->set($playerName, $keyCounts);
        $this->keyConfig->save();
    }

    /**
     * Check if a player has a specific amount of keys.
     *
     * @param string $playerName
     * @param string $keyType
     * @param int $amount
     * @return bool
     */
    public function hasKeys(string $playerName, string $keyType, int $amount): bool {
        $keyCounts = $this->getKeyCounts($playerName);
        return isset($keyCounts[$keyType]) && $keyCounts[$keyType] >= $amount;
    }

    /**
     * Add keys to a player's existing key counts.
     *
     * @param string $playerName
     * @param string $keyType
     * @param int $amount
     */
    public function addKeys(string $playerName, string $keyType, int $amount): void {
        $keyCounts = $this->getKeyCounts($playerName);
        $keyCounts[$keyType] = ($keyCounts[$keyType] ?? 0) + $amount;
        $this->setKeyCounts($playerName, $keyCounts);
    }

    /**
     * Remove keys from a player's existing key counts.
     *
     * @param string $playerName
     * @param string $keyType
     * @param int $amount
     */
    public function removeKeys(string $playerName, string $keyType, int $amount): void {
        $keyCounts = $this->getKeyCounts($playerName);
        if (isset($keyCounts[$keyType])) {
            $keyCounts[$keyType] = max(0, $keyCounts[$keyType] - $amount);
            $this->setKeyCounts($playerName, $keyCounts);
        }
    }

    /**
     * Reset all key counts for a player.
     *
     * @param string $playerName
     */
    public function resetKeys(string $playerName): void {
        $this->setKeyCounts($playerName, []);
    }

    /**
     * Get the count of a specific key for a player.
     *
     * @param string $playerName
     * @param string $keyType
     * @return int
     */
    public function getSpecificKeyCount(string $playerName, string $keyType): int {
        $keyCounts = $this->getKeyCounts($playerName);
        return $keyCounts[$keyType] ?? 0;
    }
}
