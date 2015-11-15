<?php

namespace App\Presenters;


use App\Components\IOrdersFactory;
use App\Model\Order;
use App\Model\Settings;
use App\Utils\BootstrapForm;
use Nette\Application\UI\Form;

class HomepagePresenter extends BasePresenter
{

    /** @var IOrdersFactory @inject */
    public $ordersFactory;

    function orderFormSucceeded(Form $form, $values)
    {
        $user = $this->findUser();

        $order = new Order($user);
        $order->setType($values->type);
        $order->setAmount($values->amount);
        $order->setName($values->name);
        $order->setBusinessName($values->business_name);
        $order->setIc($values->ic);
        $order->setDic($values->dic);
        $order->setAddress($values->address);
        $order->setInvoiceAddress($values->invoice_address);
        $order->setShippingMethod($values->shipping_method);
        $order->setNote($values->note);

        $price_index = $values->type . '.price_per_unit';
        $order->setPricePerUnit(Settings::get($price_index));

        $this->em->persist($order);
        $this->em->flush();

        $order->createNum();
        $this->em->flush();

        $this->flashMessage('Order has been successfully created!', 'success');
        $this->redirect('this');
    }

    protected function createComponentOrderForm()
    {
        $form = new Form();

        $form->addSelect('type', 'Type', [
            Order::TYPE_NITROGEN => 'Nitrogen',
            Order::TYPE_HELIUM => 'Helium'
        ])->setRequired();
        $form->addText('amount', 'Amount [liters]')
            ->addRule(Form::NUMERIC)
            ->addRule(Form::MIN, 'Amount must be at least %dl', 1)
            ->setRequired();
        $form->addText('name', 'Name')
            ->setOption('description', 'Enter the name to be used in invoice.')
            ->setRequired();
        $form->addText('business_name', 'Bussiness Name')
            ->setOption('description', 'Fill in case you are a company.');
        $form->addText('ic', 'IC')
            ->setOption('description', 'Fill in case you are a company');
        $form->addText('dic', 'DIC')
            ->setOption('description', 'Fill in case you are a company');
        $form->addTextArea('address', 'Address')
            ->setOption('description', 'Enter the shipping address.')
            ->setRequired();
        $form->addTextArea('invoice_address', 'Invoice address')
            ->setOption('description', 'Enter the invoice address if it differs from the shipping address.');
        $form->addSelect('shipping_method', 'Shipping method', [
            Order::SHIPPING_DELIVERY => 'I want to deliver the package to specified address',
            Order::SHIPPING_PICKUP => 'I will pick the package myself'
        ])->setRequired();
        $form->addTextArea('note', 'Note')
            ->setOption('description', 'You can enter additional information related to this order.');

        $form->addSubmit('process', 'Create order');

        $form->onSuccess[] = $this->orderFormSucceeded;

        return BootstrapForm::makeBootstrap($form);
    }

    protected function createComponentOrders()
    {
        $orders = $this->ordersFactory->create();
        $orders->setOrders($this->em->getRepository(Order::class)->findBy([
            'user' => $this->findUser(),
            'status' => Order::STATUS_PENDING
        ]));

        return $orders;
    }
}
