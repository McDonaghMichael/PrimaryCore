<?php

namespace PrimaryCore\commands\sub;

use PrimaryCore\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\lang\Translatable;
use PrimaryCore\database\UserManager;
use PrimaryCore\utils\Utils;

class RankupCommand extends Command
{
    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission("global.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game.");
            return;
        }

        if (!$this->testPermission($sender)) {
            return;
        }

        $username = $sender->getName();
        $economyManager = Main::getInstance()->getEconomyManager();
        $userManager = Main::getInstance()->getUserManager();

        // Get the player's current rank
        $currentRank = $userManager->getPlayerMineRank($username);
        if ($currentRank === null) {
            $sender->sendMessage("Your rank could not be found.");
            return;
        }

        // Determine the next rank
        $nextRank = $currentRank + 1;
        if ($nextRank > UserManager::MINE_RANK_Z) {
            $sender->sendMessage("You have reached the maximum rank.");
            return;
        }

        // Get the cost to rank up to the next rank
        $rankUpCost = $userManager->getRankUpCost($nextRank);

        // Get the player's current coins
        $currentCoins = $economyManager->getCoins($sender);

        // Check if the player has enough coins to rank up
        if ($currentCoins < $rankUpCost) {
            $sender->sendMessage("You do not have enough coins to rank up. You need " . ($rankUpCost - $currentCoins) . " more coins.");
            return;
        }

        // Deduct the coins and update the player's rank
        $economyManager->removeCoins($sender, $rankUpCost);
        $userManager->setPlayerMineRank($username, $nextRank);

        Utils::broadcastMessage("Rankup");
        $sender->sendMessage("Congratulations! You have ranked up to " . UserManager::rankNumberToLetter($nextRank) . ".");
    }
}
