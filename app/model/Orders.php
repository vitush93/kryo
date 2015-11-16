<?php

namespace App\Model;


use Doctrine\ORM\EntityManager;
use Nette\Object;

class Orders extends Object
{
    /** @var EntityManager */
    private $em;

    function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param int|Order $order
     * @param int $status
     */
    function changeStatus($order, $status)
    {
        if (is_object($order)) {
            $order->setStatus($status);
        } else {
            $this->em->find(Order::class, $order)->setStatus($status);
        }

        $this->em->flush();
    }
}