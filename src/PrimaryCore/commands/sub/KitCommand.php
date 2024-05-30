<?php

namespace PrimaryCore\commands\sub;

use PrimaryCore\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use PrimaryCore\utils\translate\TranslateManager;
use pocketmine\utils\TextFormat as TF;
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

class KitCommand extends Command
{
    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission("global.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $options = [];
        foreach(Main::getInstance()->getKitsManager()->getAllKits() as $kit){
            $options[] = new MenuOption($kit->getName());
        }
        $mainForm = new MenuForm(
            TF::BOLD . "KITS",
            "Please choose a kit",
            $options,

            function(CommandSender $submitter, int $selected) use ($options) : void {
                $kitName = $options[$selected]->getText();
                
                // Create the second form with the selected kit's name as title
                $selectedKitForm = new MenuForm(
                    TF::BOLD . $kitName,
                    "Kit info here",
                    [new MenuOption("Option 3", new FormIcon("https://pbs.twimg.com/profile_images/776551947595833345/Og1CSz_c_400x400.jpg", FormIcon::IMAGE_TYPE_URL))],
        
                    function(CommandSender $submitter, int $selected) use ($kitName) : void {
                        $submitter->sendMessage(TF::GREEN . "You selected kit: " . $kitName);
                    },
        
                    function(CommandSender $submitter) : void {
                        $submitter->sendMessage(TF::RED . "You closed the kit info.");
                    }
                );

                // Send the second form
                $submitter->sendForm($selectedKitForm);
            },

            function(CommandSender $submitter) : void {
                // Handle closure of the main form if needed
                $submitter->sendMessage(TF::RED . "You closed the kit menu.");
            }
        );

        // Send the main form
        $sender->sendForm($mainForm);
    }

}