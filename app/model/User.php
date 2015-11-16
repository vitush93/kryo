<?php

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Nette\InvalidArgumentException;
use Nette\Security\Passwords;
use Nette\Utils\Validators;

/**
 * Class User
 * @package App\Model
 *
 * @Entity()
 * @Table(name="users")
 */
class User
{
    const ROLE_ADMIN = 'admin';
    const ROLE_KRYO = 'kryo';
    const ROLE_USER = 'user';

    private static $ALLOWED_ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_USER,
        self::ROLE_KRYO
    ];

    /**
     * @OneToMany(targetEntity="Order", mappedBy="user")
     *
     * @var ArrayCollection
     */
    private $orders;

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
     * @Column(type="string", nullable=true)
     * @var string
     */
    private $phone;

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
     * @Column(type="string", nullable=true)
     *
     * @var string
     */
    private $address;


    /**
     * @Column(type="string", nullable=true)
     *
     * @var string
     */
    private $business_name;

    /**
     * @Column(type="string", nullable=true)
     *
     * @var string
     */
    private $ic;

    /**
     * @Column(type="string", nullable=true)
     *
     * @var string
     */
    private $dic;

    /**
     * User constructor.
     *
     * @param string $email
     * @param string $password
     * @param string $role
     */
    function __construct($email, $password, $role)
    {
        $this->orders = new ArrayCollection();

        $this->setEmail($email);
        $this->setPassword($password);
        $this->setRole($role);
    }

    /**
     * @return string
     */
    public function getBusinessName()
    {
        return $this->business_name;
    }

    /**
     * @param string $business_name
     */
    public function setBusinessName($business_name)
    {
        $this->business_name = $business_name;
    }

    /**
     * @return string
     */
    public function getIc()
    {
        return $this->ic;
    }

    /**
     * @param string $ic
     */
    public function setIc($ic)
    {
        $this->ic = $ic;
    }

    /**
     * @return string
     */
    public function getDic()
    {
        return $this->dic;
    }

    /**
     * @param string $dic
     */
    public function setDic($dic)
    {
        $this->dic = $dic;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return ArrayCollection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param Order $order
     */
    public function addOrder(Order $order)
    {
        $this->orders->add($order);
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
     * @return array
     */
    public static function getALLOWEDROLES()
    {
        return self::$ALLOWED_ROLES;
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

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }
}