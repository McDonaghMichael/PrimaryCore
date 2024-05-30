<?php

namespace PrimaryCore\ranks;

class Rank {
    
    /** @var string */
    private $name;
    
    /** @var int */
    private $id;
    
    /** @var string */
    private $format;
    
    /** @var string */
    private $chatFormat;
    
    /** @var string */
    private $nameTagFormat; // New property for name tag format
    
    /** @var array */
    private $permissions;

    public function __construct(string $name, int $id, string $format, string $chatFormat, string $nameTagFormat, array $permissions) {
        $this->name = $name;
        $this->id = $id;
        $this->format = $this->parseColorCodes($format);
        $this->chatFormat = $this->parseColorCodes($chatFormat);
        $this->nameTagFormat = $this->parseColorCodes($nameTagFormat); // Initialize name tag format
        $this->permissions = $permissions;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getFormat(): string {
        return $this->format;
    }

    public function getChatFormat(): string {
        return $this->chatFormat;
    }
    
    public function getNameTagFormat(): string {
        return $this->nameTagFormat; // Getter for name tag format
    }

    public function getPermissions(): array {
        return $this->permissions;
    }

    private function parseColorCodes(string $format): string {
        // Replace color placeholders with Minecraft color codes
        $format = str_replace("{BLACK}", "§0", $format);
        $format = str_replace("{DARK_BLUE}", "§1", $format);
        $format = str_replace("{DARK_GREEN}", "§2", $format);
        $format = str_replace("{DARK_AQUA}", "§3", $format);
        $format = str_replace("{DARK_RED}", "§4", $format);
        $format = str_replace("{DARK_PURPLE}", "§5", $format);
        $format = str_replace("{GOLD}", "§6", $format);
        $format = str_replace("{GRAY}", "§7", $format);
        $format = str_replace("{DARK_GRAY}", "§8", $format);
        $format = str_replace("{BLUE}", "§9", $format);
        $format = str_replace("{GREEN}", "§a", $format);
        $format = str_replace("{AQUA}", "§b", $format);
        $format = str_replace("{RED}", "§c", $format);
        $format = str_replace("{LIGHT_PURPLE}", "§d", $format);
        $format = str_replace("{YELLOW}", "§e", $format);
        $format = str_replace("{WHITE}", "§f", $format);
    
        // Replace formatting placeholders with Minecraft formatting codes
        $format = str_replace("{OBFUSCATED}", "§k", $format);
        $format = str_replace("{BOLD}", "§l", $format);
        $format = str_replace("{STRIKETHROUGH}", "§m", $format);
        $format = str_replace("{UNDERLINE}", "§n", $format);
        $format = str_replace("{ITALIC}", "§o", $format);
        $format = str_replace("{RESET}", "§r", $format);
    
        return $format;
    }
    
}
