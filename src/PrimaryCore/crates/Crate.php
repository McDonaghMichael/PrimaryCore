<?php

namespace PrimaryCore\crates;

use pocketmine\item\Item;
use pocketmine\math\Vector3;

class Crate {

    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $world;

    /** @var Vector3 */
    private $position;

    /** @var Item[] */
    private $items = [];

    public function __construct(int $id, string $name, string $world, Vector3 $position, array $items = []) {
        $this->id = $id;
        $this->name = $name;
        $this->world = $world;
        $this->position = $position;
        $this->items = $items;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getWorld(): string {
        return $this->world;
    }

    public function getPosition(): Vector3 {
        return $this->position;
    }

    public function getItems(): array {
        return $this->items;
    }

    public function addItem(Item $item): void {
        $this->items[] = $item;
    }

    public function removeItem(Item $item): void {
        $key = array_search($item, $this->items, true);
        if ($key !== false) {
            unset($this->items[$key]);
        }
    }

    public function clearItems(): void {
        $this->items = [];
    }
}
