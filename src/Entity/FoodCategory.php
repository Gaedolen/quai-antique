<?php

namespace App\Entity;

use App\Entity\Food;
use App\Repository\FoodCategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FoodCategoryRepository::class)]
class FoodCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Relation vers Food
    #[ORM\ManyToOne(targetEntity: Food::class, inversedBy: 'foodCategories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Food $food = null;

    // Relation vers Category
    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'foodCategories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFood(): ?Food
    {
        return $this->food;
    }

    public function setFood(?Food $food): self
    {
        $this->food = $food;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        return $this;
    }
}