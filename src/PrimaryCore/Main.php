<?php

namespace PrimaryCore;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use PrimaryCore\commands\CommandManager;
use PrimaryCore\listeners\ListenerManager;
use PrimaryCore\ranks\RankManager;
use PrimaryCore\staff\StaffManager;
use PrimaryCore\kits\KitsManager;
use PrimaryCore\utils\translate\TranslateManager;
use PrimaryCore\database\DatabaseManager;
use PrimaryCore\database\UserManager;
use PrimaryCore\economy\EconomyManager;
use PrimaryCore\tasks\ScoreboardTask;
use PrimaryCore\tasks\BroadcastTask;

class Main extends PluginBase implements Listener {

    /** @var self */
    private static $instance;

    /** @var CommandManager */
    private $commandManager;

    /** @var ListenerManager */
    private $listenerManager;

    /** @var StaffManager */
    private $staffManager;

    /** @var RankManager */
    private $rankManager;

    /** @var TranslateManager */
    private $translateManager;

    /** @var KitsManager */
    private $kitsManager;

    /** @var DatabaseManager */
    private $databaseManager;

    /** @var UserManager */
    private $userManager;

    private $economyManager;

    public function onLoad(): void {
        self::$instance = $this;
    }

    public function onEnable(): void {
        $dbPath = $this->getDataFolder() . "database.db";
        $this->databaseManager = new DatabaseManager($dbPath);
        $this->commandManager = new CommandManager($this);
        $this->listenerManager = new ListenerManager($this);
        $this->staffManager = new StaffManager($this);
        $this->rankManager = new RankManager($this);
        $this->translateManager = new TranslateManager();
        $this->kitsManager = new KitsManager();
        $this->economyManager = new EconomyManager();
        $this->userManager = new UserManager($this->databaseManager);
        $this->loadTasks();
    }

    public static function getInstance(): ?Main {
        return self::$instance;
    }

    public function getCommandManager(): CommandManager {
        return $this->commandManager;
    }

    public function getListenerManager(): ListenerManager {
        return $this->listenerManager;
    }

    public function getStaffManager(): StaffManager {
        return $this->staffManager;
    }

    public function getRankManager(): RankManager {
        return $this->rankManager;
    }
    
    public function getTranslateManager(): TranslateManager {
        return $this->translateManager;
    }

    public function getKitsManager(): KitsManager {
        return $this->kitsManager;
    }

    public function getDatabaseManager(): DatabaseManager {
        return $this->databaseManager;
    }

    public function getUserManager(): UserManager {
        return $this->userManager;
    }

    public function getEconomyManager(): EconomyManager {
        return $this->economyManager;
    }

    public function loadTasks(): void {
        Main::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new BroadcastTask(), 20, 20);
        Main::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new ScoreboardTask(), 20, 20);


    }
}
