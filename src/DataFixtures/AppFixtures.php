<?php

namespace App\DataFixtures;

use App\Entity\Catalog;
use App\Entity\Price;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AppFixtures extends Fixture
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function load(ObjectManager $manager)
    {
        $catalog1 = new Catalog();
        $catalog1->setFilePath('catalog1.json');
        $manager->persist($catalog1);

        $catalog2 = new Catalog();
        $catalog2->setFilePath('catalog2.json');
        $catalog2->setState('imported');
        $manager->persist($catalog2);

        $manager->flush();

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
