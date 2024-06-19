<?php

namespace PrimaryCore\kits;

use PrimaryCore\Main;
use pocketmine\plugin\Plugin;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\entity\effect\Effect;

class Kit {
    private int $id;
    private string $name;
    private string $description;
    private string $permission;
    private int $cooldown;
    private array $items;
    private array $effects;

    public function __construct(int $id, string $name, string $description, string $permission, int $cooldown, array $items = [], array $effects = []) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;#
        $this->permission = $permission;
        $this->cooldown = $cooldown;
        $this->items = $items;
        $this->effects = $effects;
    }

    public function getId(): int {
        return $this->id;
    }
    
    public function getName(): string {
        return $this->name;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getPermission(): string {
        return $this->permission;
    }

    public function getCooldown(): int {
        return $this->cooldown;
    }

    public function getItems(): array {
        return $this->items;
    }

    public function getEffects(): array {
        return $this->effects;
    }

    public function addItem(Item $item): void {
        $this->items[] = $item;
    }

    public function addEffect(Effect $effect): void {
        $this->effects[] = $effect;
    }
}