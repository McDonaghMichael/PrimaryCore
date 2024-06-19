<?php

namespace PrimaryCore\gangs\forms\administrative\delete;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Label;
use dktapps\pmforms\element\Toggle;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use PrimaryCore\Main;

use PrimaryCore\gangs\forms\menu\MenuGangForm;

class DeleteGangForm {

    public function load(Player $player): CustomForm {

        return new CustomForm(
            TF::BOLD . "DELETE",
            [
                new Label("this_is_a_label", "DELETE someone to gang"),
                new Toggle("scoreboard", "Scoreboard", true),
                
            ],
            function (Player $submitter, CustomFormResponse $response): void {
               },
            function (Player $submitter): void {
                $menuForm = new MenuGangForm();
                $form = $menuForm->load($submitter);
                $submitter->sendForm($form);
            }
        );
    }
}