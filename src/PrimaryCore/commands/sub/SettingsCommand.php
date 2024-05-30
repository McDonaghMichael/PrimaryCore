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
use pocketmine\player\Player;

class SettingsCommand extends Command
{
    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission("global.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $options = ["Inmate"];
        foreach(Main::getInstance()->getRankManager()->getRanksForPlayer($sender) as $rank){
            $options[] = $rank->getName();
        }
        $mainForm = new CustomForm(
			TF::BOLD . "SETTINGS",
			[
				new Label("this_is_a_label", "Change your server settings here"),
                new Toggle("scoreboard", "Scoreboard", Main::getInstance()->getUserManager()->isScoreboardEnabled($sender->getName())),
                new Toggle("announcements", "Announcements", Main::getInstance()->getUserManager()->isAnnouncementsEnabled($sender->getName())),
				new Dropdown("ranks", "Display Rank", $options),
				
			],

            function (Player $submitter, CustomFormResponse $response): void {
                $submitter->sendMessage("Response: " . print_r($response, true));
                Main::getInstance()->getUserManager()->updateScoreboardSetting($submitter->getName(), $response->getBool("scoreboard"));
                Main::getInstance()->getUserManager()->updateAnnouncementsSetting($submitter->getName(), $response->getBool("announcements"));

            },


            function(Player $submitter) : void {
                $submitter->sendMessage(TF::RED . "You closed the kit menu.");
            }
        );
        $sender->sendForm($mainForm);
    }

}