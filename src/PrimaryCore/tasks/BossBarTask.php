<?php

namespace PrimaryCore\tasks;

use pocketmine\entity\effect\VanillaEffects;
use pocketmine\scheduler\Task;
use pocketmine\world\Position;
use pocketmine\player\Player;
use PrimaryCore\Main;
use xenialdan\apibossbar\BossBar;
use PrimaryCore\database\UserManager;

class BossBarTask extends Task
{

    private $currentIndex;

    private array $messages = [
        "3ewfd", 
        "£EWFDF"
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
                        $bar->setSubTitle(Main::getInstance()->getUserManager()->rankNumberToLetter(Main::getInstance()->getUserManager()->getPlayerMineRank($player->getName())) . " -> " . Main::getInstance()->getUserManager()->rankNumberToLetter($nextRank) . " [" . $rankUpCost . "]");
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
