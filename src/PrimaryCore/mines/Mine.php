<?php
namespace PrimaryCore\mines;

class Mine {
    private $mineId;
    private $mineName;
    private $worldName;
    private $coords;
    private $rankRequired;

    public function __construct(int $mineId, string $mineName, string $worldName, array $coords, array $rankRequired = []) {
        $this->mineId = $mineId;
        $this->mineName = $mineName;
        $this->worldName = $worldName;
        $this->coords = $coords;
        $this->rankRequired = $rankRequired;
    }

    public function getMineId(): int {
        return $this->mineId;
    }

    public function getMineName(): string {
        return $this->mineName;
    }

    public function getWorldName(): string {
        return $this->worldName;
    }

    public function getCoords(): array {
        return $this->coords;
    }

    public function getRankRequired(): array {
        return $this->rankRequired;
    }
}
