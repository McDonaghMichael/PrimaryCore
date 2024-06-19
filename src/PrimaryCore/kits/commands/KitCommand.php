<?php

namespace PrimaryCore\kits\commands;

use PrimaryCore\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;

class KitCommand extends Command {
    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = []) {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission("global.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game.");
            return;
        }

        $kitsManager = Main::getInstance()->getKitsManager();
        $options = [];
        foreach ($kitsManager->getAllKits() as $kit) {
            if ($sender->hasPermission($kit->getPermission())) {
                $cooldownTime = $kitsManager->getCooldownTime($sender, $kit->getName());
                if ($cooldownTime === 0) {
                    $status = TF::GREEN . TF::BOLD . "READY";
                } else {
                    $formattedTime = $this->formatCooldownTime($cooldownTime);
                    $status = TF::RED . TF::BOLD . $formattedTime;
                }
                $options[] = new MenuOption($kit->getName() . " - " . $status);
            }
        }

        if (empty($options)) {
            $sender->sendMessage(TF::RED . "You do not have access to any kits.");
            return;
        }

        $mainForm = new MenuForm(
            TF::BOLD . "KITS",
            "Please choose a kit",
            $options,
            function (Player $submitter, int $selected) use ($options, $kitsManager): void {
                $selectedOption = $options[$selected]->getText();
                $kitName = explode(" - ", $selectedOption)[0];

                if ($kitsManager->giveKit($submitter, $kitName)) {
                    $submitter->sendMessage(TF::GREEN . "You have selected the " . $kitName . " kit.");
                } else {
                    $submitter->sendMessage(TF::RED . "Failed to give you the " . $kitName . " kit.");
                }
            },
            function (Player $submitter): void {
                $submitter->sendMessage(TF::RED . "You closed the kit menu.");
            }
        );

        $sender->sendForm($mainForm);
    }

    private function formatCooldownTime(int $seconds): string {
        $days = intdiv($seconds, 86400);
        $seconds %= 86400;
        $hours = intdiv($seconds, 3600);
        $seconds %= 3600;
        $minutes = intdiv($seconds, 60);
        $seconds %= 60;

        return "{$days}D {$hours}H {$minutes}M {$seconds}S";
    }
}
