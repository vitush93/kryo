<?php

namespace App\Presenters;


use App\Components\IOrdersFactory;
use App\Model\Order;
use App\Model\Orders;
use App\Model\Settings;
use App\Model\User;
use App\Utils\BootstrapForm;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Mail\IMailer;
use Nette\Mail\Message;

class HomepagePresenter extends BasePresenter
{
    /** @var Orders @inject */
    public $orderManager;

    /** @var IOrdersFactory @inject */
    public $ordersFactory;

    /** @var IMailer @inject */
    public $mailer;

    function actionAll()
    {
        $this->setView('default');
    }

    function actionDetail($id)
    {
        $order = $this->em->find(Order::class, $id);
        if (!$order) throw new BadRequestException;

        $this->template->order = $order;
    }

    /**
     * @param string $to E-mail address
     * @param string $subject E-mail subject
     * @param string $message
     */
    public function sendMail($to, $subject, $message)
    {
        $mail = new Message();
        $mail->setFrom('MFFun <noreply@vithabada.cz>')
            ->addTo($to)
            ->setSubject($subject)
            ->setHtmlBody($message);
        $this->mailer->send($mail);
    }

    function orderFormSucceeded(Form $form, $values)
    {
        $user = $this->findUser();

        $order = new Order($user);
        $order->setType($values->type);
        $order->setAmount($values->amount);
        $order->setName($values->name);
        $order->setPhone($values->phone);
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

        $this->orderManager->invoice($order, true);

        $mail = new Message();
        $mail->setFrom(Settings::get('contact.name') . ' <' . Settings::get('contact.email') . '>')
            ->addTo($order->getUser()->getEmail())
            ->setSubject('Your order ' . $order->getNum())
            ->setBody('You have placed a new order on kryo.mossbauer.cz. Please follow payment instructions in attachment.');

        $mail->addAttachment(WWW_DIR . '/../temp/' . $order->getInvoiceFileName());

        $this->mailer->send($mail);

        $this->flashMessage('Order has been successfully created!', 'success');
        $this->redirect('this');
    }

    protected function createComponentOrderForm()
    {
        $user = $this->findUser();
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
            ->setDefaultValue($user->getName())
            ->setOption('description', 'Enter the name to be used in invoice.')
            ->setRequired();
        $form->addText('phone', 'Phone')->setDefaultValue($user->getPhone());
        $form->addText('business_name', 'Bussiness Name')
            ->setDefaultValue($user->getBusinessName())
            ->setOption('description', 'Fill in case you are a company.');
        $form->addText('ic', 'IC')
            ->setDefaultValue($user->getIc())
            ->setOption('description', 'Fill in case you are a company');
        $form->addText('dic', 'DIC')
            ->setDefaultValue($user->getDic())
            ->setOption('description', 'Fill in case you are a company');
        $form->addTextArea('address', 'Address', 10, 5)
            ->setDefaultValue($user->getAddress())
            ->setOption('description', 'Enter the shipping address.')
            ->setRequired();
        $form->addTextArea('invoice_address', 'Invoice address', 10, 5)
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

    function accountFormSucceeded(Form $form, $values)
    {
        $user = $this->findUser();

        if ($values->password) {
            $user->setPassword($values->password);
        }

        $user->setName($values->name);
        $user->setEmail($values->email);
        $user->setPhone($values->phone);
        $user->setAddress($values->address);
        $user->setBusinessName($values->business_name);
        $user->setIc($values->ic);
        $user->setDic($values->dic);

        try {
            $this->em->flush();
            $this->flashMessage('Account saved.', 'info');
            $this->redirect('this');
        } catch (UniqueConstraintViolationException $e) {
            $form->addError('User with this e-mail address already exists.');
        }
    }

    protected function createComponentAccountForm()
    {
        /** @var User $defaults */
        $defaults = $this->findUser();

        $form = new Form();

        $form->addText('email', 'E-mail')
            ->setOption('description', 'This is your contact e-mail and your login.')
            ->setDefaultValue($defaults->getEmail())
            ->addRule(Form::EMAIL)
            ->setRequired();
        $form->addText('phone', 'Phone')
            ->setDefaultValue($defaults->getPhone());
        $form->addText('name', 'Name')
            ->setDefaultValue($defaults->getName());
        $form->addText('password', 'Password')
            ->setOption('description', 'Fill only if you want to change your current password.')
            ->addCondition(Form::FILLED)
            ->addRule(Form::MIN_LENGTH, 'Password must be at least %d characters long.', 6);
        $form->addTextArea('address', 'Address')
            ->setDefaultValue($defaults->getAddress());
        $form->addText('business_name', 'Business name')
            ->setDefaultValue($defaults->getBusinessName());
        $form->addText('ic', 'IC')->setDefaultValue($defaults->getIc());
        $form->addText('dic', 'DIC')->setDefaultValue($defaults->getDic());

        $form->addSubmit('process', 'Save');
        $form->onSuccess[] = $this->accountFormSucceeded;


        return BootstrapForm::makeBootstrap($form);
    }

    protected function createComponentOrders()
    {
        $orders = $this->ordersFactory->create();

        if ($this->action == 'default') {
            $data = $this->em->getRepository(Order::class)->findBy([
                'user' => $this->findUser(),
                'status' => Order::STATUS_PENDING
            ]);
        } else {
            $data = $this->em->getRepository(Order::class)->findBy([
                'user' => $this->findUser(),
            ]);
        }

        $orders->setOrders($data);

        return $orders;
    }
}
