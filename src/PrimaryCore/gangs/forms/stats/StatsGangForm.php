<?php

namespace PrimaryCore\gangs\forms\stats;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Label;
use dktapps\pmforms\element\Slider;
use dktapps\pmforms\element\StepSlider;
use dktapps\pmforms\element\Toggle;
use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use dktapps\pmforms\ModalForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use PrimaryCore\Main;

class StatsGangForm {

    public function load(Player $player): MenuForm {


        $gangName = Main::getInstance()->getGangManager()->getPlayerGang($player->getName());

        return new MenuForm(
			TF::BOLD . "STATISTICS",
			"Reputation: " . Main::getInstance()->getGangManager()->getReputation($gangName) .
			"\nCount: " . Main::getInstance()->getGangManager()->getGangMemberCount($gangName),
			[
				new MenuOption("Return\n" . TF::ITALIC . TF::GRAY . "Head to menu...")

			],
            function(Player $submitter, int $selected) : void{
             
            },
            function (Player $submitter): void {
                
            }
        );
    }
}
