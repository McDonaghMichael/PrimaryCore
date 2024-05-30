<?php
namespace PrimaryCore\mines;

use pocketmine\plugin\Plugin;
use PrimaryCore\database\UserManager;
class MinesManager {

    private $mines = [];
    private $prestigeMines = [];
    private $rankRequiredMines = [];


    public function __construct() {
        $this->initializeMines();
    }

    // Method to initialize mines
    private function initializeMines() {
        // Instead of initializing mines as arrays, now we'll create Mine objects
        $this->addMine(new Mine(
            1,
            'A',
            'world1',
            ['x' => 100, 'y' => 50, 'z' => 200],
            [UserManager::MINE_RANK_A]
        ));

        $this->addMine(new Mine(
            2,
            'B',
            'world1',
            ['x' => 150, 'y' => 60, 'z' => 250],
            [UserManager::MINE_RANK_B]
        ));
        // Add more mines as needed
    }

    // Method to add a mine
    public function addMine(Mine $mine) {
        // Add the mine to the appropriate array based on type
        $this->mines[] = $mine;
    }

    // Method to get all mines
    public function getMines(): array {
        return $this->mines;
    }

    // Method to get prestige mines
    public function getPrestigeMines(): array {
        return $this->prestigeMines;
    }

    // Method to get rank required mines
    public function getRankRequiredMines(): array {
        return $this->rankRequiredMines;
    }
}
