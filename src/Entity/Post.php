<?php

namespace App\Entity;

use App\Core\Db;
use DateTimeImmutable;

class Post
{
    private int $id;
    private string $title;
    private string $slug;
    private string $thumbnail;
    private string $hat;
    private string $content;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;
    private int $authorId;
    private ?int $categoryId;

    public function __construct(string $title, string $slug, string $thumbnail, string $hat, string $content, DateTimeImmutable $createdAt, DateTimeImmutable $updatedAt, int $authorId, ?int $categoryId)
    {
        $this->title = $title;
        $this->slug = $slug;
        $this->thumbnail = $thumbnail;
        $this->hat = $hat;
        $this->content = $content;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->authorId = $authorId;
        $this->categoryId = $categoryId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    public function getThumbnail(): string
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

}