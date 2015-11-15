<?php

namespace App\Components;


use App\Model\Order;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Control;

class Orders extends Control
{
    /** @var EntityManager */
    private $em;

    /** @var array */
    private $orders = [];

    /**
     * Orders constructor.
     * @param EntityManager $entityManager
     */
    function __construct(EntityManager $entityManager)
    {
        parent::__construct();

        $this->em = $entityManager;
    }

    /**
     * @param $orders
     */
    function setOrders($orders)
    {
        $this->orders = $orders;
    }

    /**
     * @param int $id Order id.
     */
    function handleCancel($id)
    {
        /** @var Order $order */
        $order = $this->em->find(Order::class, $id);

        if ($order) {
            $order->setStatus(Order::STATUS_CANCELLED);

            $this->em->flush();
            $this->presenter->flashMessage('Order has been cancelled', 'info');
            $this->presenter->redirect('Homepage:default');
        }
    }

    function render()
    {
        $this->template->setFile(__DIR__ . '/orders.latte');
        $this->template->orders = $this->orders;

        $this->template->render();
    }
}

interface IOrdersFactory
{
    /** @return Orders */
    function create();
}