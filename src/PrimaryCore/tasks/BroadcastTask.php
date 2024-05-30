<?php

namespace PrimaryCore\tasks;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use PrimaryCore\Main;
use pocketmine\utils\TextFormat as TF;

class BroadcastTask extends Task
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
                if(Main::getInstance()->getUserManager()->isAnnouncementsEnabled($player->getName())){
                $player->sendMessage($message);
                }
            }
        }
        // Increment index for the next message, and loop back to the start if necessary
        $this->currentIndex++;
        if ($this->currentIndex >= count($this->messages)) {
            $this->currentIndex = 0;
        }
    }
}
