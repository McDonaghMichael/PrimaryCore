<?php

namespace PrimaryCore\gangs;

use PrimaryCore\database\DatabaseManager;
use PrimaryCore\Main;
use PrimaryCore\gangs\commands\GangCommand;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;

class GangManager {
    
    private DatabaseManager $databaseManager;

    private array $invites = [];

    public function __construct() {
        $this->databaseManager = Main::getInstance()->getDatabaseManager();
        Main::getInstance()->getServer()->getCommandMap()->registerAll("PrimaryCore", [
            new GangCommand("gang", "Permet d'aller au spawn", "/gang"),
        ]);
    }

    public function invitePlayerToGang(Player $player, string $gangName): void {
        $username = $player->getName();
        $this->invites[$username] = [
            'gang' => $gangName,
            'expires' => time() + 30 
        ];
        $player->sendMessage(TF::YELLOW . "You have been invited to join the gang '$gangName'. Type '/gang accept' within 30 seconds to join.");
    }

    public function acceptGangInvite(string $username): bool {
        if (isset($this->invites[$username])) {
            $invite = $this->invites[$username];
            if (time() <= $invite['expires']) {
                $gangName = $invite['gang'];
                $this->addMemberToGang($username, $gangName);
                unset($this->invites[$username]);
                return true;
            } else {
                unset($this->invites[$username]);
            }
        }
        return false;
    }


    public function createGang(string $gangName, string $description = "", int $public = 0): void {
        $db = $this->databaseManager->getDatabase();
        $stmt = $db->prepare("INSERT INTO gangs (gang_name, description, public) VALUES (:gang_name, :description, :public)");
        $stmt->bindValue(":gang_name", $gangName, SQLITE3_TEXT);
        $stmt->bindValue(":description", $description, SQLITE3_TEXT);
        $stmt->bindValue(":public", $public, SQLITE3_INTEGER);
        $stmt->execute();
        $stmt->close();
    }

    public function gangExists(string $gangName): bool {
        $db = $this->databaseManager->getDatabase();
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM gangs WHERE gang_name = :gang_name");
        $stmt->bindValue(":gang_name", $gangName, SQLITE3_TEXT);
        $result = $stmt->execute();
        $count = $result->fetchArray(SQLITE3_ASSOC)['count'] ?? 0;
        $stmt->close();
        return $count > 0;
    }

    public function addMemberToGang(string $username, string $gangName): void {
        $db = $this->databaseManager->getDatabase();
        $stmt = $db->prepare("UPDATE players SET gang_name = :gang_name WHERE username = :username");
        $stmt->bindValue(":gang_name", $gangName, SQLITE3_TEXT);
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $stmt->execute();
        $stmt->close();
    }

    public function removeMemberFromGang(string $username): void {
        $db = $this->databaseManager->getDatabase();
        $stmt = $db->prepare("UPDATE players SET gang_name = NULL WHERE username = :username");
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $stmt->execute();
        $stmt->close();
    }

    public function promoteMember(string $username): void {
        // Implement the logic for promoting a member within the gang
    }

    public function demoteMember(string $username): void {
        // Implement the logic for demoting a member within the gang
    }

    public function getPlayersInGang(string $gangName): array {
        $db = $this->databaseManager->getDatabase();
        $stmt = $db->prepare("SELECT username FROM players WHERE gang_name = :gang_name");
        $stmt->bindValue(":gang_name", $gangName, SQLITE3_TEXT);
        $result = $stmt->execute();

        $players = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $players[] = $row['username'];
        }

        $stmt->close();
        return $players;
    }

    public function getReputation(string $gangName): int {
        $db = $this->databaseManager->getDatabase();
        $stmt = $db->prepare("SELECT reputation FROM gangs WHERE gang_name = :gang_name");
        $stmt->bindValue(":gang_name", $gangName, SQLITE3_TEXT);
        $result = $stmt->execute();
        $reputation = $result->fetchArray(SQLITE3_ASSOC)['reputation'] ?? 0;
        $stmt->close();
        return $reputation;
    }
    

    public function addReputation(string $gangName, int $amount): void {
        $db = $this->databaseManager->getDatabase();
        $stmt = $db->prepare("UPDATE gangs SET reputation = reputation + :amount WHERE gang_name = :gang_name");
        $stmt->bindValue(":amount", $amount, SQLITE3_INTEGER);
        $stmt->bindValue(":gang_name", $gangName, SQLITE3_TEXT);
        $stmt->execute();
        $stmt->close();
    }

    public function removeReputation(string $gangName, int $amount): void {
        $db = $this->databaseManager->getDatabase();
        $stmt = $db->prepare("UPDATE gangs SET reputation = reputation - :amount WHERE gang_name = :gang_name");
        $stmt->bindValue(":amount", $amount, SQLITE3_INTEGER);
        $stmt->bindValue(":gang_name", $gangName, SQLITE3_TEXT);
        $stmt->execute();
        $stmt->close();
    }

    public function setReputation(string $gangName, int $amount): void {
        $db = $this->databaseManager->getDatabase();
        $stmt = $db->prepare("UPDATE gangs SET reputation = :amount WHERE gang_name = :gang_name");
        $stmt->bindValue(":amount", $amount, SQLITE3_INTEGER);
        $stmt->bindValue(":gang_name", $gangName, SQLITE3_TEXT);
        $stmt->execute();
        $stmt->close();
    }

    public function resetReputation(string $gangName): void {
        $this->setReputation($gangName, 0);
    }

    public function deleteGang(string $gangName): void {
        $db = $this->databaseManager->getDatabase();
        $stmt = $db->prepare("DELETE FROM gangs WHERE gang_name = :gang_name");
        $stmt->bindValue(":gang_name", $gangName, SQLITE3_TEXT);
        $stmt->execute();
        $stmt->close();
        
        // Optionally, remove all members from this gang by setting their gang_name to NULL
        $stmt = $db->prepare("UPDATE players SET gang_name = NULL WHERE gang_name = :gang_name");
        $stmt->bindValue(":gang_name", $gangName, SQLITE3_TEXT);
        $stmt->execute();
        $stmt->close();
    }

    public function setLeader(string $gangName, string $leader): void {
        $db = $this->databaseManager->getDatabase();
        $stmt = $db->prepare("UPDATE gangs SET leader = :leader WHERE gang_name = :gang_name");
        $stmt->bindValue(":leader", $leader, SQLITE3_TEXT);
        $stmt->bindValue(":gang_name", $gangName, SQLITE3_TEXT);
        $stmt->execute();
        $stmt->close();

        $stmt = $db->prepare("UPDATE players SET gang_name = :gang WHERE username = :username");
        $stmt->bindValue(":username", $leader, SQLITE3_TEXT);
        $stmt->bindValue(":gang", $gangName, SQLITE3_TEXT);
        $stmt->execute();
        $stmt->close();
    }

    public function isGangLeader(string $username, string $gangName): bool {
        $db = $this->databaseManager->getDatabase();
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM gangs WHERE leader = :username AND gang_name = :gang_name");
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $stmt->bindValue(":gang_name", $gangName, SQLITE3_TEXT);
        $result = $stmt->execute();
        $count = $result->fetchArray(SQLITE3_ASSOC)['count'] ?? 0;
        $stmt->close();
        return $count > 0;
    }
    

    public function getPlayerGang(string $username): ?string {
        $db = $this->databaseManager->getDatabase();
        $stmt = $db->prepare("SELECT gang_name FROM players WHERE username = :username");
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        $stmt->close();
        return $row['gang_name'] ?? null;
    }

    
    public function isPlayerInGang(string $username): bool {
        $db = $this->databaseManager->getDatabase();
        $stmt = $db->prepare("SELECT gang_name FROM players WHERE username = :username");
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        $stmt->close();
        return !empty($row['gang_name']);
    }

    public function getGangMemberCount(string $gangName): int {
        $db = $this->databaseManager->getDatabase();
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM players WHERE gang_name = :gang_name");
        $stmt->bindValue(":gang_name", $gangName, SQLITE3_TEXT);
        $result = $stmt->execute();
        $count = $result->fetchArray(SQLITE3_ASSOC)['count'] ?? 0;
        $stmt->close();
        return $count;
    }
    
}
