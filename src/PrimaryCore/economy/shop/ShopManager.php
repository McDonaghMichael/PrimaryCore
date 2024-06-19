<?php

namespace PrimaryCore\economy\shop;
use PrimaryCore\economy\shop\{CategoryForm, ShopItem, SubCategoryForm};

use pocketmine\player\Player;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;

class ShopManager {
    /** @var Category[] */
    private array $categories = [];

    public function __construct() {
        $this->loadCategories();
    }

    private function loadCategories(): void {
     
        $blocksCategory = new CategoryForm("Blocks");
        $blocksSubcategory = new SubCategoryForm("Building Blocks");
        $blocksSubcategory->addItem(new ShopItem("Stone", 1, 10));
        $blocksSubcategory->addItem(new ShopItem("Wood", 2, 5));
        $blocksCategory->addSubcategory($blocksSubcategory);
        $this->addCategory($blocksCategory);

        $decorationsCategory = new CategoryForm("Decorations");
        $decorationsSubcategory = new SubCategoryForm("Furniture");
        $decorationsSubcategory->addItem(new ShopItem("Chair", 3, 15));
        $decorationsSubcategory->addItem(new ShopItem("Table", 4, 20));
        $decorationsCategory->addSubcategory($decorationsSubcategory);
        $this->addCategory($decorationsCategory);

    }

    public function addCategory(CategoryForm $category): void {
        $this->categories[] = $category;
    }

    public function getCategories(): array {
        return $this->categories;
    }

    public function load(Player $player): void {
        $options = [];
        foreach ($this->categories as $category) {
            $options[] = new MenuOption($category->getName());
        }

        $shopForm = new MenuForm(
            "Shop",
            "Please choose a category",
            $options,
            function(Player $submitter, int $selected) : void{
                $selectedCategory = $this->categories[$selected];
                $selectedCategory->openForm($submitter);
            },
            function(Player $submitter) : void{
                // Handle form close if needed
            }
        );

        $player->sendForm($shopForm);
    }
}
