<?php

namespace App\Components;


use App\Model\Order;
use App\Model\Settings;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Control;
use TCPDF;

class Orders extends Control
{
    /** @var EntityManager */
    private $em;

    /** @var array */
    private $orders = [];

    /** @var \App\Model\Orders */
    private $orderManager;

    /**
     * Orders constructor.
     * @param EntityManager $entityManager
     * @param \App\Model\Orders $orders
     */
    function __construct(EntityManager $entityManager, \App\Model\Orders $orders)
    {
        parent::__construct();

        $this->em = $entityManager;
        $this->orderManager = $orders;
    }

    /**
     * @param $orders
     */
    function setOrders($orders)
    {
        $this->orders = $orders;
    }

    /**
     * @param $id
     */
    function handleInvoice($id)
    {
        /** @var Order $order */
        $order = $this->em->find(Order::class, $id);
        if (!$order) return;

        $this->orderManager->invoice($order);
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