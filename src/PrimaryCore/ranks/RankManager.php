<?php

namespace PrimaryCore\ranks;

use PrimaryCore\Main;
use PrimaryCore\database\DatabaseManager;
use pocketmine\plugin\Plugin;
use pocketmine\Player\Player;

use pocketmine\utils\TextFormat as TF;
use PrimaryCore\ranks\commands\AddRankCommand;
use PrimaryCore\ranks\commands\RemoveRankCommand;
use PrimaryCore\ranks\commands\ResetRanksCommand;
use pocketmine\permission\PermissionAttachment;
use pocketmine\permission\PermissionManager;

class RankManager {

    const OWNER = 100000;
    const MANAGER = 999999;
    const ADMIN = 90;
    const HEAD_DEVELOPER = 80;
    const DEVELOPER = 70;
    const BUILDER = 60;
    const NITRO = 40;    
    const INMATE = 0;    

    /** @var Plugin */
    private $plugin;

    /** @var array */
    private $ranks = [];

    private $attachments = [];

    private $databaseManager;

    public function __construct(Plugin $plugin) {
        $this->plugin = $plugin;
        $this->databaseManager = Main::getInstance()->getDatabaseManager();
        $this->setupRanks();
        Main::getInstance()->getServer()->getCommandMap()->registerAll("PrimaryCore", [
            new AddRankCommand("addrank", "Permet d'aller au spawn", "/addrank"),
            new RemoveRankCommand("removerank", "Permet d'aller au spawn", "/removerank"),
            new ResetRanksCommand("resetrank", "Permet d'aller au spawn", "/resetranks"),
        ]);
    }

    private function setupRanks(): void {
        // Create and add ranks here
        $this->addRank(new Rank("Owner", RankManager::OWNER, "{GOLD}{BOLD}Owner", "> {rank} {username} - {msg}", "{RED}[Owner]", ["example.cmd", "ex.cmd"]));
        $this->addRank(new Rank("Admin", RankManager::ADMIN, "{GOLD}{BOLD}Admin", "> {rank} {username} - {msg}", "{GOLD}[Admin]", ["admin.cmd"]));
        $this->addRank(new Rank("Nitro", RankManager::NITRO, "{GOLD}{BOLD}Nitro", "> {rank} {username} - {msg}", "{GOLD}[Nitros]", ["admin.cmd"]));
        $this->addRank(new Rank("Developer", RankManager::DEVELOPER, "{GOLD}{BOLD}Dev", "> {rank} {username} - {msg}", "{GOLD}[Dev]", ["admin.cmd"]));
        $this->addRank(new Rank("Inmate", RankManager::INMATE, "{GOLD}{BOLD}INMATE", "{DARK_GRAY}> {WHITE}[{GRAY}{gang}{WHITE}] [{GRAY}{mine}{WHITE}][{GRAY}{prestige}{WHITE}] {rank} {username} - {msg}", "{GOLD}[INMATE]", ["admin.cmd"]));
        $this->addRank(new Rank("Manager", RankManager::MANAGER, "{BOLD}{DARK_PURPLE}MANAGER", "> {rank} {LIGHT_PURPLE){username} {GRAY}- {YELLOW}{msg}", "{BOLD}{DARK_PURPLE}[MANAGER]", ["admin.command"]));
        $this->addRank(new Rank("Builder", RankManager::BUILDER, "{GOLD}{BOLD}Builder", "> {rank} {username} - {msg}", "{GOLD}[Builder]", ["admin.cmd"]));
        $this->addRank(new Rank("Head Dev", RankManager::HEAD_DEVELOPER, "{GOLD}{BOLD}Head Dev", "> {rank} {username} - {msg}", "{GOLD}[HDEV]", ["admin.cmd"]));

    }

    public function loadPermissions(Player $player): void {
        $this->removePermissions($player);
        $ranks = $this->getRanksForPlayer($player);
        foreach ($ranks as $rank) {
            $permissions = $rank->getPermissions();
            if ($permissions !== null) {
                if (!isset($this->attachments[$player->getName()])) {
                    $this->attachments[$player->getName()] = $player->addAttachment(Main::getInstance());
                }
                $attachment = $this->attachments[$player->getName()];
                foreach ($permissions as $permission) {
                    $attachment->setPermission($permission, true);
                }
            }
        }
    }

    public function removePermissions(Player $player): void {
        if (isset($this->attachments[$player->getName()])) {
            $attachment = $this->attachments[$player->getName()];
            $ranks = $this->getRanksForPlayer($player);
            foreach ($ranks as $rank) {
                $permissions = $rank->getPermissions();
                if ($permissions !== null) {
                    foreach ($permissions as $permission) {
                        $attachment->unsetPermission($permission);
                    }
                }
            }
            $player->removeAttachment($attachment);
            unset($this->attachments[$player->getName()]);

        }
    }

    public function addRank(Rank $rank): void {
        $this->ranks[$rank->getId()] = $rank;
    }

    public function getRankById(int $id): ?Rank {
        return $this->ranks[$id] ?? null;
    }

    public function getAllRanks(): array {
        return $this->ranks;
    }

    public function getChatFormat($player, String $message): string {
        $rank = $this->getRankForPlayer($player);
        if ($rank === null) {
            return "";
        }
    
        $format = $rank->getChatFormat();
        $format = str_replace("{rank}", $rank->getFormat(), $format);
        $format = str_replace("{username}", $player->getName(), $format);
        $format = str_replace("{msg}", $message, $format);
        $format = str_replace("{mine}", Main::getInstance()->getUserManager()->rankNumberToLetter(Main::getInstance()->getUserManager()->getPlayerMineRank($player->getName())), $format);
        $format = str_replace("{gang}", Main::getInstance()->getUserManager()->getPlayerGangName($player->getName()), $format);
        $format = str_replace("{prestige}", "1", $format);
        return $format;
    }

    public function addRankToUser($player, int $rankId): void {
        $username = $player->getName();
        $db = $this->databaseManager->getDatabase();
    
        // Begin a transaction
        $db->exec("BEGIN TRANSACTION");
    
        try {
            // Insert or replace the user's rank in the server_ranks table
            $stmt = $db->prepare("INSERT OR REPLACE INTO server_ranks (username, rank) VALUES (:username, :rankId)");
            $stmt->bindValue(":username", $username, SQLITE3_TEXT);
            $stmt->bindValue(":rankId", $rankId, SQLITE3_INTEGER);
            $stmt->execute();
            $stmt->close();
    
            // Update the user's server_Rank in the players table
            $stmt = $db->prepare("UPDATE players SET server_Rank = :rankId WHERE username = :username");
            $stmt->bindValue(":rankId", $rankId, SQLITE3_INTEGER);
            $stmt->bindValue(":username", $username, SQLITE3_TEXT);
            $stmt->execute();
            $stmt->close();
    
            // Commit the transaction
            $db->exec("COMMIT");
        } catch (\Throwable $e) {
            // Rollback the transaction if an error occurs
            $db->exec("ROLLBACK");
            throw $e; // Rethrow the exception for handling by the caller
        }
    }
    
    public function removeRankFromUser($player, int $rankId): void {
        // Determine the username based on the type of $player parameter
        if (is_string($player)) {
            $username = $player;
        } elseif ($player instanceof Player) {
            $username = $player->getName();
        } else {
            throw new \InvalidArgumentException('Parameter $player must be either a string or an instance of Player');
        }
    
        $db = $this->databaseManager->getDatabase();
    
        // Begin a transaction
        $db->exec("BEGIN TRANSACTION");
    
        try {
            // Delete the rank from the server_ranks table
            $stmt = $db->prepare("DELETE FROM server_ranks WHERE username = :username AND rank = :rankId");
            $stmt->bindValue(":username", $username, SQLITE3_TEXT);
            $stmt->bindValue(":rankId", $rankId, SQLITE3_INTEGER);
            $stmt->execute();
            $stmt->close();
    
            // Check if the removed rank was the user's current server rank
            $stmt = $db->prepare("SELECT server_Rank FROM players WHERE username = :username");
            $stmt->bindValue(":username", $username, SQLITE3_TEXT);
            $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
            $currentRank = $result !== false && isset($result['server_Rank']) ? (int)$result['server_Rank'] : null;
            $stmt->close();
    
            if ($currentRank === $rankId) {
                // Get the next highest rank
                $stmt = $db->prepare("SELECT MAX(rank) FROM server_ranks WHERE username = :username");
                $stmt->bindValue(":username", $username, SQLITE3_TEXT);
                $result = $stmt->execute();
                $maxRank = 0; // Default value if no rank is found
    
                while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                    $maxRank = (int)$row['MAX(rank)'];
                }
    
                $stmt->close();
    
                // Set the next rank to the maximum rank found or 0 if no rank is found
                $nextRank = $maxRank > 0 ? $maxRank : 0;
    
                // Update the user's server_Rank to the next highest rank or 0 if no next rank
                $stmt = $db->prepare("UPDATE players SET server_Rank = :nextRank WHERE username = :username");
                $stmt->bindValue(":nextRank", $nextRank, SQLITE3_INTEGER);
                $stmt->bindValue(":username", $username, SQLITE3_TEXT);
                $stmt->execute();
                $stmt->close();
            }
    
            // Commit the transaction
            $db->exec("COMMIT");
        } catch (\Throwable $e) {
            // Rollback the transaction if an error occurs
            $db->exec("ROLLBACK");
            throw $e; // Rethrow the exception for handling by the caller
        }
    }
    
    
    

    public function getRankForPlayer($player): ?Rank {
        // Get the player's username
        $username = $player->getName();
    
        // Query the database to get the server rank for the player
        $db = $this->databaseManager->getDatabase();
        $stmt = $db->prepare("SELECT server_Rank FROM players WHERE username = :username");
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        $stmt->close();
    
        // If the player has a server rank, return the corresponding Rank object
        if ($result !== false && isset($result['server_Rank'])) {
            // Assume that the rank IDs in the database correspond to the same as the RankManager constants
            return $this->getRankById((int)$result['server_Rank']);
        }
    
        // If the player has no server rank or not found in the database, return the default rank "Inmate"
        return $this->getRankById(self::INMATE);
    }
    
    public function getRanksForPlayer($player): array {
        $username = $player->getName();
        $db = $this->databaseManager->getDatabase();
    
        // Query to get all ranks associated with the player
        $stmt = $db->prepare("SELECT rank FROM server_ranks WHERE username = :username");
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $result = $stmt->execute();
    
        $ranks = [];
    
        // Fetch all rank IDs and get corresponding Rank objects
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            if (isset($row['rank'])) {
                $rank = $this->getRankById((int)$row['rank']);
                if ($rank !== null) {
                    $ranks[] = $rank;
                }
            }
        }
        $stmt->close();
    
        return $ranks;
    }
    
    
}
