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
    public const BRETAGNE_REGION_REFERENCE = 'bretagne-region';
    public const JEANNE_MICHELINE_OWNER_REFERENCE = 'jeanne-micheline-owner';

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

        $region2 = new Region();
        $region2->setCountry("FR");
        $region2->setName("Bretagne");
        $region2->setPresentation("La région du paysan breton");
        $manager->persist($region2);
        $manager->flush();
        $this->addReference(self::BRETAGNE_REGION_REFERENCE, $region2);

        $owner = new Owner();
        $owner->setFirstname("Jean");
        $owner->setFamilyName("Michel");
        $owner->setCountry("FR");
        $owner->setAddress("2 rue des Plantes en Pot");
        $manager->persist($owner);
        $manager->flush();
        $this->addReference(self::JEAN_MICHEL_OWNER_REFERENCE, $owner);

        $owner2 = new Owner();
        $owner2->setFirstname("Jeanne");
        $owner2->setFamilyName("Micheline");
        $owner2->setCountry("FR");
        $owner2->setAddress("5 allée de la boulangerie du village");
        $manager->persist($owner2);
        $manager->flush();
        $this->addReference(self::JEANNE_MICHELINE_OWNER_REFERENCE, $owner2);


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

        $room2 = new Room();
        $room2->setSummary("La cousine de Jean Michel vous héberge");
        $room2->setDescription("Très jolie cousine");
        $room2->setCapacity(1);
        $room2->setPrice(999.98);
        $room2->setSuperficy(20);
        $room2->setAddress("5 allée de la boulangerie du village");
        //$room->addRegion($region);
        // On peut plutôt faire une référence explicite à la référence
        // enregistrée précédamment, ce qui permet d'éviter de se
        // tromper d'instance de Region :
        $room2->addRegion($this->getReference(self::BRETAGNE_REGION_REFERENCE));
        $room2->setOwner($this->getReference(self::JEANNE_MICHELINE_OWNER_REFERENCE));
        $manager->persist($room2);
        $manager->flush();

        $room3 = new Room();
        $room3->setSummary("La cousine de Jean Michel vous héberge CHEZ ELLE");
        $room3->setDescription("Toujours aussi jolie la cousine");
        $room3->setCapacity(1);
        $room3->setPrice(999.99);
        $room3->setSuperficy(30);
        $room3->setAddress("1 impasse de la cousine");
        $room3->addRegion($this->getReference(self::BRETAGNE_REGION_REFERENCE));
        $room3->setOwner($this->getReference(self::JEANNE_MICHELINE_OWNER_REFERENCE));
        $manager->persist($room3);
        $manager->flush();
    }
}