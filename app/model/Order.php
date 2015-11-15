<?php

namespace App\Model;


use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Nette\InvalidArgumentException;

/**
 * Class Order
 * @package App\Model
 *
 * @Entity()
 * @Table(name="orders")
 */
class Order
{
    const SHIPPING_PICKUP = 'pickup';
    const SHIPPING_DELIVERY = 'delivery';
    const TYPE_NITROGEN = 'nitrogen';
    const TYPE_HELIUM = 'helium';

    private static $ALLOWED_SHIPPING_METHODS = [
        self::SHIPPING_DELIVERY,
        self::SHIPPING_PICKUP
    ];

    private static $ALLOWED_TYPES = [
        self::TYPE_HELIUM,
        self::TYPE_NITROGEN
    ];

    /**
     * @ManyToOne(targetEntity="User", inversedBy="orders")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     *
     * @var User
     */
    private $user;

    /**
     * @Id()
     * @GeneratedValue()
     * @Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @Column(type="string")
     * @var string
     */
    private $type;

    /**
     * @Column(type="integer")
     * @var int
     */
    private $amount;

    /**
     * @Column(type="integer")
     * @var int
     */
    private $price_per_unit;

    /**
     * @Column(type="text", nullable=true)
     * @var string
     */
    private $note;

    /**
     * @Column(type="text")
     * @var string
     */
    private $name;

    /**
     * @Column(type="string")
     *
     * @var string
     */
    private $address;

    /**
     * @Column(type="string")
     *
     * @var string
     */
    private $shipping_method;

    /**
     * @Column(type="string", nullable=true)
     * @var string
     */
    private $business_name;

    /**
     * @Column(type="string", nullable=true)
     * @var string
     */
    private $ic;

    /**
     * @Column(type="string", nullable=true)
     * @var string
     */
    private $dic;

    /**
     * Order constructor.
     * @param User $user
     */
    function __construct(User $user)
    {
        $this->setUser($user);
    }

    /**
     * @return array
     */
    public static function getALLOWEDTYPES()
    {
        return self::$ALLOWED_TYPES;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getPricePerUnit()
    {
        return $this->price_per_unit;
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
     * @param int $price_per_unit
     */
    public function setPricePerUnit($price_per_unit)
    {
        $this->price_per_unit = $price_per_unit;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        if (!in_array($type, self::$ALLOWED_TYPES)) throw new InvalidArgumentException("Type $type not found.");

        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return array
     */
    public static function getSHIPPINGMETHODS()
    {
        return self::$ALLOWED_SHIPPING_METHODS;
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
     * @return string
     */
    public function getShippingMethod()
    {
        return $this->shipping_method;
    }

    /**
     * @param string $shipping_method
     */
    public function setShippingMethod($shipping_method)
    {
        if (!in_array($shipping_method, self::$ALLOWED_SHIPPING_METHODS)) throw new InvalidArgumentException("Shipping method $shipping_method not found.");

        $this->shipping_method = $shipping_method;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $user->addOrder($this);

        $this->user = $user;
    }


}