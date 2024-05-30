<?php

namespace PrimaryCore\database;

use SQLite3;

class UserManager {
    private DatabaseManager $databaseManager;

    public function __construct(DatabaseManager $databaseManager) {
        $this->databaseManager = $databaseManager;
    }

    public function playerExists(string $username): bool {
        $db = $this->databaseManager->getDatabase();
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM players WHERE username = :username");
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $result = $stmt->execute();
        $count = $result->fetchArray(SQLITE3_ASSOC)['count'] ?? 0;
        $stmt->close();
        return $count > 0;
    }

    public function insertPlayer(string $username): void {
        $db = $this->databaseManager->getDatabase();
        
        // Insert into the economy table
        $stmt = $db->prepare("INSERT INTO economy (username) VALUES (:username)");
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $stmt->execute();
        $stmt->close();
        
        // Insert into the players table
        $stmt = $db->prepare("INSERT INTO players (username) VALUES (:username)");
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $stmt->execute();
        $stmt->close();
    }
}
