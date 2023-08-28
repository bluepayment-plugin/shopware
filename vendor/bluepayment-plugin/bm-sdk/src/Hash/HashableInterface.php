<?php
declare(strict_types=1);

namespace BlueMedia\Hash;

interface HashableInterface
{
    public function toArray(): array;
    public function getHash(): string;
    public function isHashPresent(): bool;
}
