<?php
if (!isset($_SERVER['argv'][2])) {
    echo '
Add new user to database.
Usage: create-user.php <email> <password> <role>
';
    exit(1);
}
$container = require __DIR__ . '/../app/bootstrap.php';
$user = new \App\Model\User($_SERVER['argv'][1], $_SERVER['argv'][2], $_SERVER['argv'][3]);

/** @var \Doctrine\ORM\EntityManager $em */
$em = $container->getByType('\Doctrine\ORM\EntityManager');

$em->persist($user);
$em->flush();

echo "User {$_SERVER['argv'][1]} was added.\n";