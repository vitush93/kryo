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

$address = new \App\Model\Setting('supply.address', "Univerzita Karlova v Praze, Matematicko-fyzikální fakulta\nNové Město, Ke Karlovu 3\n121 16 Praha 2\nČeská republika");
$ic = new \App\Model\Setting('supply.ic', '28115708');
$dic = new \App\Model\Setting('supply.dic', 'CZ28115708');

$name = new \App\Model\Setting('contact.name', 'Kryo');
$mail = new \App\Model\Setting('contact.email', 'noreply@mossbauer.cz');


$em->persist($helium_price);
$em->persist($nitrogen_price);
$em->persist($address);
$em->persist($ic);
$em->persist($dic);
$em->persist($name);
$em->persist($mail);

$em->flush();


echo 'Done.';