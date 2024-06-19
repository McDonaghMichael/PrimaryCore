<?php

namespace PrimaryCore\tasks;

use pocketmine\entity\effect\VanillaEffects;
use pocketmine\scheduler\Task;
use pocketmine\world\Position;
use pocketmine\player\Player;
use PrimaryCore\Main;
use xenialdan\apibossbar\BossBar;
use PrimaryCore\database\UserManager;
use pocketmine\utils\TextFormat as TF;

class BossBarTask extends Task
{

    private $currentIndex;

    private array $messages = [
        TF::BOLD . TF::RED . "STORE " . TF::DARK_GRAY . ">> " . TF::GOLD . "Store.ReactiveMC.Net", 
        TF::BOLD . TF::GREEN . "VOTE " . TF::DARK_GRAY . ">> " . TF::YELLOW . "Vote.ReactiveMC.Net",
        TF::BOLD . TF::DARK_BLUE . "DISCORD " . TF::DARK_GRAY . ">> " . TF::AQUA . "Vote.ReactiveMC.Net",
        TF::BOLD . TF::LIGHT_PURPLE . "VOTE " . TF::DARK_GRAY . ">> " . TF::DARK_PURPLE . "Vote.ReactiveMC.Net",
    ];

    public function __construct()
    {
        $this->currentIndex = 0;
       
    }

    public function onRun(): void
    {
        $message = $this->messages[$this->currentIndex];
        foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $player) {
            
            
            if ($player instanceof Player) {
                $bar = Main::$bar;
                
                $bar->setPercentage(0.1);
                $bar->setTitle($message);
        $currentRank = Main::getInstance()->getUserManager()->getPlayerMineRank($player->getName());

                $nextRank = $currentRank + 1;
                if ($nextRank > UserManager::MINE_RANK_Z) {
                    $bar->setSubTitle('MAX');
                    
                }else{
                    $rankUpCost = Main::getInstance()->getUserManager()->getRankUpCost($nextRank);

                    
                    $currentCoins = Main::getInstance()->getEconomyManager()->getCoins($player);
                            
                    if ($rankUpCost <= 0) {
                        $bar->setPercentage(0.0); // To avoid division by zero
                    }else{
                        $percentage = $currentCoins / $rankUpCost;
                        $bar->setPercentage(min(max($percentage, 0), 1));
                    }
                    
                    
                    if(Main::getInstance()->getUserManager()->getPlayerMineRank($player->getName()) !== null){
                        $bar->setSubTitle(TF::AQUA . Main::getInstance()->getUserManager()->rankNumberToLetter(Main::getInstance()->getUserManager()->getPlayerMineRank($player->getName())) . TF::GRAY . " -> " . TF::AQUA . Main::getInstance()->getUserManager()->rankNumberToLetter($nextRank) . TF::GRAY . " [" . TF::BOLD . TF::GOLD . $rankUpCost . TF::RESET . TF::GRAY . "]" . TF::RESET . TF::DARK_GRAY . " | " . TF::AQUA . "Use /rankup");
                    }
                    
                }

       
                
            }

        }

        $this->currentIndex++;
        if ($this->currentIndex >= count($this->messages)) {
            $this->currentIndex = 0;
        }
    }
}
