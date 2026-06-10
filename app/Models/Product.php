<?php

namespace App\Models;

class Product
{
    public function __construct(
        private string $id,
        private string $name,
        private string $description,
        private float $price,
        private ?float $oldPrice,
        private string $image,
        private string $alt,
        private ?string $tagLabel = null,
        private ?string $tagClass = null,
        private bool $featured = false,
        private bool $muted = false
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function price(): float
    {
        return $this->price;
    }

    public function oldPrice(): ?float
    {
        return $this->oldPrice;
    }

    public function image(): string
    {
        return $this->image;
    }

    public function alt(): string
    {
        return $this->alt;
    }

    public function tagLabel(): ?string
    {
        return $this->tagLabel;
    }

    public function tagClass(): ?string
    {
        return $this->tagClass;
    }

    public function featured(): bool
    {
        return $this->featured;
    }

    public function muted(): bool
    {
        return $this->muted;
    }
}
