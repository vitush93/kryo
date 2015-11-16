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

    const STATUS_PENDING = 1;
    const STATUS_FULFILLED = 2;
    const STATUS_CANCELLED = 3;

    private static $TYPE_NAMES = [
        self::TYPE_HELIUM => 'LHe2',
        self::TYPE_NITROGEN => 'LN2'
    ];

    private static $ALLOWED_SHIPPING_METHODS = [
        self::SHIPPING_DELIVERY,
        self::SHIPPING_PICKUP
    ];

    private static $ALLOWED_TYPES = [
        self::TYPE_HELIUM,
        self::TYPE_NITROGEN
    ];

    private static $ALLOWED_STATUSES = [
        self::STATUS_CANCELLED,
        self::STATUS_FULFILLED,
        self::STATUS_PENDING
    ];

    /**
     * @ManyToOne(targetEntity="User", inversedBy="orders")
     *
     * @var User
     */
    private $user;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="fullfilled_id", referencedColumnName="id")
     * @var User
     */
    private $fulfilled_by;

    /**
     * @Id()
     * @GeneratedValue()
     * @Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @Column(type="string", unique=true)
     * @var string
     */
    private $num = '0';

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    private $date;

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
     * @Column(type="string", nullable=true)
     * @var string
     */
    private $phone;

    /**
     * @Column(type="string")
     *
     * @var string
     */
    private $address;

    /**
     * @Column(type="string", nullable=true)
     * @var string
     */
    private $invoice_address;

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
     * @Column(type="integer")
     * @var int
     */
    private $status;

    /**
     * Order constructor.
     * @param User $user
     */
    function __construct(User $user)
    {
        $this->setUser($user);
        $this->status = self::STATUS_PENDING;
        $this->date = new \DateTime();
    }

    /**
     * @return array
     */
    public static function getALLOWEDTYPES()
    {
        return self::$ALLOWED_TYPES;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        if (!in_array($status, self::$ALLOWED_STATUSES)) throw new InvalidArgumentException("Undefined order status.");

        $this->status = $status;
    }

    /**
     * @return User
     */
    public function getFulfilledBy()
    {
        return $this->fulfilled_by;
    }

    /**
     * @param User $fulfilled_by
     */
    public function setFulfilledBy($fulfilled_by)
    {
        $this->fulfilled_by = $fulfilled_by;
    }

    /**
     * @return string
     */
    public function getType($name = false)
    {
        if ($name) {
            return self::$TYPE_NAMES[$this->type];
        }

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

    function isCancelled()
    {
        return $this->status == self::STATUS_CANCELLED;
    }

    function isPending()
    {
        return $this->status == self::STATUS_PENDING;
    }

    function isFulfilled()
    {
        return $this->status == self::STATUS_FULFILLED;
    }

    /**
     * @return array
     */
    public static function getALLOWEDSTATUSES()
    {
        return self::$ALLOWED_STATUSES;
    }

    /**
     * @return array
     */
    public static function getALLOWEDSHIPPINGMETHODS()
    {
        return self::$ALLOWED_SHIPPING_METHODS;
    }

    /**
     * @return string
     */
    public function getInvoiceAddress()
    {
        if (empty($this->invoice_address)) return $this->address;

        return $this->invoice_address;
    }

    /**
     * @param string $invoice_address
     */
    public function setInvoiceAddress($invoice_address)
    {
        $this->invoice_address = $invoice_address;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getNum()
    {
        return $this->num;
    }

    public function createNum()
    {
        $this->num = date('Ym') . str_pad($this->id, 4, '0', STR_PAD_LEFT);
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