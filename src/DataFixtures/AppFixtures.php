<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Entity\Project;
use App\Entity\SchoolYear;
use EasySlugger\Slugger;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // créer un utilisateur ayant un rôle admin
        $user = new User();
        $user->setEmail('admin@example.com');
        $user->setRoles('ROLE_ADMIN');
        $user->setFirstname('Foo');
        $user->setLastname('Foo');
        // encoder le mot de passe
        $password = $this->encoder->encodePassword($user, 'motdepasse');
        $user->setPassword($password);
        $manager->persist($user);
        
        // créer un générateur de fausses données, localisé pour le français
        $faker = \Faker\Factory::create('fr_FR');

        // créer 60 students
        for ($i = 0; $i < 60; $i++) {
            // générer un prénom et un nom de famille
            $firstname = $faker->firstName;
            $lastname = $faker->lastName;

            // sluggifier le prénom et le nom de famille (enlever les majuscules et les accents)
            // et concaténer avec un nom de domaine de mail généré
            $email = Slugger::slugify($firstname).'.'.Slugger::slugify($lastname).'@'.$faker->safeEmailDomain;

            // créer un student avec les données générées
            $user = new User();
            $user->setRoles('ROLE_STUDENT');
            $user->setPassword($password);
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setEmail($email);
            $manager->persist($user);
        }

        // créer 5 teachers
        for ($i = 0; $i < 5; $i++) {
            // générer un prénom et un nom de famille
            $firstname = $faker->firstName;
            $lastname = $faker->lastName;

            // sluggifier le prénom et le nom de famille (enlever les majuscules et les accents)
            // et concaténer avec un nom de domaine de mail généré
            $email = Slugger::slugify($firstname).'.'.Slugger::slugify($lastname).'@'.$faker->safeEmailDomain;

            // créer un teacher avec les données générées
            $user = new User();
            $user->setRoles('ROLE_TEACHER');
            $user->setPassword($password);
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setEmail($email);
            $manager->persist($user);
        }
        // créer 15 client
        for ($i = 0; $i < 15; $i++) {
            // générer un prénom et un nom de famille
            $firstname = $faker->firstName;
            $lastname = $faker->lastName;

            // sluggifier le prénom et le nom de famille (enlever les majuscules et les accents)
            // et concaténer avec un nom de domaine de mail généré
            $email = Slugger::slugify($firstname).'.'.Slugger::slugify($lastname).'@'.$faker->safeEmailDomain;

            // créer un client avec les données générées
            $user = new User();
            $user->setRoles('ROLE_CLIENT');
            $user->setPassword($password);
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setEmail($email);
            $manager->persist($user);
        }
        // créer 3 school years
        for ($i = 0; $i < 3; $i++) {
            // générer un nom
            $name = $faker->word;

            // créer une schoolyear avec les données générées
            $schoolyear = new SchoolYear();
            $schoolyear->setName($name);
            $manager->persist($schoolyear);
        }

        // créer 20 project
        for ($i = 0; $i < 20; $i++) {
            // générer un nom
            $name = $faker->word;

            // créer un project avec les données générées
            $project = new Project();
            $project->setName($name);
            $manager->persist($project);
        }
        // sauvegarder le tout en BDD
        $manager->flush();   
    }
}
