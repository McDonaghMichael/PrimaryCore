<?php
namespace PrimaryCore\staff;

use pocketmine\plugin\Plugin;
use PrimaryCore\staff\sub\commands\BanCommand;
use PrimaryCore\Main;

class StaffManager{

	/** @var Plugin */
    private $plugin;

    public function __construct(Plugin $plugin) {
        $this->plugin = $plugin;
        $commands = ["ban"];
            foreach ($commands as $command) {
                Main::getInstance()->getServer()->getCommandMap()->unregister(Main::getInstance()->getServer()->getCommandMap()->getCommand($command));
            }
    
            Main::getInstance()->getServer()->getCommandMap()->registerAll("PrimaryCore", [
                new BanCommand("ban", "bann", "/ban"),
            ]);
    }
}