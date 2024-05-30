<?php

namespace PrimaryCore\listeners\sub;

use PrimaryCore\Main;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\utils\Config;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;
use pocektmine\Player;
use pocketmine\player\chat\LegacyRawChatFormatter;
use function pocketmine\server;

class onChatListener implements Listener
{
    public function onPlayerChat(PlayerChatEvent $event): void {
        $player = $event->getPlayer();
        $message = $event->getMessage();
        $event->setFormatter(new LegacyRawChatFormatter(Main::getInstance()->getRankManager()->getChatFormat($player, $message)));
    }

}