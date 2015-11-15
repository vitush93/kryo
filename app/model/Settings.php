<?php

namespace App\Model;


use App\EntityManagerFactory;
use Doctrine\ORM\EntityManager;
use Nette\InvalidArgumentException;

class Settings
{
    /** @var null|self */
    private static $instance;

    /** @var EntityManager */
    private $em;

    /**
     * Settings constructor.
     * @param EntityManager $entityManager
     */
    private function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    private static function init()
    {
        $factory = new EntityManagerFactory();
        $em = $factory->create();

        self::$instance = new Settings($em);
    }

    private function _get($key)
    {
        /** @var Setting $setting */
        $setting = $this->em->getRepository(Setting::class)->findOneBy(['key' => $key]);
        if (!$setting) throw new InvalidArgumentException("Setting with key $key was not found");

        return $setting->getValue();
    }

    private function _set($key, $value)
    {
        /** @var Setting $setting */
        $setting = $this->em->getRepository(Setting::class)->findOneBy(['key' => $key]);
        if ($setting) {
            $setting->setValue($value);

            $this->em->flush();
        } else {
            $setting = new Setting($key, $value);

            $this->em->persist($setting);
            $this->em->flush();
        }
    }

    static function get($key)
    {
        if (self::$instance == null) self::init();

        return self::$instance->_get($key);
    }

    static function set($key, $value)
    {
        if (self::$instance == null) self::init();

        self::$instance->_set($key, $value);
    }

}