<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $styleNumber;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="array")
     * @Assert\NotBlank
     */
    private $images = [];


    /**
     * @ORM\ManyToOne(targetEntity=Price::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     */
    private $price;

    /**
     * For JsonResponse purpose
     * @return array
     */
    public function toArray()
    {
        $product =  [
            'id' => $this->getId(),
            'styleNumber' => $this->getStyleNumber(),
            'name' => $this->getName(),
            'price' => $this->getPrice()->getCurrency().$this->getPrice()->getAmount(),
        ];

        foreach ($this->getImages() as $x => $image) {
            $product['image'.$x] = $image;
        }

        return $product;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStyleNumber(): ?string
    {
        return $this->styleNumber;
    }

    public function setStyleNumber(string $styleNumber): self
    {
        $this->styleNumber = $styleNumber;

        return $this;
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

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(array $images): self
    {
        $this->images = $images;

        return $this;
    }

    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function setPrice(?Price $price): self
    {
        $this->price = $price;

        return $this;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new UniqueEntity([
            'fields' => 'styleNumber',
        ]));
        $metadata->addPropertyConstraint('name', new Assert\NotNull());
        $metadata->addPropertyConstraint('styleNumber', new Assert\NotNull());
        $metadata->addPropertyConstraint('price', new Assert\NotNull());
        $metadata->addPropertyConstraint('price', new Assert\Type(Price::class));
        $metadata->addPropertyConstraint('images', new Assert\NotNull());
    }

    public function __toString(): string
    {
        return  (string) $this->name;
    }
}
