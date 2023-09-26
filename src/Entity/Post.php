<?php

namespace App\Entity;

use DateTimeImmutable;

class Post
{

    /**
     * @var int|null
     */
    private ?int $id;

    /**
     * @var string|null
     */
    private ?string $title;

    /**
     * @var string|null
     */
    private ?string $slug;

    /**
     * @var string|null
     */
    private ?string $thumbnail;

    /**
     * @var string|null
     */
    private ?string $hat;

    /**
     * @var string|null
     */
    private ?string $content;

    /**
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $created_at;

    /**
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $updated_at;

    /**
     * @var bool|null
     */
    private ?Bool $is_active;

    /**
     * @var int|null
     */
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
     * @return Post
     */
    public function setTitle($title): self
    {
        $this->title = $title;

        return $this;
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
     * @return Post
     */
    public function setSlug($slug): self
    {
        $this->slug = $slug;

        return $this;
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
     * @return Post
     */
    public function setThumbnail($thumbnail): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
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
     * @return Post
     */
    public function setHat($hat): self
    {
        $this->hat = $hat;

        return $this;
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
     * @return Post
     */
    public function setContent($content): self
    {
        $this->content = $content;

        return $this;
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
     * @return Post
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->created_at = $createdAt;

        return $this;
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
     * @return Post
     */
    public function setUpdatedAt($updatedAt): self
    {
        $this->updated_at = $updatedAt;

        return $this;
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
     * @return Post
     */
    public function setUserId($user_id): self
    {
        $this->user_id = $user_id;

        return $this;
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
     * @return Post
     */
    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;

        return $this;
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