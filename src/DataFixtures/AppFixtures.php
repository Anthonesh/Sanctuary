<?php

namespace App\DataFixtures;
use App\Entity\Informationsresidents;
use App\Entity\ResidentInformation;
use App\Entity\Residents;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {


        //Creation d'utilisateurs
        $adminUser = $this->createUser("admin@refuge.com",["ROLE_ADMIN"], "123456789", "Rusch",  "Julien", "0745321695", "12", "rue du moulin", "34280", "Bailalrgues", "France", $manager);

        
        //Residents data

        $residentsNames = [
            'Cabaretune', 'Halfen', 'Jappeloup', 'Jesty', 'Jojo', 'Julie', "Lenthier d'Y",
            'Léon', 'Lila', 'Mambo', 'Mistral', 'Ninja', 'Pepsi', 'Petite fleur', 'Qaida', 'Rolls', 'Tommy', 'Zoe'
        ];

        $type = ['Poney', 'Cheval de selle', 'Cheval de course', 'Pur-sang', 'Trait'];

        $images = [
            'images\cards\Cabaretune.webp', 'images\cards\Halfen.webp', 'images\cards\Jappeloup.webp', 'images\cards\Jesty.webp', 'images\cards\Jojo.webp',
            'images\cards\Julie.webp', "images\cards\Lenthier d'Y.webp", 'images\cards\Léon.webp', 'images\cards\Lila.webp', 'images\cards\Mambo.webp', 
            'images\cards\Mistral.webp', 'images\cards\Ninja.webp', 'images\cards\Pepsi.webp', 'images\cards\Petite_fleur.webp', 'images\cards\Qaida.webp',
            'images\cards\Rolls.webp', 'images\cards\Tommy.webp', 'images\cards\Zoé.webp'
        ];

        $imageIndex = 0;

        foreach ($residentsNames as $name) {
            $resident = new Residents();
            $resident->setName($name);
            $resident->setType($type[array_rand($type)]);
            $resident->setBirthDate(new \DateTime('-' . mt_rand(1, 20) . ' years'));
            $resident->setDescription('Histoire du resident');

            // Attribuer une image au resident en utilisant le compteur
            $resident->setImage($images[$imageIndex]);
        
            // Incrémenter le compteur pour passer à l'image suivante
            $imageIndex++;
        
            // Assurez-vous que le compteur ne dépasse pas la taille du tableau
            if ($imageIndex >= count($images)) {
                $imageIndex = 0; // Revenir au début du tableau si nécessaire
            }


            $manager->persist($resident);
        }

        $manager->flush();

        $residents = $manager->getRepository(residents::class)->findAll();

        foreach ($residents as $resident) {
            $infosresident = new ResidentInformation();
            $infosresident->setFood('Information sur la nourriture');
            $infosresident->setCare('Information sur les soins');
            $infosresident->setHealthRecord('Information sur le carnet de santé');  

            $manager->persist($infosresident);
        }

        $manager->flush();

    }

    public function createUser($email, $arrRoles, $Password, $lastName, $firstName, $phoneNumber, $streetNumber, $streetName, $postalCode, $city, $country, ObjectManager $manager): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setRoles($arrRoles);
        $user->setPassword(password_hash($Password, PASSWORD_BCRYPT));
        $user->setLastName($lastName);
        $user->setFirstName($firstName);
        $user->setPhoneNumber($phoneNumber);
        $user->setStreetNumber($streetNumber);
        $user->setStreetName($streetName);
        $user->setPostalCode($postalCode);
        $user->setCity($city);
        $user->setCountry($country);

        $manager->persist($user);

        // $this->setReference('utilisateurs-' . $this->counter, $user);
        // $this->counter++;
        
        return $user;
    }
}