<?php
namespace App\Entity;

use App\Repository\CommentRepository;
use DateTimeImmutable;

class Comment
{
    private ?int $id;
    private ?string $content;
    private ?int $user_id;
    private ?DateTimeImmutable $created_at;
    private ?int $parent_id;
    private ?int $post_id;
    private ?bool $is_approved;
    private ?array $children;

    /**
     * @param int|null $id
     * @param string|null $content
     * @param int|null $user_id
     * @param DateTimeImmutable|null $created_at
     * @param int|null $parent_id
     * @param int|null $post_id
     * @param bool|null $is_approved
     * @param array $children
     */
    public function __construct(
        int $id = null,
        string $content = null,
        int $user_id = null,
        DateTimeImmutable $created_at = null,
        int $parent_id = null,
        int $post_id = null,
        bool $is_approved = null,
        array $children = [],
    ) {
        $this->id = $id;
        $this->content = $content;
        $this->user_id = $user_id;
        $this->created_at = $created_at;
        $this->parent_id = $parent_id;
        $this->post_id = $post_id;
        $this->is_approved = $is_approved;
        $this->children = $children;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function getStatus(): bool
    {
        return $this->is_approved;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return void
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return int
     */
    public function getAuthorId(): int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     * @return void
     */
    public function setAuthorId(int $user_id): void
    {
        $this->user_id = $user_id;
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
     * @return int|null
     */
    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    /**
     * @param int|null $parent_id
     * @return void
     */
    public function setParentId(?int $parent_id): void
    {
        $this->parent_id = $parent_id;
    }

    /**
     * @return int
     */
    public function getPostId(): int
    {
        return $this->post_id;
    }

    /**
     * @param int $post_id
     * @return void
     */
    public function setPostId(int $post_id): void
    {
        $this->post_id = $post_id;
    }

    /**
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->is_approved;
    }

    /**
     * @param bool $is_approved
     * @return void
     */
    public function setApproved(bool $is_approved): void
    {
        $this->is_approved = $is_approved;
    }

    /**
     * @return bool
     */
    public function hasChildren(): bool
    {
        return empty($this->children);
    }

    /**
     * @return array|null
     */
    public function getChildren(): ?array
    {
        return $this->children;
    }

    /**
     * @param array $data
     * @return Comment|null
     */
    public static function createFromDatabase(array $data): ?Comment
    {
        if (!$data) {
            return null;
        }
        $commentRepository = new CommentRepository();
        $createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['created_at']);

        return new self(
            $data['id'],
            $data['content'],
            $data['user_id'],
            $createdAt,
            $data['parent_id'],
            $data['post_id'],
            (bool)$data['is_approved'],
            $commentRepository->setChildren($data['id']),
        );
    }
}
