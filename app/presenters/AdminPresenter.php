<?php

namespace App\Presenters;


use App\Model\Settings;
use App\Model\User;
use App\Utils\BootstrapForm;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Nette\Application\UI\Form;

class AdminPresenter extends BasePresenter
{
    function renderUsers()
    {
        $this->template->users = $this->em->getRepository(User::class)->findAll();
    }

    function userFormSucceeded(Form $form, $values)
    {
        try {
            $user = new User($values->email, $values->password, $values->role);
            $this->em->persist($user);
            $this->em->flush();

            $this->flashMessage('User has been created.', 'success');
            $this->redirect('this');
        } catch (UniqueConstraintViolationException $e) {
            $form->addError('User with this e-mail already exists.');
        }
    }

    function settingsFormSucceeded(Form $form, $values)
    {
        Settings::set('helium.price_per_unit', $values->helium_price);
        Settings::set('nitrogen.price_per_unit', $values->nitrogen_price);
        $this->flashMessage('Settings saved.', 'info');
        $this->redirect('this');
    }

    protected function createComponentSettingsForm()
    {
        $form = new Form();

        $form->addText('helium_price', 'Helium price')
            ->setOption('description', 'Kč per unit')
            ->addRule(Form::INTEGER)
            ->setRequired()
            ->setDefaultValue(Settings::get('helium.price_per_unit'));
        $form->addText('nitrogen_price', 'Nitrogen price')
            ->setOption('description', 'Kč per unit')
            ->addRule(Form::INTEGER)
            ->setRequired()
            ->setDefaultValue(Settings::get('nitrogen.price_per_unit'));
        $form->addSubmit('proces', 'Save');

        $form->onSuccess[] = $this->settingsFormSucceeded;


        return BootstrapForm::makeBootstrap($form);
    }

    protected function createComponentUserForm()
    {
        $form = new Form();

        $form->addText('email', 'E-mail')
            ->addRule(Form::EMAIL)->setRequired();
        $form->addSelect('role', 'Role', [
            User::ROLE_USER => 'User',
            User::ROLE_KRYO => 'Kryo',
            User::ROLE_ADMIN => 'Admin'
        ])->setRequired();
        $form->addPassword('password', 'Password')
            ->addRule(Form::MIN_LENGTH, 'Password must be at least %d characters long.', 6)
            ->setRequired();
        $form->addPassword('password2', 'Password again')
            ->addRule(Form::EQUAL, 'Passwords are not the same.', $form['password'])
            ->setOmitted();
        $form->addSubmit('process', 'Create a new user');

        $form->onSuccess[] = $this->userFormSucceeded;

        return BootstrapForm::makeBootstrap($form);
    }
}