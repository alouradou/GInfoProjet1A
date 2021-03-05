<?php

namespace App\DataFixtures;

use App\Entity\Course;
use App\Entity\Item;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function makeValidUsername($string): string{
        for ($i=0;$i<strlen($string);$i++) {
            if ($string[$i] == " "){
                $string[$i] = "_";
            }
        }
        return strtr($string,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
            'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $faker = Factory::create('fr_FR');

        $user = new User();
        $user->setFirstName("John")
             ->setLastName("Appleseed")
             ->setEmail("john.appleseed@centrale-marseille.fr")
             ->setPromo(2020)
             ->setPassword($this->passwordEncoder->encodePassword($user,"azerty"))
             ->setUsername("jappleseed");

        $manager->persist($user);

        $user2 = new User();
        $user2->setFirstName("Tim")
            ->setLastName("Cook")
            ->setEmail("tim.cook@centrale-marseille.fr")
            ->setPromo(2020)
            ->setPassword($this->passwordEncoder->encodePassword($user,"AppleCupertino$2011"))
            ->setUsername("tcook")
        ->setRoles(["ROLE_ADMIN"]);

        $manager->persist($user2);

        for ($i=0;$i<40;$i++){
            $user = new User();
            $user->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setPromo(2020)
                ->setPassword($this->passwordEncoder->encodePassword($user,"azerty"));
            $username = $this->makeValidUsername($user->getFirstName()[0].$user->getLastName());
            $user->setUsername(strtolower($username))
            ->setEmail(strtolower($username)."@centrale-marseille.fr");
            $manager->persist($user);
        }

        $course2 =  new Course();
        $course2->setCreatedBy($user2)
            ->setName("Mon premier cours de Symfony")
            ->setActive(true)
            ->setOpen(false);
        $manager->persist($course2);
        for ($i=0;$i<2;$i++){
            $course =  new Course();
            $course->setCreatedBy($user)
                ->setName("Mon premier cours de ".$faker->internetExplorer)
                ->setActive(true)
                ->setOpen(false);
            $manager->persist($course);
        }

//        $item =  new Item();
//        $item->setChapter($faker->word())
//            ->setCourse($course2)
//            ->setDescription($faker->word)
//            ->setName($faker->name)
//            ->setOrd($faker->randomDigit);
        $item2 =  new Item();
        $item2->setChapter($faker->word())
             ->setCourse($course2)
             ->setDescription($faker->word)
             ->setName($faker->name)
             ->setOrd($faker->randomDigit);
        $manager->persist($item2);

        $manager->flush();
    }
}
