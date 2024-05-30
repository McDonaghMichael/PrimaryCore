<?php

namespace PrimaryCore\utils\translate;
use pocketmine\utils\TextFormat;

class TranslateManager {

    public function __construct() {
        
    }

    public function translate(string $key, array $placeholders = []): string {
        // Check and find the correct constant to use
        $translation = $this->findTranslation($key);

        if ($translation !== null) {
            // Replace placeholders in the message
            foreach ($placeholders as $placeholder => $value) {
                $translation = str_replace('{' . $placeholder . '}', $value, $translation);
            }
            // Replace color placeholders with Minecraft color codes
            $translation = $this->replaceColorPlaceholders($translation);
            // Replace formatting placeholders with Minecraft formatting codes
            $translation = $this->replaceFormattingPlaceholders($translation);
            return $translation;
        } else {
            // If translation not found, return the key itself
            return $key;
        }
    }

    private function findTranslation(string $key): ?string {
        // Check each translation category
        $reflectionClass = new \ReflectionClass(TranslationMessages::class);
        $constants = $reflectionClass->getConstants();

        foreach ($constants as $constantName => $constantValue) {
            if (is_array($constantValue) && array_key_exists($key, $constantValue)) {
                return $constantValue[$key];
            }
        }

        return null; 
    }

    public function replaceColorPlaceholders(string $translation): string {
        $translation = str_replace('{BLACK}', TextFormat::BLACK, $translation);
        $translation = str_replace('{DARK_BLUE}', TextFormat::DARK_BLUE, $translation);
        $translation = str_replace('{DARK_GREEN}', TextFormat::DARK_GREEN, $translation);
        $translation = str_replace('{DARK_AQUA}', TextFormat::DARK_AQUA, $translation);
        $translation = str_replace('{DARK_RED}', TextFormat::DARK_RED, $translation);
        $translation = str_replace('{DARK_PURPLE}', TextFormat::DARK_PURPLE, $translation);
        $translation = str_replace('{GOLD}', TextFormat::GOLD, $translation);
        $translation = str_replace('{GRAY}', TextFormat::GRAY, $translation);
        $translation = str_replace('{DARK_GRAY}', TextFormat::DARK_GRAY, $translation);
        $translation = str_replace('{BLUE}', TextFormat::BLUE, $translation);
        $translation = str_replace('{GREEN}', TextFormat::GREEN, $translation);
        $translation = str_replace('{AQUA}', TextFormat::AQUA, $translation);
        $translation = str_replace('{RED}', TextFormat::RED, $translation);
        $translation = str_replace('{LIGHT_PURPLE}', TextFormat::LIGHT_PURPLE, $translation);
        $translation = str_replace('{YELLOW}', TextFormat::YELLOW, $translation);
        $translation = str_replace('{WHITE}', TextFormat::WHITE, $translation);

        return $translation;
    }

    public function replaceFormattingPlaceholders(string $translation): string {
        $translation = str_replace('{OBFUSCATED}', TextFormat::OBFUSCATED, $translation);
        $translation = str_replace('{BOLD}', TextFormat::BOLD, $translation);
        $translation = str_replace('{STRIKETHROUGH}', TextFormat::STRIKETHROUGH, $translation);
        $translation = str_replace('{UNDERLINE}', TextFormat::UNDERLINE, $translation);
        $translation = str_replace('{ITALIC}', TextFormat::ITALIC, $translation);
        $translation = str_replace('{RESET}', TextFormat::RESET, $translation);

        return $translation;
    }
}
