<?php

namespace PrimaryCore\gangs\commands;

use PrimaryCore\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use PrimaryCore\gangs\forms\create\CreateGangForm;
use PrimaryCore\gangs\forms\menu\MenuGangForm;
use PrimaryCore\gangs\forms\administrative\invite\InviteGangForm;

class GangCommand extends Command
{
    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission("global.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game.");
            return;
        }

        $player = $sender->getName();
        $gangManager = Main::getInstance()->getGangManager();

        if (empty($args)) {
            $sender->sendMessage("Usage: /gang <invite|accept|other_subcommands>");
            return;
        }

        switch ($args[0]) {

            case "accept":
                if ($gangManager->acceptGangInvite($player)) {
                    $sender->sendMessage("You have successfully joined the gang.");
                } else {
                    $sender->sendMessage("You don't have any pending gang invites or the invite has expired.");
                }
                break;

            default:
                if ($gangManager->isPlayerInGang($player)) {
                    $gangForm = new MenuGangForm();
                    $form = $gangForm->load($sender);
                    $sender->sendForm($form);
                } else {
                    $gangForm = new CreateGangForm();
                    $form = $gangForm->load($sender);
                    $sender->sendForm($form);
                }
                break;
        }
    }
}
