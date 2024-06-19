<?php

namespace PrimaryCore\gangs\forms\manage;

use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use PrimaryCore\Main;
use PrimaryCore\gangs\forms\menu\MenuGangForm;
use PrimaryCore\gangs\forms\manage\ManageSelectedPlayerGangForm;

class ManageGangForm {

    public function load(Player $player): MenuForm {
        $gangManager = Main::getInstance()->getGangManager();
        $gangName = $gangManager->getPlayerGang($player->getName());

        if ($gangName === null) {
            // If the player is not in a gang, you can return an error message or handle it accordingly
            return new MenuForm(
                TF::BOLD . "Error",
                "You are not in a gang!",
                [
                    new MenuOption("Return\n" . TF::ITALIC . TF::DARK_GRAY . "Back to menu...")
                ],
                function (Player $submitter, int $selected): void {
                    // Handle return to the main menu or previous screen
                    $menuForm = new MenuGangForm();
                    $form = $menuForm->load($submitter);
                    $submitter->sendForm($form);
                }
            );
        }

        $members = $gangManager->getPlayersInGang($gangName);

        // Create menu options for each gang member
        $options = [];
        foreach ($members as $memberName) {
            $options[] = new MenuOption($memberName);
        }

        // Add a return option at the bottom
        $options[] = new MenuOption("Return\n" . TF::ITALIC . TF::DARK_GRAY . "Back to menu...");

        return new MenuForm(
            TF::BOLD . "Manage Gang",
            "Select a member to manage:",
            $options,
            function (Player $submitter, int $selected) use ($members): void {
                if ($selected < count($members)) {
                    $selectedMember = $members[$selected];
                    // Here you can handle what happens when a member is clicked
                    // For example, you could show another form with actions for the selected member
                    $submitter->sendMessage(TF::GREEN . "You selected: " . $selectedMember);
                    $playerForm = new ManageSelectedPlayerGangForm();
                    $form = $playerForm->load($submitter, $selectedMember);
                    $submitter->sendForm($form);
                } else {
                    // Handle the "Return" option
                    $menuForm = new MenuGangForm();
                    $form = $menuForm->load($submitter);
                    $submitter->sendForm($form);
                }
            },
            function (Player $submitter): void {
                // This handles what happens when the player closes the form
                $menuForm = new MenuGangForm();
                $form = $menuForm->load($submitter);
                $submitter->sendForm($form);
            }
        );
    }
}
