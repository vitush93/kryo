<?php

namespace App\Model;


use Doctrine\ORM\EntityManager;
use Nette\Object;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\Passwords;

class Auth extends Object implements IAuthenticator
{
    /** @var EntityManager */
    private $em;

    /**
     * Auth constructor.
     * @param EntityManager $entityManager
     */
    function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    function authenticate(array $credentials)
    {
        list($email, $password) = $credentials;

        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy(array(
            'email' => $email
        ));

        if (!$user) {
            throw new AuthenticationException("User with e-mail $email not found.", self::IDENTITY_NOT_FOUND);
        } elseif (!Passwords::verify($password, $user->getPassword())) {
            throw new AuthenticationException('Incorrect password.', self::INVALID_CREDENTIAL);
        } elseif (Passwords::needsRehash($user->getPassword())) {
            $user->setPassword(Passwords::hash($password));
        }

        $arr = array(
            'email' => $user->getEmail(),
            'id' => $user->getId(),
            'role' => $user->getRole()
        );

        return new Identity($arr['id'], $arr['role'], $arr);
    }


}