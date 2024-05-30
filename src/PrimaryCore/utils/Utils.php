<?php

namespace PrimaryCore\utils;

use pocketmine\player\Player;
use pocketmine\Server;
use PrimaryCore\Main;

class Utils {

    public static function broadcastMessage(string $message): void
    {
        $message = Main::getInstance()->getTranslateManager()->replaceColorPlaceholders($message);
        $message = Main::getInstance()->getTranslateManager()->replaceFormattingPlaceholders($message);

        Server::getInstance()->broadcastMessage($message);
    }
}
