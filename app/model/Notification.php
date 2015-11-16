<?php

namespace App\Model;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

/**
 * Class Notification
 * @package App\Model
 *
 * @Entity()
 * @Table(name="notifications")
 */
class Notification
{
    const TYPE_SUCCESS = 'success';
    const TYPE_WARNING = 'warning';

    /**
     * @ManyToOne(targetEntity="Order")
     * @JoinColumn(name="order_id", referencedColumnName="id")
     * @var Order
     */
    private $order;

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
    private $message;

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    private $date;

    /**
     * Notification constructor.
     * @param Order $order
     * @param $message
     */
    function __construct(Order $order, $message)
    {
        $this->order = $order;
        $this->message = $message;
        $this->date = new \DateTime();
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
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
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param Order $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }
}