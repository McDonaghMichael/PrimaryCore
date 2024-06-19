<?php

namespace PrimaryCore\economy;

use PrimaryCore\database\DatabaseManager;
use PrimaryCore\economy\commands\AddCoinsCommand;
use PrimaryCore\economy\commands\CoinsCommand;
use PrimaryCore\economy\commands\RemoveCoinsCommand;
use PrimaryCore\economy\commands\ResetCoinsCommand;
use PrimaryCore\economy\commands\SetCoinsCommand;
use PrimaryCore\economy\commands\TopCoinsCommand;
use PrimaryCore\economy\commands\ShopCommand;
use pocketmine\Player;
use PrimaryCore\Main;

class EconomyManager {
    
    /** @var DatabaseManager */
    private $databaseManager;

    public function __construct(
    ) {
        $this->databaseManager = Main::getInstance()->getDatabaseManager();
        Main::getInstance()->getServer()->getCommandMap()->registerAll("PrimaryCore", [
            new AddCoinsCommand("addcoins", "Permet d'aller au spawn", "/coins"),
            new CoinsCommand("coins", "Permet d'aller au spawn", "/coins"),
            new RemoveCoinsCommand("removecoins", "Permet d'aller au spawn", "/coins"),
            new ResetCoinsCommand("resetcoins", "Permet d'aller au spawn", "/coins"),
            new SetCoinsCommand("setcoins", "Permet d'aller au spawn", "/coins"),
            new TopCoinsCommand("topcoins", "Permet d'aller au spawn", "/coins"),
            new ShopCommand("shop", "Permet d'aller au spawn", "/coins"),
        ]);
    }

    public function addCoins($player, int $amount): void {
        $currentCoins = $this->getCoins($player);
        $newCoins = $currentCoins + $amount;
        $this->setCoins($player, $newCoins);
    }

    public function removeCoins($player, int $amount): void {
        $currentCoins = $this->getCoins($player);
        $newCoins = max(0, $currentCoins - $amount); // Ensure coins don't go negative
        $this->setCoins($player, $newCoins);
    }

    public function setCoins($player, int $amount): void {
        $username = $player->getName();
        $db = $this->databaseManager->getDatabase();
        $stmt = $db->prepare("UPDATE economy SET coins = :coins WHERE username = :username");
        $stmt->bindValue(":coins", $amount, SQLITE3_INTEGER);
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $stmt->execute();
        $stmt->close();
    }

    public function resetCoins($player): void {
        $this->setCoins($player, 0);
    }

    public function getCoins($player): int {
        $username = $player->getName();
        $db = $this->databaseManager->getDatabase();
        $stmt = $db->prepare("SELECT coins FROM economy WHERE username = :username");
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        $stmt->close();
        return $result !== false && isset($result['coins']) ? (int)$result['coins'] : 0;
    }

    public function getTopCoins(int $limit = 10): array {
        $db = $this->databaseManager->getDatabase();
        $stmt = $db->prepare("SELECT username, coins FROM economy ORDER BY coins DESC LIMIT :limit");
        $stmt->bindValue(":limit", $limit, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $topCoins = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $topCoins[$row['username']] = (int)$row['coins'];
        }
        $stmt->close();
        return $topCoins;
    }
}
