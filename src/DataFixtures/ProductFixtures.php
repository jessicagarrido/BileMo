<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Brands;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $productPrice = [
            999.99, 849.99, 699.99, 1199.99, 549.99,
            1299.99, 1099.99, 799.99, 599.99, 1399.99
        ];
    
        $productName = [
            'iPhone 15 Pro', 'Samsung Galaxy S23', 'Google Pixel 8',
            'OnePlus 11', 'Xiaomi Redmi Note 12', 'Sony Xperia 1 V',
            'Huawei P60 Pro', 'Oppo Find X5 Pro', 'Asus ROG Phone 7',
            'Motorola Edge 40 Pro'
        ];
    
        $productDescription = [
            'Le dernier modèle d\'Apple avec un design élégant et une puissance inégalée.',
            'Smartphone haut de gamme de Samsung, doté d\'un écran AMOLED vibrant et de performances exceptionnelles.',
            'Un téléphone signé Google, connu pour ses incroyables capacités en photographie et son interface fluide.',
            'Un flagship killer offrant un excellent rapport qualité-prix, avec des performances dignes des meilleurs.',
            'Un téléphone abordable avec une grande autonomie et un écran spacieux.',
            'Un appareil photo performant et une expérience multimédia immersive, parfait pour les amateurs de médias.',
            'Un téléphone premium de Huawei avec un design luxueux et des capacités photo exceptionnelles.',
            'Le modèle phare d\'Oppo, conçu pour les amateurs de photographie et doté d\'une charge rapide avancée.',
            'Un smartphone dédié aux gamers, avec un écran ultra-rapide et une grande capacité de stockage.',
            'Une option premium avec un design élégant, des performances puissantes et une excellente autonomie.'
        ];
    
        $brands = [
            'Apple', 'Samsung', 'Google', 'OnePlus', 'Xiaomi',
            'Sony', 'Huawei', 'Oppo', 'Asus', 'Motorola'
        ];
    
        $brandEntities = [];
        foreach ($brands as $brandName) {
            $brand = new Brands();
            $brand->setName($brandName);
            $manager->persist($brand);
            $brandEntities[] = $brand;
        }
    
        for ($i = 0; $i < 10; $i++) {
            $product = new Product();
            $product->setName($productName[$i])
                ->setPrice($productPrice[$i])
                ->setDescription($productDescription[$i])
                ->setBrands($brandEntities[$i]);
            $manager->persist($product);
        }
    
        $manager->flush();
    }
    

}