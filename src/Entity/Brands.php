<?php

namespace App\Entity;

use OpenApi\Annotations as OA;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
/**
 * @OA\Schema(
 *    schema="Brands",
 *    description="Représente une marque de produits"
 *)
 * @Serializer\ExclusionPolicy("ALL")
 * 
 */

#[ORM\Entity(repositoryClass: BrandsRepository::class)]




class Brands
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /**
     * @OA\Property(
     *     type="integer",
     *     description="L'identifiant unique de la marque"
     * )
     */
    private ?int $id = null;

    #[Serializer\Expose]
    #[ORM\Column(length: 200)]
    /**
     * @OA\Property(
     *     type="string",
     *     maxLength=200,
     *     description="Le nom de la marque"
     * )
     */
    private ?string $name = null;

    /**
     * @var Collection<int, Product>
     * 
     * @OA\Property(
     *     type="array",
     *     @OA\Items(ref="#/components/schemas/Product"),
     *     description="Liste des produits associés à cette marque"
     * )
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'brands', orphanRemoval: true)]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }


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

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setBrands($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            if ($product->getBrands() === $this) {
                $product->setBrands(null);
            }
        }

        return $this;
    }

}
