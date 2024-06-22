<?php

namespace PrimaryCore\staff;

use PrimaryCore\Main;
use pocketmine\player\Player;

class StaffManager {

    /** @var mixed */
    private $databaseManager;

    private $staffChatMembers = [];

    public function __construct() {
        $this->databaseManager = Main::getInstance()->getDatabaseManager();

        $commands = ["ban", "kick", "tempban"];
        foreach ($commands as $command) {
            $cmd = Main::getInstance()->getServer()->getCommandMap()->getCommand($command);
            if ($cmd !== null) {
                Main::getInstance()->getServer()->getCommandMap()->unregister($cmd);
            }
        }

        // Register new commands
        Main::getInstance()->getServer()->getCommandMap()->registerAll("PrimaryCore", [
            new \PrimaryCore\staff\commands\BanCommand("ban", "ban a player", "/ban"),
            new \PrimaryCore\staff\commands\KickCommand("kick", "kick a player", "/kick"),
            new \PrimaryCore\staff\commands\TempBanCommand("tempban", "temporarily ban a player", "/tempban"),
            new \PrimaryCore\staff\commands\UnbanCommand("unban", "unban a player", "/unban"),
            new \PrimaryCore\staff\commands\TempMuteCommand("tempmute", "tempmute a player", "/tempmute"),
            new \PrimaryCore\staff\commands\MuteCommand("mute", "mute a player", "/mute"),
            new \PrimaryCore\staff\commands\UnmuteCommand("unmute", "unmute a player", "/unmute"),
            new \PrimaryCore\staff\commands\StaffChatCommand("staffchat", "staffchat a player", "/sc"),
        ]);
    }

    public function addTemporaryBan(string $username, int $length, string $reason, string $bannedBy): void {
        $timeSinceBanned = time();
        $db = $this->databaseManager->getDatabase();

        $db->exec("BEGIN TRANSACTION");
        try {
            $stmt = $db->prepare(
                "INSERT INTO temporary_bans (username, length, time_since_banned, reason, banned_by)
                VALUES (:username, :length, :time_since_banned, :reason, :banned_by)"
            );
            $stmt->bindValue(":username", $username, SQLITE3_TEXT);
            $stmt->bindValue(":length", $length, SQLITE3_INTEGER);
            $stmt->bindValue(":time_since_banned", $timeSinceBanned, SQLITE3_INTEGER);
            $stmt->bindValue(":reason", $reason, SQLITE3_TEXT);
            $stmt->bindValue(":banned_by", $bannedBy, SQLITE3_TEXT);
            $stmt->execute();
            $stmt->close();

            $db->exec("COMMIT");
        } catch (\Throwable $e) {
            $db->exec("ROLLBACK");
            Main::getInstance()->getLogger()->error("Failed to add temporary ban: " . $e->getMessage());
            throw $e;
        }
    }

    public function addPermanentBan(string $username, string $reason, string $bannedBy): void {
        $timeSinceBanned = time();
        $db = $this->databaseManager->getDatabase();

        $db->exec("BEGIN TRANSACTION");
        try {
            $stmt = $db->prepare(
                "INSERT INTO permanent_bans (username, time_since_banned, reason, banned_by)
                VALUES (:username, :time_since_banned, :reason, :banned_by)"
            );
            $stmt->bindValue(":username", $username, SQLITE3_TEXT);
            $stmt->bindValue(":time_since_banned", $timeSinceBanned, SQLITE3_INTEGER);
            $stmt->bindValue(":reason", $reason, SQLITE3_TEXT);
            $stmt->bindValue(":banned_by", $bannedBy, SQLITE3_TEXT);
            $stmt->execute();
            $stmt->close();

            $db->exec("COMMIT");
        } catch (\Throwable $e) {
            $db->exec("ROLLBACK");
            Main::getInstance()->getLogger()->error("Failed to add permanent ban: " . $e->getMessage());
            throw $e;
        }
    }

    public function removeBan(string $username): void {
        $db = $this->databaseManager->getDatabase();

        $db->exec("BEGIN TRANSACTION");
        try {
            $stmtTemp = $db->prepare("DELETE FROM temporary_bans WHERE username = :username");
            $stmtTemp->bindValue(":username", $username, SQLITE3_TEXT);
            $stmtTemp->execute();
            $stmtTemp->close();

            $stmtPerm = $db->prepare("DELETE FROM permanent_bans WHERE username = :username");
            $stmtPerm->bindValue(":username", $username, SQLITE3_TEXT);
            $stmtPerm->execute();
            $stmtPerm->close();

            $db->exec("COMMIT");
        } catch (\Throwable $e) {
            $db->exec("ROLLBACK");
            Main::getInstance()->getLogger()->error("Failed to remove ban: " . $e->getMessage());
            throw $e;
        }
    }

    public function isPlayerBanned(string $username): bool {
        $db = $this->databaseManager->getDatabase();

        // Check temporary ban
        $stmtTemp = $db->prepare("SELECT * FROM temporary_bans WHERE username = :username");
        $stmtTemp->bindValue(":username", $username, SQLITE3_TEXT);
        $resultTemp = $stmtTemp->execute();
        $tempBan = $resultTemp->fetchArray(SQLITE3_ASSOC);
        $stmtTemp->close();

        if ($tempBan) {
            $currentTime = time();
            $banEndTime = $tempBan['time_since_banned'] + $tempBan['length'];

            if ($currentTime < $banEndTime) {
                return true;
            } else {
                $this->removeBan($username); // Automatically remove expired ban
            }
        }

        // Check permanent ban
        $stmtPerm = $db->prepare("SELECT * FROM permanent_bans WHERE username = :username");
        $stmtPerm->bindValue(":username", $username, SQLITE3_TEXT);
        $resultPerm = $stmtPerm->execute();
        $permBan = $resultPerm->fetchArray(SQLITE3_ASSOC);
        $stmtPerm->close();

        return $permBan !== false;
    }

    public function getBanReason(string $username): ?string {
        $db = $this->databaseManager->getDatabase();

        // Check temporary ban reason
        $stmtTemp = $db->prepare("SELECT reason FROM temporary_bans WHERE username = :username");
        $stmtTemp->bindValue(":username", $username, SQLITE3_TEXT);
        $resultTemp = $stmtTemp->execute();
        $tempBan = $resultTemp->fetchArray(SQLITE3_ASSOC);
        $stmtTemp->close();

        if ($tempBan) {
            return $tempBan['reason'];
        }

        // Check permanent ban reason
        $stmtPerm = $db->prepare("SELECT reason FROM permanent_bans WHERE username = :username");
        $stmtPerm->bindValue(":username", $username, SQLITE3_TEXT);
        $resultPerm = $stmtPerm->execute();
        $permBan = $resultPerm->fetchArray(SQLITE3_ASSOC);
        $stmtPerm->close();

        if ($permBan) {
            return $permBan['reason'];
        }

        return null;
    }

    public function getBanDuration($username): string
    {
        $db = $this->databaseManager->getDatabase();
        $stmt = $db->prepare("SELECT length, time_since_banned FROM temporary_bans WHERE username = :username");
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $result = $stmt->execute();
        $ban = $result->fetchArray(SQLITE3_ASSOC);
        $stmt->close();

        if (!$ban) {
            return "Permanent";
        }

        $banEndTime = $ban['time_since_banned'] + $ban['length'];
        $remainingTime = $banEndTime - time();

        if ($remainingTime <= 0) {
            // Ban expired
            $this->removeBan($username);
            return "Ban Expired";
        }

        // Calculate remaining days, hours, and minutes
        $days = floor($remainingTime / 86400);
        $hours = floor(($remainingTime % 86400) / 3600);
        $minutes = floor(($remainingTime % 3600) / 60);

        return sprintf("%dD %dH %dM", $days, $hours, $minutes);
    }

    public function addTemporaryMute(string $username, int $length, string $reason, string $mutedBy): void {
        $timeSinceMuted = time();
        $db = $this->databaseManager->getDatabase();

        $db->exec("BEGIN TRANSACTION");
        try {
            $stmt = $db->prepare(
                "INSERT INTO temporary_mutes (username, length, time_since_muted, reason, muted_by)
                VALUES (:username, :length, :time_since_muted, :reason, :muted_by)"
            );
            $stmt->bindValue(":username", $username, SQLITE3_TEXT);
            $stmt->bindValue(":length", $length, SQLITE3_INTEGER);
            $stmt->bindValue(":time_since_muted", $timeSinceMuted, SQLITE3_INTEGER);
            $stmt->bindValue(":reason", $reason, SQLITE3_TEXT);
            $stmt->bindValue(":muted_by", $mutedBy, SQLITE3_TEXT);
            $stmt->execute();
            $stmt->close();

            $db->exec("COMMIT");
        } catch (\Throwable $e) {
            $db->exec("ROLLBACK");
            Main::getInstance()->getLogger()->error("Failed to add temporary mute: " . $e->getMessage());
            throw $e;
        }
    }

    public function addPermanentMute(string $username, string $reason, string $mutedBy): void {
        $timeSinceMuted = time();
        $db = $this->databaseManager->getDatabase();

        $db->exec("BEGIN TRANSACTION");
        try {
            $stmt = $db->prepare(
                "INSERT INTO permanent_mutes (username, time_since_muted, reason, muted_by)
                VALUES (:username, :time_since_muted, :reason, :muted_by)"
            );
            $stmt->bindValue(":username", $username, SQLITE3_TEXT);
            $stmt->bindValue(":time_since_muted", $timeSinceMuted, SQLITE3_INTEGER);
            $stmt->bindValue(":reason", $reason, SQLITE3_TEXT);
            $stmt->bindValue(":muted_by", $mutedBy, SQLITE3_TEXT);
            $stmt->execute();
            $stmt->close();

            $db->exec("COMMIT");
        } catch (\Throwable $e) {
            $db->exec("ROLLBACK");
            Main::getInstance()->getLogger()->error("Failed to add permanent mute: " . $e->getMessage());
            throw $e;
        }
    }

    public function removeMute(string $username): void {
        $db = $this->databaseManager->getDatabase();

        $db->exec("BEGIN TRANSACTION");
        try {
            $stmtTemp = $db->prepare("DELETE FROM temporary_mutes WHERE username = :username");
            $stmtTemp->bindValue(":username", $username, SQLITE3_TEXT);
            $stmtTemp->execute();
            $stmtTemp->close();

            $stmtPerm = $db->prepare("DELETE FROM permanent_mutes WHERE username = :username");
            $stmtPerm->bindValue(":username", $username, SQLITE3_TEXT);
            $stmtPerm->execute();
            $stmtPerm->close();

            $db->exec("COMMIT");
        } catch (\Throwable $e) {
            $db->exec("ROLLBACK");
            Main::getInstance()->getLogger()->error("Failed to remove mute: " . $e->getMessage());
            throw $e;
        }
    }

    public function isPlayerMuted(string $username): bool {
        $db = $this->databaseManager->getDatabase();

        // Check temporary mute
        $stmtTemp = $db->prepare("SELECT * FROM temporary_mutes WHERE username = :username");
        $stmtTemp->bindValue(":username", $username, SQLITE3_TEXT);
        $resultTemp = $stmtTemp->execute();
        $tempMute = $resultTemp->fetchArray(SQLITE3_ASSOC);
        $stmtTemp->close();

        if ($tempMute) {
            $currentTime = time();
            $muteEndTime = $tempMute['time_since_muted'] + $tempMute['length'];

            if ($currentTime < $muteEndTime) {
                return true;
            } else {
                $this->removeMute($username); // Automatically remove expired mute
            }
        }

        // Check permanent mute
        $stmtPerm = $db->prepare("SELECT * FROM permanent_mutes WHERE username = :username");
        $stmtPerm->bindValue(":username", $username, SQLITE3_TEXT);
        $resultPerm = $stmtPerm->execute();
        $permMute = $resultPerm->fetchArray(SQLITE3_ASSOC);
        $stmtPerm->close();

        return $permMute !== false;
    }

    public function getMuteReason(string $username): ?string {
        $db = $this->databaseManager->getDatabase();

        // Check temporary mute reason
        $stmtTemp = $db->prepare("SELECT reason FROM temporary_mutes WHERE username = :username");
        $stmtTemp->bindValue(":username", $username, SQLITE3_TEXT);
        $resultTemp = $stmtTemp->execute();
        $tempMute = $resultTemp->fetchArray(SQLITE3_ASSOC);
        $stmtTemp->close();

        if ($tempMute) {
            return $tempMute['reason'];
        }

        // Check permanent mute reason
        $stmtPerm = $db->prepare("SELECT reason FROM permanent_mutes WHERE username = :username");
        $stmtPerm->bindValue(":username", $username, SQLITE3_TEXT);
        $resultPerm = $stmtPerm->execute();
        $permMute = $resultPerm->fetchArray(SQLITE3_ASSOC);
        $stmtPerm->close();

        if ($permMute) {
            return $permMute['reason'];
        }

        return null;
    }

    public function getMuteDuration($username): string
    {
        $db = $this->databaseManager->getDatabase();
        $stmt = $db->prepare("SELECT length, time_since_muted FROM temporary_mutes WHERE username = :username");
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $result = $stmt->execute();
        $mute = $result->fetchArray(SQLITE3_ASSOC);
        $stmt->close();

        if (!$mute) {
            return "Permanent";
        }

        $muteEndTime = $mute['time_since_muted'] + $mute['length'];
        $remainingTime = $muteEndTime - time();

        if ($remainingTime <= 0) {
            // Mute expired
            $this->removeMute($username);
            return "Mute Expired";
        }

        // Calculate remaining days, hours, and minutes
        $days = floor($remainingTime / 86400);
        $hours = floor(($remainingTime % 86400) / 3600);
        $minutes = floor(($remainingTime % 3600) / 60);

        return sprintf("%dD %dH %dM", $days, $hours, $minutes);
    }

    public function addToStaffChat(Player $player): bool {
        $username = $player->getName();
        if (!$this->isInStaffChat($player)) {
            $this->staffChatMembers[$username] = $player;
            return true;
        }
        return false;
    }

    /**
     * Remove a player from the staff chat.
     *
     * @param Player $player
     * @return bool True if successfully removed, false if not found.
     */
    public function removeFromStaffChat(Player $player): bool {
        $username = $player->getName();
        if ($this->isInStaffChat($player)) {
            unset($this->staffChatMembers[$username]);
            return true;
        }
        return false;
    }

    /**
     * Check if a player is currently in the staff chat.
     *
     * @param Player $player
     * @return bool
     */
    public function isInStaffChat(Player $player): bool {
        $username = $player->getName();
        return isset($this->staffChatMembers[$username]);
    }

    /**
     * Get all players currently in the staff chat.
     *
     * @return Player[]
     */
    public function getStaffChatMembers(): array {
        return $this->staffChatMembers;
    }

    /**
     * Clear all players from the staff chat.
     */
    public function clearStaffChat(): void {
        $this->staffChatMembers = [];
    }
}
