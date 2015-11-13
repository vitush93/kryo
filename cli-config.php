<?php

/** @var \Nette\DI\Container $container */
$container = require 'app/bootstrap.php';

/** @var \Doctrine\ORM\EntityManager $em */
$em = $container->getByType('Doctrine\ORM\EntityManager');

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($em);