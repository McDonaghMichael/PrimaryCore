<?php

namespace PrimaryCore\crates;

use pocketmine\math\Vector3;
use PrimaryCore\Main;
use PrimaryCore\crates\commands\CratesCommand;
use PrimaryCore\utils\FloatingTextAPI;
use pocketmine\world\Position;
use pocketmine\world\World;
use pocketmine\player\Player;

class CratesManager {

    /** @var Crate[] */
    private $crates = [];

    /** @var KeyManager */
    private $keyManager;

    public function __construct() {
        $this->keyManager = new KeyManager();

        Main::getInstance()->getServer()->getPluginManager()->registerEvents(new CratesListener($this), Main::getInstance());
        Main::getInstance()->getServer()->getCommandMap()->registerAll("PrimaryCore", [
            new CratesCommand("crates", "Permet d'aller au spawn", "/spawn"),
        ]);

        $this->addCrate(1, "Test", "world", new Vector3(100, 100, 100));
    }

    public function getKeyManager(): KeyManager {
        return $this->keyManager;
    }

    public function addCrate(int $id, string $name, string $world, Vector3 $position, array $items = []): void {
        $crate = new Crate($id, $name, $world, $position, $items);
        $this->crates[$id] = $crate;
    }

    public function removeCrate(int $id): void {
        unset($this->crates[$id]);
    }

    public function getCrate(int $id): ?Crate {
        return $this->crates[$id] ?? null;
    }

    public function getAllCrates(): array {
        return $this->crates;
    }

    public function clearAllCrates(): void {
        $this->crates = [];
    }

    public function updateCrateText(Player $player){
        foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $player) {
            if ($player instanceof Player) {
            $message = str_replace("{count}", Main::getInstance()->getCratesManager()->getKeyManager()->getSpecificKeyCount($player->getName(), "Test"), "You have {count} keys");
            FloatingTextAPI::update($player, "Test", $message);
        }
    }
    }
}
