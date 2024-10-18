<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Emprunt;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {

        $faker = Faker\Factory::create();

        $books = [];
        for ($i = 0; $i < 10; $i++) {
            $book = new Book();
            $book->setTitle($faker->name());
            $book->setAuthor($faker->userName());
            $books[] = $book;

            $manager->persist($book);
        }

        $admin = new User();
        $admin->setEmail('admin@symf.fr');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        for ($i = 0; $i < 6; $i += 2) {
            $reservation = new Emprunt();
            $reservation->setDate(new \DateTimeImmutable("now -" . $i . " day"));
            $reservation->setEmprunteur($admin);
            $reservation->setLivre($books[$i]);

            $manager->persist($reservation);
        }

        $manager->flush();
    }
}
