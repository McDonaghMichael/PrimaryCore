<?php

namespace PrimaryCore\commands\forms;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Label;
use dktapps\pmforms\element\Toggle;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use PrimaryCore\Main;

class SettingsForm {

    public function load(Player $player): CustomForm {
        $options = ["Inmate"];
        foreach (Main::getInstance()->getRankManager()->getRanksForPlayer($player) as $rank) {
            $options[] = $rank->getName();
        }

        return new CustomForm(
            TF::BOLD . "SETTINGS",
            [
                new Label("this_is_a_label", "Change your server settings here"),
                new Toggle("scoreboard", "Scoreboard", Main::getInstance()->getUserManager()->isScoreboardEnabled($player->getName())),
                new Toggle("announcements", "Announcements", Main::getInstance()->getUserManager()->isAnnouncementsEnabled($player->getName())),
                new Dropdown("ranks", "Display Rank", $options),
            ],
            function (Player $submitter, CustomFormResponse $response): void {
                $submitter->sendMessage("Response: " . print_r($response, true));
                Main::getInstance()->getUserManager()->updateScoreboardSetting($submitter->getName(), $response->getBool("scoreboard"));
                Main::getInstance()->getUserManager()->updateAnnouncementsSetting($submitter->getName(), $response->getBool("announcements"));
            },
            function (Player $submitter): void {
                $submitter->sendMessage(TF::RED . "You closed the settings menu.");
            }
        );
    }
}
