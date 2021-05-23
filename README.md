# Projet-student Part 1

Mise en place d'une BDD avec Symfony.

## Stucture de la BDD

1. user
   - id: clé primaire
   - email: varchar 190, unique
   - roles: text
   - password: varchar 190
   - firstname: varchar 190
   - lastname: varchar 190
   - phone: varchar 20, nullable
   - school_year_id: clé étrangère qui pointe vers school_year.id
2. school_year
   - id: clé primaire
   - name: varchar 190
   - date_start: datetime, nullable
   - date_end: datetime, nullable
3. project
   - id: clé primaire
   - name: varchar 190
   - description: text, nullable
4. project_user
   - project_id: clé étrangère qui pointe vers project.id
   - user_id: clé étrangère qui pointe vers user.id

- l'entité User possède une relation ManyToMany vers l'entité Project
- l'entité User possède une relation ManyToOne vers l'entité SchoolYear

## Création d'un utilisateur pour se connecter à la BDD

Ouvrir le terminal puis lancer ces commandes:

1. `sudo mysql`
   - Commande qui permet de rentrer dans le serveur MYSQL.
2. `CREATE USER 'nouveau_utilisateur'@'localhost' IDENTIFIED BY 'mot_de_passe';`
   - Commande qui permet de créer un utilisateur pour se connecter à la BDD. 'nouveau_utilisateur' est le nom de l'utilisateur, 'localhost' correspond au serveur local de l'ordinateur et 'mot_de_passe' est le mot de passe qui permettra la connection.
3. `GRANT ALL PRIVILEGES ON * . * TO 'nouveau_utilisateur'@'localhost';`
   - Commande qui permet d'accorder tous les privilèges pour la BDD, pour le nouvel utilisateur.
4. `FLUSH PRIVILEGES`
   - Commande qui permet de prendre en compte les changements.
5. `exit;`
   - Commande qui permet de sortir du serveur MYSQL.

## Création de la BDD

Ouvrir le terminal puis lancer ces commandes:

1. `sudo mysql`
   - Commande qui permet de rentrer dans le serveur MYSQL.
2. `CREATE DATABASE nomdelabdd;`
   - Commande qui permet de créer la BDD que nous retrouverons dans PMA
3. `exit;`
   - Commande qui permet de sortir du serveur MYSQL.

## Création d'un projet Symfony

Ouvrir le terminal puis lancer ces commandes:

1. `Symfony new nomduprojet --full`
   - Commande qui permet créer le projet Symfony

## Liaison de l'accés à la BDD avec Symfony

Pour lier la BDD au projet Symfony et appliquer les commandes sur la BDD, il faut suivre ces étapes.

1. Créer un fichier .env.local à la racine du projet
   - Dans ce fichier on y met: `DATABASE_URL="mysql://user:motdepasse@127.0.0.1:3306/nomdelabdd?serverVersion=5.7"`
   - 'user' correspond au nom d'utilisateur créé précédemment.
   - 'motdepasse' correspond au mot de passe de l'utilisateur créé précédemment.
   - 'nomdelabdd' correspond au nom de la BDD créé précédemment.

## Création des tables de la BDD

Pour créer les tables de la BDD, nous allons utiliser Doctrine. Voici les étapes à suivre :

1. `php bin/console make:entity`
   - Commande qui permet de créer une entité (table) dans la BDD. La première question posé sera le nom de l'entité, ensuite on nous demandera les propriétés (les colonnes de la table), donc :
     a. Le nom de la propriété
     b. Le type de propriété
     - string
     - boolean
     - relation
     - integer
       c. La longueur (si le type est string) la relation (ManyToOne, OneToMany, OneToOne, ManyToMany)
       d. Si la propriété peut-être nullable ou non
2. Appuyer sur la touche entrée pour arrêter l'ajout de la propriété.
3. `php bin/console make:migration`
   - Commande qui permet de créer une migration. Ce qui va récupérer les entités que nous venons de créer.
4. `php bin/console doctrine:migration:migrate`
   - Commande qui permet d'éxécuter les requêtes et qui permettra de voir que les tables se sont bien créé dans la BDD.
5. `php bin/console doctrine:schema:validate`
   - Commande qui permet de vérifier si tout est à jour.

## Instalation des dépendances

Les dépendances permettent de créer facilement des fixtures, générer des fauses données de façon aléatoire et sluggifier.

1. `doctrine/doctrine-fixtures-bundle`
   - Commande qui permet de créer facilement des fixtures.
2. `fzaninotto/faker`
   - Commande qui permet de générer des fauses données de façon aléatoire. Par exemple : des noms de personne, des adresses, des numéros de téléphone, des paragraphes de texte (du type lorem ipsum), etc.
3. `javiereguiluz/easyslugger`
   - Commande qui permet de sluggifier, c-à-d de transformer toute chaîne de caractères en chaîne de caractères sans majuscule, sans accent et sans espaces. Par exemple : `Foo bar baz` devient `foo-bar-baz`.

## Injection de données indispensables

Les données indispensables correspondent aux données atribués à l'admin de la BDD. Suivre les étapes suivante pour l'injection de données indispensables.

1. Dans `App/DataFixtures.php`

   - Le fichier d'origine se presente de cette façon:

   ```
   <?php
   namespace App\DataFixtures;

   use Doctrine\Bundle\FixturesBundle\Fixture;
   use Doctrine\Persistence\ObjectManager;


   class AppFixtures extends Fixture
    {

       // Sauvegarde dans la BDD
       $manager->flush();

    }
   ```

- En debut de code nous avons la balise ouvrante de PHP, se trouvent ensuite l'importation de dépendances et fichier.

2. Création des données indispensables dans `App/DataFixtures.php`

   - Création d'un User avec un role ADMIN, et importation des fichiers d'entité et les dépendances :

   ```
   <?php
   namespace App\DataFixtures;

   use App\Entity\User;
   use App\Entity\SchoolYear;
   use App\Entity\Project;
   use EasySlugger\Slugger;
   use Doctrine\Bundle\FixturesBundle\Fixture;
   use Doctrine\Persistence\ObjectManager;
   use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

   class AppFixtures extends Fixture
   {
       private $encoder;

       public function __construct(UserPasswordEncoderInterface $encoder)
       {
       $this->encoder = $encoder;
       }

       public function load(ObjectManager $manager)
       {
           // Création User Admin
           $user = new User();
           $user->setFirstname('Foo');
           $user->setLastname('Foo');
           $user->setEmail('admin@example.com');
           $user->setRoles(['ROLE_ADMIN']);
           // Encodage du mot de passe
           $password = $this->encoder->encodePassword($user, 'motdepasse');
           $user->setPassword($password);
           $manager->persist($user);

           // Sauvegarde dans la BDD
           $manager->flush();
       }
   }
   ```

   - Avant de lancer la fixture, entrer les données de test qui vont être générer par les dépendances.

## Injection de données test

Les données test, correspondent à des données aléatoires qui vont être générer pour tester l'application.

1. Dans `App/DataFixtures/AppFixtures.php`

```
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
        $user->setRoles(['ROLE_ADMIN']);
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
            $user->setRoles(['ROLE_STUDENT']);
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
            $user->setRoles(['ROLE_TEACHER']);
            $user->setPassword($password);
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setEmail($email);
            $manager->persist($user);
        }
        // créer 15 clients
        for ($i = 0; $i < 15; $i++) {
            // générer un prénom et un nom de famille
            $firstname = $faker->firstName;
            $lastname = $faker->lastName;

            // sluggifier le prénom et le nom de famille (enlever les majuscules et les accents)
            // et concaténer avec un nom de domaine de mail généré
            $email = Slugger::slugify($firstname).'.'.Slugger::slugify($lastname).'@'.$faker->safeEmailDomain;

            // créer un client avec les données générées
            $user = new User();
            $user->setRoles(['ROLE_CLIENT']);
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

        // créer 20 projects
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
```

2. Ensuite pousser ces données dans la BDD:
   - Entrer la commande `php bin/console doctrine:fixtures:load`
   - Puis on va nous demander si nous voulons effacer si nous voulons effacer les données du fichier `AppFixtures.php`, nous choisissons yes et on appuie sur Return.

# Projet-student Part 2

Création des URL pour afficher les données de la BDD.

## Créer les requêtes

Rentrer ces commandes dans le terminal:

1. `php bin/console make:crud`

   - Permet de créer un controleur selon l'entité voulu.
   - On nous pour quelle entité on veut creer le CRUD.
   - Et on nous demande le nom que nous souhaitons donner au controlleur.

2. `App/src/Controller`

   - Nous y trouverons tout nos controllers créés grâce à la commande précédente, voici un exemple du résultat pour le controller de l'entité Project :

   - Le controller nous a créé une requête pour :
      Afficher toutes les données de la table Project.
      Afficher les données de Project en fonction d'un id.
      Permettre de modifier des données de Project en fonction d'un id.
      Permettre de supprimer un Project en fonction d'un id.

## Test des requêtes avec l'URL

Pour tester les requêtes, lancer le serveur Symfony.

1. `symfony server:start`
   - commande qui perment d'accéder au projet symfony qui se lance à l'adresse: `localhost:8000`

## Les requêtes disponibles

1. `localhost:8000/user`
   - Renvoie la liste complète des utilisateurs

2. `localhost:8000/user/{id}`
   - Renvoie les données d'un user en fonction de son id.

3. `localhost:8000/user/search/{role}`
   - Renvoie une liste de user en fonction de leur role (ROLE_ADMIN, ROLE_STUDENT, ROLE_TEACHER).

4. `localhost:8000/schoolyear`
   - Renvoie la liste des schoolyears.

5. `localhost:8000/schoolyear/{id}`
   - Renvoie les données d'une schoolyear en fonction de son id.

6. `localhost:8000/project`
   - Renvoie la liste des projects.

7. `localhost:8000/project/{id}`
   - Renvoie les données d'un project en fonction de son id.

# Projet-student Part 3

Création du back-end d'une application web qui affiche les pages demandées.

## Créer le formulaire d'authentification

1. `php bin/console make:form`
   - Cette commande génére un formulaire d'authentification.
   - On accède au formulaire par `localhost:8000/login`.
2. Nous allons dans SecurityController.php dans `App/src/Controller/SecurityController.php`
   - Voici le contenu du fichier :
   ```
   <?php

   namespace App\Controller;

   use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
   use Symfony\Component\HttpFoundation\Response;
   use Symfony\Component\Routing\Annotation\Route;
   use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

   class SecurityController extends AbstractController
   {
      /**
      * @Route("/login", name="app_login")
      */
      public function login(AuthenticationUtils $authenticationUtils): Response
      {
         // if ($this->getUser()) {
         //     return $this->redirectToRoute('target_path');
         // }

         // get the login error if there is one
         $error = $authenticationUtils->getLastAuthenticationError();
         // last username entered by the user
         $lastUsername = $authenticationUtils->getLastUsername();

         return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
      }

      /**
      * @Route("/logout", name="app_logout")
      */
      public function logout()
      {
         throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
      }
   }
   ```
   
## Liste des URL disponibles

  | NOM DE LA ROUTE         | METHODE         | URL |
  | ------|-----|-----|
  | project_index             | GET            | /project/ |                         
  | project_new               | GET,POST       | /project/new |                      
  | project_show              | GET            | /project/{id} |                     
  | project_edit              | GET,POST       | /project/{id}/edit |                
  | project_delete            | POST           | /project/{id} |                     
  | school_year_index         | GET            | /schoolyear/ |                      
  | school_year_new           | GET,POST       | /schoolyear/new |                   
  | school_year_show          | GET            | /schoolyear/{id} |                 
  | school_year_edit          | GET,POST       | /schoolyear/{id}/edit |             
  | school_year_delete        | POST           | /schoolyear/{id} |                  
  | app_login                 | ANY            | /login |                            
  | app_logout                | ANY            | /logout |                           
  | user_index                | GET            | /user |                             
  | user_new                  | GET,POST       | /user/new |                         
  | user_show                 | GET            | /user/{id} |                        
  | user_test                 | GET            | /user/search/{roles} |              
  | user_edit                 | GET,POST       | /user/{id}/edit |                   
  | user_delete               | POST           | /user/{id} |   


