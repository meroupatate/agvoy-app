<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Region;
use App\Entity\Room;
use App\Entity\Owner;

class AppFixtures extends Fixture
{
    // définit un nom de référence pour une instance de Region
    public const IDF_REGION_REFERENCE = 'idf-region';
    public const JEAN_MICHEL_OWNER_REFERENCE = 'jean-michel-owner';

    public function load(ObjectManager $manager)
    {
        //...

        $region = new Region();
        $region->setCountry("FR");
        $region->setName("Ile de France");
        $region->setPresentation("La région française capitale");
        $manager->persist($region);

        $manager->flush();
        // Une fois l'instance de Region sauvée en base de données,
        // elle dispose d'un identifiant généré par Doctrine, et peut
        // donc être sauvegardée comme future référence.
        $this->addReference(self::IDF_REGION_REFERENCE, $region);

        $owner = new Owner();
        $owner->setFirstname("Jean");
        $owner->setFamilyName("Michel");
        $owner->setCountry("FR");
        $owner->setAddress("2 rue des Plantes en Pot");
        $manager->persist($region);

        $manager->flush();
        $this->addReference(self::JEAN_MICHEL_OWNER_REFERENCE, $owner);


        $room = new Room();
        $room->setSummary("Beau poulailler ancien à Évry");
        $room->setDescription("très joli espace sur paille");
        $room->setCapacity(2);
        $room->setPrice(300);
        $room->setSuperficy(21);
        $room->setAddress("9 rue Charles Fourier");
        //$room->addRegion($region);
        // On peut plutôt faire une référence explicite à la référence
        // enregistrée précédamment, ce qui permet d'éviter de se
        // tromper d'instance de Region :
        $room->addRegion($this->getReference(self::IDF_REGION_REFERENCE));
        $room->setOwner($this->getReference(self::JEAN_MICHEL_OWNER_REFERENCE));
        $manager->persist($room);

        $manager->flush();

        //...
    }

    //...
}