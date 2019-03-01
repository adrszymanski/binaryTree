<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NodeRepository")
 * @ORM\Table(indexes={@ORM\Index(name="parent_idx", columns={"parent_id"})})
 */
class Node
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $creditsLeft;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $creditsRight;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default":null})
     */
    private $parentId;

    /**
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private $isLeft;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $depth;

    /**
     * @ORM\Column(type="text", options={"default": "/"})
     */
    private $parents;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreditsLeft(): ?int
    {
        return $this->creditsLeft;
    }

    public function setCreditsLeft(int $creditsLeft): self
    {
        $this->creditsLeft = $creditsLeft;

        return $this;
    }

    public function getCreditsRight(): ?int
    {
        return $this->creditsRight;
    }

    public function setCreditsRight(int $creditsRight): self
    {
        $this->creditsRight = $creditsRight;

        return $this;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function setParentId(?int $parentId): self
    {
        $this->parentId = $parentId;

        return $this;
    }

    public function getIsLeft(): ?bool
    {
        return $this->isLeft;
    }

    public function setIsLeft(bool $isLeft): self
    {
        $this->isLeft = $isLeft;

        return $this;
    }

    public function getDepth(): ?int
    {
        return $this->depth;
    }

    public function setDepth(int $depth): self
    {
        $this->depth = $depth;

        return $this;
    }

    public function getParents(): ?string
    {
        return $this->parents;
    }

    public function setParents(string $parents): self
    {
        $this->parents = $parents;

        return $this;
    }
}
