<?php
/**
 * This script initializes data required by the application (settings table for example).
 */

$container = require __DIR__ . '/../app/bootstrap.php';

/** @var \Doctrine\ORM\EntityManager $em */
$em = $container->getByType('\Doctrine\ORM\EntityManager');

foreach ($em->getRepository(\App\Model\Setting::class)->findAll() as $setting) {
    $em->remove($setting);
}
$em->flush();

$helium_price = new \App\Model\Setting('helium.price_per_unit', '10');
$nitrogen_price = new \App\Model\Setting('nitrogen.price_per_unit', '3');

$em->persist($helium_price);
$em->persist($nitrogen_price);

$em->flush();


echo 'Done.';