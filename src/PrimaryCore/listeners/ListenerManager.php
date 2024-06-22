<?php
namespace PrimaryCore\listeners;

use pocketmine\plugin\Plugin;
use PrimaryCore\listeners\sub\onJoinListener;
use PrimaryCore\listeners\sub\onChatListener;
use PrimaryCore\listeners\sub\onLoginListener;
use PrimaryCore\Main;

class ListenerManager{

	/** @var Plugin */
    private $plugin;

    public function __construct(Plugin $plugin) {
        $this->plugin = $plugin;
        Main::getInstance()->getServer()->getPluginManager()->registerEvents(new onJoinListener(), Main::getInstance());
        Main::getInstance()->getServer()->getPluginManager()->registerEvents(new onChatListener(), Main::getInstance());
        Main::getInstance()->getServer()->getPluginManager()->registerEvents(new onLoginListener(), Main::getInstance());
    }
}