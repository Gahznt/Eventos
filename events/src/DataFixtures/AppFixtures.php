<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    const DUMP_FILE='stateanpad_events.sql';
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        $file = realpath(__DIR__.'/dump/'.self::DUMP_FILE);

        if (file_exists($file)) {
            $sql = file_get_contents($file);
            $manager->getConnection()->exec($sql);
        }

        $manager->flush();

    }
}
