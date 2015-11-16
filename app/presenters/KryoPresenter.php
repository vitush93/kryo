<?php

namespace App\Presenters;


use App\Model\Order;
use App\Model\Orders;
use App\Model\Settings;
use Nette\Mail\Message;

class KryoPresenter extends BasePresenter
{
    /** @var array */
    private $orders;

    /** @var Orders @inject */
    public $orderManager;

    function actionPending()
    {
        $this->setView('orders');

        $this->orders = $this->em->getRepository(Order::class)->findBy([
            'status' => Order::STATUS_PENDING
        ], ['date' => 'DESC']);
    }

    function actionAll()
    {
        $this->setView('orders');

        $this->orders = $this->em->getRepository(Order::class)->findBy([], ['date' => 'DESC']);
    }

    function handleCancel($id)
    {
        /** @var Order $order */
        $order = $this->em->find(Order::class, $id);

        if ($order) {
            $this->orderManager->changeStatus($order, Order::STATUS_CANCELLED);

            $this->flashMessage("Order {$order->getNum()} cancelled.", 'info');
            $this->redirect('this');
        }
    }

    function handleFulfill($id)
    {
        /** @var Order $order */
        $order = $this->em->find(Order::class, $id);

        if ($order) {
            $this->orderManager->changeStatus($order, Order::STATUS_FULFILLED);

            $mail = new Message();
            $mail->setFrom(Settings::get('contact.name').' <'.Settings::get('contact.email').'>')
                ->addTo($order->getUser()->getEmail())
                ->setSubject('Order '.$order->getNum().' ready')
                ->setBody('Your order is ready to be shipped / ready for pick up.');

            $this->mailer->send($mail);

            $this->flashMessage("Order {$order->getNum()} fulfilled", 'info');
            $this->redirect('this');
        }
    }

    function renderOrders()
    {
        $this->template->orders = $this->orders;
    }
}