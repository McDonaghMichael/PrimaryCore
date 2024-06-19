<?php

namespace PrimaryCore\gangs\forms\administrative;

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

use PrimaryCore\gangs\forms\administrative\settings\SettingsGangForm;
use PrimaryCore\gangs\forms\administrative\invite\InviteGangForm;
use PrimaryCore\gangs\forms\administrative\delete\DeleteGangForm;

use PrimaryCore\gangs\forms\menu\MenuGangForm;

class AdministrativeGangForm {

    public function load(Player $player): MenuForm {


        $gangName = Main::getInstance()->getGangManager()->getPlayerGang($player->getName());

        return new MenuForm(
			TF::BOLD . "ADMINISTRATIVE",
			"Manage the gang",
			[
				new MenuOption("Settings\n" . TF::ITALIC . TF::DARK_GRAY . "Change gang options..."),
				new MenuOption("Invite Members\n" . TF::ITALIC . TF::DARK_GRAY . "Invite someone to gang..."),

				new MenuOption("DELETE GANG\n" . TF::ITALIC . TF::DARK_GRAY . "Delete gang forever..."),
				new MenuOption("Return\n" . TF::ITALIC . TF::DARK_GRAY . "Back to menu...")


			],
            function(Player $submitter, int $selected) : void{
             switch($selected){
                case 0:
                    $settingsForm = new SettingsGangForm();
                    $form = $settingsForm->load($submitter);
                    $submitter->sendForm($form);
                    break;
                case 1:
                    $inviteForm = new InviteGangForm();
                    $form = $inviteForm->load($submitter);
                    $submitter->sendForm($form);
                    break;
                case 2:
                    $deleteForm = new DeleteGangForm();
                    $form = $deleteForm->load($submitter);
                    $submitter->sendForm($form);
                    break;
                case 3:
                    $menuForm = new MenuGangForm();
                    $form = $menuForm->load($submitter);
                    $submitter->sendForm($form);
                    break;
             }
            },
            function (Player $submitter): void {
                $menuForm = new MenuGangForm();
                $form = $menuForm->load($submitter);
                $submitter->sendForm($form);
            }
        );
    }
}
