<?php

namespace App\DataFixtures;

use App\Entity\Price;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $product = new Product();
        $product->setName('T-Shirt');
        $product->setStyleNumber('ABC|123');
        $price = new Price();
        $price->setAmount(1500);
        $price->setCurrency('USD');
        $product->setPrice($price);
        $images = [
            "https://via.placeholder.com/400x300/4b0082?id=1",
            "https://via.placeholder.com/400x300/4b0082?id=2"
        ];
        $product->setImages($images);
        $manager->persist($product);

        $manager->flush();
    }
}
