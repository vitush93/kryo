<?php

namespace App\Model;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Nette\InvalidArgumentException;
use Nette\Security\Passwords;
use Nette\Utils\Validators;

/**
 * Class User
 * @package App\Model
 *
 * @Entity()
 */
class User
{
    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';

    private static $ALLOWED_ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_USER
    ];

    /**
     * @Id()
     * @GeneratedValue()
     * @Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @Column(type="string", nullable=true)
     *
     * @var string
     */
    private $name;

    /**
     * @Column(type="string", unique=true)
     *
     * @var string
     */
    private $email;

    /**
     * @Column(type="string")
     *
     * @var string
     */
    private $password;

    /**
     * @Column(type="string")
     *
     * @var string
     */
    private $role;

    /**
     * User constructor.
     *
     * @param string $email
     * @param string $password
     * @param string $role
     */
    function __construct($email, $password, $role)
    {
        $this->setEmail($email);
        $this->setPassword($password);
        $this->setRole($role);
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole($role)
    {
        if (!in_array($role, self::$ALLOWED_ROLES)) throw new InvalidArgumentException("Role $role not in list of allowed user roles.");

        $this->role = $role;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        if (!Validators::isEmail($email)) throw new InvalidArgumentException('E-mail is not in valid format.');

        $this->email = $email;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        if (strlen($password) < 6) throw new InvalidArgumentException('Password must be at least 6 characters long.');

        $password = Passwords::hash($password);

        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}