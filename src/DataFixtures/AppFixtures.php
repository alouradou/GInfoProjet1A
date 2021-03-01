<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $user = new User();
        $user->setFirstName("John")
             ->setLastName("Appleseed")
             ->setEmail("john.appleseed@centrale-marseille.fr")
             ->setPromo(2020)
             ->setPassword("azerty")
             ->setUsername("jappleseed");

        $manager->persist($user);

        $user2 = new User();
        $user2->setFirstName("Tim")
            ->setLastName("Cook")
            ->setEmail("tim.cook@centrale-marseille.fr")
            ->setPromo(2020)
            ->setPassword("azerty")
            ->setUsername("tcook");

        $manager->persist($user2);

        $manager->flush();
    }
}
