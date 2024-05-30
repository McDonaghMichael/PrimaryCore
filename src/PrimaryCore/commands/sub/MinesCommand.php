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

class MinesCommand extends Command
{
    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission("global.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $options = [];
        foreach(Main::getInstance()->getMinesManager()->getMines() as $mine){
            if($mine->getRankRequired()[0] <= Main::getInstance()->getUserManager()->getPlayerMineRank($sender->getName())){
                $options[] = new MenuOption($mine->getMineName());
            }
            
        }
        $mainForm = new MenuForm(
            TF::BOLD . "MINES",
            "Please choose a mine",
            $options,

            function(CommandSender $submitter, int $selected) use ($options) : void {
               
            },

            function(CommandSender $submitter) : void {
      
                $submitter->sendMessage(TF::RED . "You closed the mines menu.");
            }
        );

        $sender->sendForm($mainForm);
    }

}