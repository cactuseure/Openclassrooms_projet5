<?php
namespace App\Entity;

use App\Repository\CommentRepository;
use DateTimeImmutable;

class Comment
{
    private ?int $id;
    private string $content;
    private int $author_id;
    private DateTimeImmutable $createdAt;
    private ?int $parentId;
    private int $postId;
    private bool $isApproved;
    private ?array $children;

    public function __construct(
        ?int              $id,
        string            $content,
        int               $author_id,
        DateTimeImmutable $createdAt,
        ?int              $parentId,
        int               $postId,
        bool              $isApproved = false,
        array             $children = [],
    ) {
        $this->id = $id;
        $this->content = $content;
        $this->author_id = $author_id;
        $this->createdAt = $createdAt;
        $this->parentId = $parentId;
        $this->postId = $postId;
        $this->isApproved = $isApproved;

        $commentRepository = new CommentRepository();
        $this->children = $commentRepository->setChildren($id);
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getStatus(): bool
    {
        return $this->isApproved;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getAuthorId(): int
    {
        return $this->author_id;
    }

    public function setAuthorId(int $author_id): void
    {
        $this->author_id = $author_id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function setParentId(?int $parentId): void
    {
        $this->parentId = $parentId;
    }

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function setPostId(int $postId): void
    {
        $this->postId = $postId;
    }

    public function isApproved(): bool
    {
        return $this->isApproved;
    }

    public function setApproved(bool $isApproved): void
    {
        $this->isApproved = $isApproved;
    }

    public function hasChildren(): bool
    {
        return empty($this->children);
    }

    public function getChildren(): ?array
    {
        return $this->children;
    }
}
