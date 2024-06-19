<?php

namespace PrimaryCore\economy\shop;

use pocketmine\player\Player;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use PrimaryCore\economy\shop\SubCategoryForm;

class CategoryForm {
    private string $name;
    /** @var Subcategory[] */
    private array $subcategories = [];

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function addSubcategory(SubCategoryForm $subcategory): void {
        $this->subcategories[] = $subcategory;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getSubcategories(): array {
        return $this->subcategories;
    }

    public function getOptions(Player $player): array {
        $options = [];
        foreach ($this->subcategories as $subcategory) {
            $options[] = new MenuOption($subcategory->getName());
        }
        return $options;
    }

    public function openForm(Player $player): void {
        $options = $this->getOptions($player);
        $categoryForm = new MenuForm(
            "Category: " . $this->getName(),
            "Please select a subcategory",
            $options,
            function(Player $submitter, int $selected) : void{
                $selectedSubcategory = $this->subcategories[$selected];
                $selectedSubcategory->openForm($submitter);
            },
            function(Player $submitter) : void{
                // Handle form close if needed
            }
        );
        $player->sendForm($categoryForm);
    }
}
