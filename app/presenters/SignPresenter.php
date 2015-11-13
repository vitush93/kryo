<?php

namespace App\Presenters;

use App\Utils\BootstrapForm;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;

class SignPresenter extends BasePresenter
{
    function actionOut()
    {
        $this->user->logout();

        $this->flashMessage('You have been logged out.', 'info');
        $this->redirect('default');
    }

    function signFormSucceeded(Form $form, $values)
    {
        if ($values->remember) {
            $this->getUser()->setExpiration('14 days', false);
        } else {
            $this->getUser()->setExpiration('20 minutes', true);
        }

        try {
            $this->getUser()->login($values->email, $values->password);

            $this->flashMessage('You have been successfully logged in.', 'success');
            $this->redirect('Homepage:default');
        } catch (AuthenticationException $e) {
            $this->flashMessage($e->getMessage(), 'danger');
        }
    }

    protected function createComponentSignForm()
    {
        $form = new Form();

        $form->addText('email', 'E-mail')
            ->addRule(Form::EMAIL)
            ->setRequired();
        $form->addPassword('password', 'Password')
            ->setRequired();
        $form->addCheckbox('remember', 'Remember me')
            ->setDefaultValue(true);
        $form->addSubmit('process', 'Login');

        $form->onSuccess[] = $this->signFormSucceeded;

        return BootstrapForm::makeBootstrap($form);
    }
}