<?php

namespace PrimaryCore\gangs\forms\manage;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Label;
use dktapps\pmforms\element\Toggle;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use PrimaryCore\Main;

use PrimaryCore\gangs\forms\menu\MenuGangForm;
use PrimaryCore\gangs\forms\manage\ManageGangForm;

class ManageSelectedPlayerGangForm {

    public function load(Player $player, $selectedPlayer): CustomForm {

        return new CustomForm(
            TF::BOLD . "MANAGE " . $selectedPlayer,
            [
                new Label("this_is_a_label", "Change your gang settings here"),
                new Toggle("scoreboard", "Scoreboard", true),
                
            ],
            function (Player $submitter, CustomFormResponse $response): void {
               },
            function (Player $submitter): void {
                $manageForm = new ManageGangForm();
                $form = $manageForm->load($submitter);
                $submitter->sendForm($form);
            }
        );
    }
}
