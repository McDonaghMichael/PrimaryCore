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

use PrimaryCore\gangs\forms\manage\ManageGangForm;
use PrimaryCore\gangs\forms\stats\StatsGangForm;
use PrimaryCore\gangs\forms\administrative\AdministrativeGangForm;

class MenuGangForm {

    public function load(Player $player): MenuForm {

        return new MenuForm(
			TF::BOLD . "GANG MENU",
			"Please choose an option",
			[
				new MenuOption("Stats\n" . TF::ITALIC . TF::DARK_GRAY . "Tap for more..."),
				new MenuOption("Manage Members\n" . TF::ITALIC . TF::DARK_GRAY . "Tap for more..."),
				new MenuOption("Administrative\n" . TF::ITALIC . TF::DARK_GRAY . "Tap for more...")

			],
            function(Player $submitter, int $selected) : void{
                switch($selected){
                    case 0:
                        $gangForm = new StatsGangForm();
                        $form = $gangForm->load($submitter);
                        $submitter->sendForm($form);
                        break;
                        case 1:
                            $manageForm = new ManageGangForm();
                            $form = $manageForm->load($submitter);
                            $submitter->sendForm($form);
                            break;
                        case 2:
                            $gangForm = new AdministrativeGangForm();
                            $form = $gangForm->load($submitter);
                            $submitter->sendForm($form);
                            break;
                }
             
            },
            function (Player $submitter): void {
                
            }
        );
    }
}
