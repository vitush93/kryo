<?php

namespace App;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Nette\Neon\Neon;

class EntityManagerFactory
{
    /**
     * @return EntityManager
     */
    static function create()
    {
        $devMode = true;

        $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__), $devMode);

        $neonConfig = file_get_contents(__DIR__.'/../config/config.local.neon'); // yuck
        $neonConfig = Neon::decode($neonConfig);

        return EntityManager::create($neonConfig['parameters']['doctrine'], $config);
    }
}