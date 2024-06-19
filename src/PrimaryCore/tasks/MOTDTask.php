<?php

namespace PrimaryCore\tasks;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use PrimaryCore\Main;
use pocketmine\utils\TextFormat as TF;

class MOTDTask extends Task
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
        Main::getInstance()->getServer()->getNetwork()->setName($message);
        // Increment index for the next message, and loop back to the start if necessary
        $this->currentIndex++;
        if ($this->currentIndex >= count($this->messages)) {
            $this->currentIndex = 0;
        }
    }
}
