<?php

namespace PrimaryCore\gangs\forms\menu;
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

class MenuGangForm {

    public function load(Player $player): MenuForm {

        return new MenuForm(
			TF::BOLD . "GANG MENU", /* title of the form */
			"Please choose an option", /* body text, shown above the menu options */
			[
				/* menu option with no icon */
				new MenuOption("Stats\n" . TF::ITALIC . TF::GRAY . "Tap for more..."),
				new MenuOption("Manage Members\n" . TF::ITALIC . TF::GRAY . "Tap for more..."),
				new MenuOption("Administrative\n" . TF::ITALIC . TF::GRAY . "Tap for more...")

			],
            function(Player $submitter, int $selected) : void{
             
            },
            function (Player $submitter): void {
                
            }
        );
    }
}
