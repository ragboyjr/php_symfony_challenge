<?php

namespace App\Service;

use App\Entity\Price;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class ConversorManager
 * @package App\Service
 *
 * Service used to convert currency
 */
class ConversorManager
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * ConversorManager constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Get current exchange
     */
    public function getExchange($base, $currencyToConvert)
    {
        $data = json_decode(file_get_contents("https://api.exchangeratesapi.io/latest?base=$base&symbols=$currencyToConvert"), true);

        return $data;
    }

    /**
     * Convert amount
     * @param Price $price
     * @param $currencyToConvert
     * @return false|float
     * @throws \Exception
     */
    public function convertProductPrice(Product $product, $currencyToConvert)
    {
        //get exchange rates and validate third party service return valid information
        if (!$product->getPrice() instanceof Price) {
            throw new \Exception("Price not set");
        }

        $exchange = $this->getExchange($product->getPrice()->getCurrency(), $currencyToConvert);

        if (!isset($exchange['rates'][$currencyToConvert])) {
            throw new \Exception("No currency rates founded");
        }

        //convert amount
        $exchangePrice = $product->getPrice()->getAmount() * $exchange['rates'][$currencyToConvert];

        return round($exchangePrice, 2);
    }
}
