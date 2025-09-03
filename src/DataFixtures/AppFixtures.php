<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $products = [
            [
                "sku" => "000001",
                "name" => "BV Lean leather ankle boots",
                "category" => "boots",
                "price" => 89000
            ],
            [
                "sku" => "000002",
                "name" => "BV Lean leather ankle boots",
                "category" => "boots",
                "price" => 99000
            ],
            [
                "sku" => "000003",
                "name" => "Ashlington leather ankle boots",
                "category" => "boots",
                "price" => 71000
            ],
            [
                "sku" => "000004",
                "name" => "Naima embellished suede sandals",
                "category" => "sandals",
                "price" => 79500
            ],
            [
                "sku" => "000005",
                "name" => "Nathane leather sneakers",
                "category" => "sneakers",
                "price" => 59000
            ]
        ];

        foreach ($products as $data) {
            $product = new Product();
            $product->setSku($data['sku']);
            $product->setName($data['name']);
            $product->setCategory($data['category']);
            $product->setPrice($data['price']);

            $manager->persist($product);
        }

        $manager->flush();
    }
}
