<?php

namespace PrimaryCore\gangs\forms\create;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Label;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Toggle;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use PrimaryCore\Main;

class CreateGangForm {

    public function load(Player $player): CustomForm {

        return new CustomForm(
            TF::BOLD . "CREATE GANG",
            [
                new Label("this_is_a_label", "Aelcome"),
                new Input("name", "Enter gang name below!", "Empire"),
            ],
            function (Player $submitter, CustomFormResponse $response): void {
                
                Main::getInstance()->getGangManager()->createGang($response->getString("name"));
                Main::getInstance()->getGangManager()->setLeader($response->getString("name"), $submitter->getName());
                $submitter->sendMessage("Gang made");
            },
            function (Player $submitter): void {
                
            }
        );
    }
}
