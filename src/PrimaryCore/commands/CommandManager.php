<?php
namespace PrimaryCore\commands;

use pocketmine\plugin\Plugin;
use PrimaryCore\Main;
use PrimaryCore\commands\sub\ExampleCommand;
use PrimaryCore\commands\sub\KitCommand;

class CommandManager{

	/** @var Plugin */
    private $plugin;

    public function __construct(Plugin $plugin) {
        $this->plugin = $plugin;
            $commands = ["kill", "about", "pl", "tell", "me", "help", "clear", "checkperm"];
            foreach ($commands as $command) {
                Main::getInstance()->getServer()->getCommandMap()->unregister(Main::getInstance()->getServer()->getCommandMap()->getCommand($command));
            }
    
            Main::getInstance()->getServer()->getCommandMap()->registerAll("PrimaryCore", [
                new ExampleCommand("example", "Permet d'aller au spawn", "/spawn"),
                new KitCommand("kit", "Permet d'aller au spawn", "/kit"),
            ]);
        
    }
}