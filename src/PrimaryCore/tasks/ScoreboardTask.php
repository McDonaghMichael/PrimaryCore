<?php

namespace PrimaryCore\tasks;

use pocketmine\entity\effect\VanillaEffects;
use pocketmine\scheduler\Task;
use pocketmine\world\Position;
use pocketmine\player\Player;
use PrimaryCore\Main;
use exodus\scoreboard\Scoreboard;


class ScoreboardTask extends Task
{

    private array $defaultScoreboard = [
        "Rank: %rank%",
        "Coins: %coins%"
    ];

    private array $mineScoreboard = [
        "Message 1",
        "Message 2",
        "Message 3"
    ];

    private array $cellScoreboard = [
        "Cell Data 1",
        "Cell Data 2"
    ];

    private array $solitaryScoreboard = [
        "Solitary Data 1",
        "Solitary Data 2"
    ];

    private array $pvpScoreboard = [
        "PvP Line 1",
        "PvP Line 2"
    ];

    public function __construct()
    {
       
    }

    public function onRun(): void
    {
        foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $player) {
            if ($player instanceof Player) {
                $scoreboard = Scoreboard::create($player, "ReactiveMC");
                $scoreboard->remove();
                $scoreboard->spawn();
                if(Main::getInstance()->getUserManager()->isScoreboardEnabled($player->getName())){
                         
                    foreach ($this->defaultScoreboard as $index => $line) {
                        $line = str_replace("%coins%", Main::getInstance()->getEconomyManager()->getCoins($player), $line);
                        $line = str_replace("%rank%", Main::getInstance()->getRankManager()->getRankForPlayer($player)->getFormat(), $line);
                        $scoreboard->setLine($index, $line);
                    }
                }else{

$scoreboard->remove();

                }
                
            }
        }

    }
}
