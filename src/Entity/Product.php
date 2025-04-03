<?php

namespace App\Entity;

use OpenApi\Annotations as OA;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Hateoas\Relation(
 *      "self_mobile",
 *      href = @Hateoas\Route(
 *          "api_mobile",
 *          parameters = { "id" = "expr(object.getId())" }
 *      )
 * )
 * @Hateoas\Relation(
 *      "all_mobiles",
 *      href = @Hateoas\Route("api_listMobiles")
 * )
 * @Serializer\ExclusionPolicy("ALL")
 *
 * @OA\Schema(
 *     schema="Product",
 *     description="Représente un produit avec ses informations."
 * )
 */
#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Serializer\Expose]
    /**
     * @OA\Property(type="integer", description="Identifiant unique du produit")
     */
    private ?int $id = null;

    #[ORM\Column(length: 200)]
    #[Serializer\Expose]
    /**
     * @OA\Property(type="string", maxLength=200, description="Nom du produit")
     */
    private ?string $name = null;

    #[ORM\Column]
    #[Serializer\Expose]
    /**
     * @OA\Property(type="number", format="float", description="Prix du produit")
     */
    private ?float $price = null;

    #[ORM\Column(length: 5000)]
    #[Serializer\Expose]
    /**
     * @OA\Property(type="string", maxLength=5000, description="Description détaillée du produit")
     */
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    /**
     * @OA\Property(ref="#/components/schemas/Brands", description="Marque associée au produit")
     */
    private ?Brands $brands = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getBrands(): ?Brands
    {
        return $this->brands;
    }

    public function setBrands(?Brands $brands): static
    {
        $this->brands = $brands;
        return $this;
    }
}
