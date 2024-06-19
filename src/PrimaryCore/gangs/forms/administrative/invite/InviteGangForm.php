<?php

namespace PrimaryCore\gangs\forms\administrative\invite;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Label;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use PrimaryCore\Main;
use PrimaryCore\gangs\forms\menu\MenuGangForm;

class InviteGangForm {

    public function load(Player $player): CustomForm {
        return new CustomForm(
            TF::BOLD . "INVITE",
            [
                new Label("label", "Invite someone to your gang"),
                new Input("username", "Username", "Steve"),
            ],
            function (Player $submitter, CustomFormResponse $response): void {
                $username = $response->getString("username");
                $gangManager = Main::getInstance()->getGangManager();
                $gangName = $gangManager->getPlayerGang($submitter->getName());

                if ($gangName === null) {
                    $submitter->sendMessage(TF::RED . "You are not in a gang.");
                    return;
                }

                $targetPlayer = Main::getInstance()->getServer()->getPlayerExact($username);

                if ($targetPlayer === null) {
                    $submitter->sendMessage(TF::RED . "Player not found.");
                    return;
                }

                $gangManager->invitePlayerToGang($targetPlayer, $gangName);
                $submitter->sendMessage(TF::GREEN . "Invite sent to " . $username);
            },
            function (Player $submitter): void {
                $menuForm = new MenuGangForm();
                $form = $menuForm->load($submitter);
                $submitter->sendForm($form);
            }
        );
    }
}
