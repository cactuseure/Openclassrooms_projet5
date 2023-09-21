<?php

namespace App\Entity;

use DateTimeImmutable;

class Post
{
    private ?int $id;
    private ?string $title;
    private ?string $slug;
    private ?string $thumbnail;
    private ?string $hat;
    private ?string $content;
    private ?DateTimeImmutable $created_at;
    private ?DateTimeImmutable $updated_at;
    private ?Bool $is_active;
    private ?int $user_id;

    public function __construct(
        int $id = null,
        string $title = null,
        string $slug = null,
        string $thumbnail = null,
        string $hat = null,
        string $content = null,
        DateTimeImmutable $created_at = null,
        DateTimeImmutable $updated_at = null,
        bool $is_active = null,
        int $user_id = null,
    )
    {
        $this->id = $id;
        $this->title = $title;
        $this->slug = $slug;
        $this->thumbnail = $thumbnail;
        $this->hat = $hat;
        $this->content = $content;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
        $this->is_active = $is_active;
        $this->user_id = $user_id;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param $title
     * @return void
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param $slug
     * @return void
     */
    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return string|null
     */
    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    /**
     * @param $thumbnail
     * @return void
     */
    public function setThumbnail($thumbnail): void
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * @return string
     */
    public function getHat(): string
    {
        return $this->hat;
    }

    /**
     * @param $hat
     * @return void
     */
    public function setHat($hat): void
    {
        $this->hat = $hat;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param $content
     * @return void
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @param DateTimeImmutable $createdAt
     * @return void
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->created_at = $createdAt;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updated_at;
    }

    /**
     * @param $updatedAt
     * @return void
     */
    public function setUpdatedAt($updatedAt): void
    {
        $this->updated_at = $updatedAt;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @param $user_id
     * @return void
     */
    public function setUserId($user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * @param bool $is_active
     * @return void
     */
    public function setIsActive(bool $is_active): void
    {
        $this->is_active = $is_active;
    }

    /**
     * @param array $data
     * @return Post|null
     */
    public static function createFromDatabase(array $data): ?Post
    {
        if (!$data) {
            return null;
        }
        $createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['created_at']);
        $updateAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['updated_at']);
        return new self(
            $data['id'],
            $data['title'],
            $data['slug'],
            $data['thumbnail'],
            $data['hat'],
            $data['content'],
            $createdAt,
            $updateAt,
            $data['is_active'],
            $data['user_id'],
        );
    }
}