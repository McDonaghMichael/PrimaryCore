<?php

namespace PrimaryCore\economy\shop;

use pocketmine\player\Player;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Label;
use dktapps\pmforms\element\Slider;
use dktapps\pmforms\FormIcon;
use pocketmine\utils\TextFormat as TF;

class SubCategoryForm {
    private string $name;
    /** @var ShopItem[] */
    private array $items = [];

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function addItem(ShopItem $item): void {
        $this->items[] = $item;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getItems(): array {
        return $this->items;
    }

    public function getOptions(Player $player): array {
        $options = [];
        foreach ($this->items as $item) {
            $options[] = new MenuOption($item->getName() . " - $" . $item->getPrice());
        }
        return $options;
    }

    public function handleSelection(Player $player, int $selected): void {
        $selectedItem = $this->items[$selected];
        // Open custom form for quantity selection
        $this->openQuantityForm($player, $selectedItem);
    }

    public function openQuantityForm(Player $player, ShopItem $item): void {
        $itemName = $item->getName();
        $itemPrice = $item->getPrice();
    
        $customForm = new CustomForm(
            "Purchase: " . $itemName . "\nPrice: $" . $itemPrice,
            [
                new Label("info", "Please select the quantity and confirm:"),
                new Slider("quantity", "Quantity", 1, 64, 1, 1) // Slider for quantity selection
            ],
            function(Player $submitter, CustomFormResponse $response) use ($itemPrice, $itemName): void{
                $quantity = (int) $response->getFloat("quantity");
                $totalPrice = $itemPrice * $quantity;
    
                // Example broadcast message upon form submission
                $submitter->sendMessage(TF::GREEN . "You purchased " . $quantity . "x " . $itemName . " for $" . $totalPrice);
            },
            function(Player $submitter) : void{
                // Example broadcast message upon form closure
                $submitter->sendMessage(TF::YELLOW . "You closed the form :(");
            }
        );
        $player->sendForm($customForm);
    }
    

    public function openForm(Player $player): void {
        $options = $this->getOptions($player);
        $subcategoryForm = new MenuForm(
            "Subcategory: " . $this->getName(),
            "Please select an item to purchase",
            $options,
            function(Player $submitter, int $selected) : void{
                $this->handleSelection($submitter, $selected);
            },
            function(Player $submitter) : void{
                // Handle form close if needed
            }
        );
        $player->sendForm($subcategoryForm);
    }
}
