<?php

namespace PrimaryCore\database;

use SQLite3;

class UserManager {
    
    private DatabaseManager $databaseManager;

    public const MINE_RANK_A = 0;

    public const MINE_RANK_B = 1;
    
    public const MINE_RANK_C = 2;
    public const MINE_RANK_D = 3;
    public const MINE_RANK_E = 4;
    public const MINE_RANK_Z = 25;

    private const RANK_UP_COSTS = [
        self::MINE_RANK_A => 0,
        self::MINE_RANK_B => 2000,
        self::MINE_RANK_C => 3000,
        self::MINE_RANK_D => 4000,
        self::MINE_RANK_E => 5000,
        // Define costs up to rank Z
        self::MINE_RANK_Z => 26000
    ];

    public function __construct(DatabaseManager $databaseManager) {
        $this->databaseManager = $databaseManager;
    }

    public function getRankUpCost(int $rank): int {
        return self::RANK_UP_COSTS[$rank] ?? 0;
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
        
        $stmt = $db->prepare("INSERT INTO settings (username, scoreboard, announcements) VALUES (:username, 1, 1)");
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $stmt->execute();
        $stmt->close();

        // Insert into the players table
        $stmt = $db->prepare("INSERT INTO players (username) VALUES (:username)");
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $stmt->execute();
        $stmt->close();

        $stmt = $db->prepare("INSERT INTO prison_ranks (username) VALUES (:username)");
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $stmt->execute();
        $stmt->close();
    }


    public function getPlayerSettings(string $username): array {
        $stmt = $this->databaseManager->getDatabase()->prepare("SELECT scoreboard, announcements FROM settings WHERE username = :username");
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $result = $stmt->execute();
        $settings = $result->fetchArray(SQLITE3_ASSOC);
        $stmt->close();
        return $settings !== false ? $settings : ['scoreboard' => true, 'announcements' => true];
    }

    public function isScoreboardEnabled(string $username): bool {
        $settings = $this->getPlayerSettings($username);
        return (bool)$settings['scoreboard'];
    }

    public function isAnnouncementsEnabled(string $username): bool {
        $settings = $this->getPlayerSettings($username);
        return (bool)$settings['announcements'];
    }

    public function updateScoreboardSetting(string $username, bool $enabled): void {
        $stmt = $this->databaseManager->getDatabase()->prepare("UPDATE settings SET scoreboard = :enabled WHERE username = :username");
        $stmt->bindValue(":enabled", $enabled ? 1 : 0, SQLITE3_INTEGER);
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $stmt->execute();
        $stmt->close();
    }

    public function updateAnnouncementsSetting(string $username, bool $enabled): void {
        $stmt = $this->databaseManager->getDatabase()->prepare("UPDATE settings SET announcements = :enabled WHERE username = :username");
        $stmt->bindValue(":enabled", $enabled ? 1 : 0, SQLITE3_INTEGER);
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $stmt->execute();
        $stmt->close();
    }

    public static function rankNumberToLetter(int $rankNumber): string {
        return chr(65 + $rankNumber); // Convert number to corresponding uppercase letter (A=0, B=1, ..., Z=25)
    }

    public function translatePrestigeLevel(int $prestigeLevel): string {
        $romanNumerals = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X'
            // Add more if necessary
        ];
        return $romanNumerals[$prestigeLevel] ?? (string)$prestigeLevel;
    }

    public function getPlayerMineRank(string $username): ?int {
        $db = $this->databaseManager->getDatabase();
        
        // Prepare and execute SQL query to fetch mine rank
        $stmt = $db->prepare("SELECT mine_rank FROM prison_ranks WHERE username = :username");
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $result = $stmt->execute();
    
        // Fetch the result row
        $row = $result->fetchArray(SQLITE3_ASSOC);
        $stmt->close();
    
        // Check if the row exists
        if ($row === false) {
            return null; // Username not found in the table
        } else {
            // Return the mine rank as an integer
            return (int)$row['mine_rank'];
        }
    }

    public function setPlayerMineRank(string $username, int $rank): void {
        $db = $this->databaseManager->getDatabase();
        $stmt = $db->prepare("UPDATE prison_ranks SET mine_rank = :rank WHERE username = :username");
        $stmt->bindValue(":rank", $rank, SQLITE3_INTEGER);
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $stmt->execute();
        $stmt->close();
    }
    
    public function getPlayerGangName(string $username): string {
        $db = $this->databaseManager->getDatabase();
        
        // Prepare and execute SQL query to fetch the gang name
        $stmt = $db->prepare("SELECT gang_name FROM players WHERE username = :username");
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $result = $stmt->execute();
    
        // Fetch the result row
        $row = $result->fetchArray(SQLITE3_ASSOC);
        $stmt->close();
    
        // Check if the row exists and return the gang name or an empty string
        return $row['gang_name'] ?? '';
    }
}
