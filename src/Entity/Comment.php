<?php
namespace App\Entity;

use App\Repository\CommentRepository;
use DateTimeImmutable;

class Comment
{

    /**
     * @var int|null
     */
    private ?int $id;

    /**
     * @var string|null
     */
    private ?string $content;

    /**
     * @var int|null
     */
    private ?int $user_id;

    /**
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $created_at;

    /**
     * @var int|null
     */
    private ?int $parent_id;

    /**
     * @var int|null
     */
    private ?int $post_id;

    /**
     * @var bool|null
     */
    private ?bool $is_approved;

    /**
     * @var array|null
     */
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
     * @return Comment
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
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
     * @return Comment
     */
    public function setAuthorId(int $user_id): self
    {
        $this->user_id = $user_id;

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
     * @return Comment
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->created_at = $createdAt;

        return $this;
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
     * @return Comment
     */
    public function setParentId(?int $parent_id): self
    {
        $this->parent_id = $parent_id;

        return $this;
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
     * @return Comment
     */
    public function setPostId(int $post_id): self
    {
        $this->post_id = $post_id;

        return $this;
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
     * @return Comment
     */
    public function setApproved(bool $is_approved): self
    {
        $this->is_approved = $is_approved;

        return $this;
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
