<?php

namespace App\Entity;

use App\Core\Db;
use DateTimeImmutable;

class Post
{
    private ?int $id = null;
    private ?string $title = null;
    private ?string $slug = null;
    private ?string $thumbnail = null;
    private ?string $hat = null;
    private ?string $content = null;
    private ?DateTimeImmutable $createdAt = null;
    private ?DateTimeImmutable $updatedAt = null;
    private ?bool $isActive = null;
    private ?int $authorId = null;

    public function __construct(
        ?string $title = null,
        ?string $slug = null,
        ?string $thumbnail = null,
        ?string $hat = null,
        ?string $content = null,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null,
        ?bool $isActive = null,
        ?int $authorId = null,
        ?int $id = null,
    )
    {
        if ($title !== null) {
            $this->title = $title;
        }
        if ($slug !== null) {
            $this->slug = $slug;
        }
        if ($thumbnail !== null) {
            $this->thumbnail = $thumbnail;
        }
        if ($hat !== null) {
            $this->hat = $hat;
        }
        if ($content !== null) {
            $this->content = $content;
        }
        if ($createdAt !== null) {
            $this->createdAt = $createdAt;
        }
        if ($updatedAt !== null) {
            $this->updatedAt = $updatedAt;
        }
        if ($isActive !== null) {
            $this->isActive = $isActive;
        }
        if ($authorId !== null) {
            $this->authorId = $authorId;
        }
        if ($id !== null) {
            $this->id = $id;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail($thumbnail): void
    {
        $this->thumbnail = $thumbnail;
    }

    public function getHat(): string
    {
        return $this->hat;
    }

    public function setHat($hat): void
    {
        $this->hat = $hat;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent($content): void
    {
        $this->content = $content;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function setAuthorId($authorId): void
    {
        $this->authorId = $authorId;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function setCategoryId(?int $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }
}