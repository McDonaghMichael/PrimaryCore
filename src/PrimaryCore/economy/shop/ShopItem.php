<?php

namespace PrimaryCore\economy\shop;

class ShopItem {
    private string $name;
    private int $itemId;
    private int $price;

    public function __construct(string $name, int $itemId, int $price) {
        $this->name = $name;
        $this->itemId = $itemId;
        $this->price = $price;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getItemId(): int {
        return $this->itemId;
    }

    public function getPrice(): int {
        return $this->price;
    }
}
