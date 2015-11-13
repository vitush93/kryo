<?php

namespace App\Presenters;


use App\Model\User;
use Nette\Application\UI\Presenter;
use Nette\Security\IUserStorage;

class BasePresenter extends Presenter
{
    protected function startup()
    {
        parent::startup();

        if ($this->name !== 'Sign') {
            if (!$this->user->isLoggedIn()) {
                if ($this->user->getLogoutReason() === IUserStorage::INACTIVITY) {
                    $this->flashMessage('You have been automatically logged out (20 minutes).', 'info');
                } else {
                    $this->flashMessage('Please login first.', 'info');
                }

                $this->redirect('Sign:default');
            } else {
                if (!$this->user->isAllowed($this->name)) {
                    $this->flashMessage('Access denied.', 'danger');
                    $this->user->logout();

                    $this->redirect('Sign:default');
                }
            }
        } else {
            if ($this->user->isLoggedIn()) {
                if ($this->user->isInRole(User::ROLE_ADMIN)) {
                    $this->redirect('Homepage:default');
                }
            }
        }
    }
}