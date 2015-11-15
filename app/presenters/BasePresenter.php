<?php

namespace App\Presenters;


use App\Model\User;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Presenter;
use Nette\Security\IUserStorage;

class BasePresenter extends Presenter
{
    /** @var EntityManager @inject */
    public $em;

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

    /**
     * @return null|User
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    protected function findUser()
    {
        if ($this->user->isLoggedIn()) {
            return $this->em->find(User::class, $this->user->id);
        } else {
            return null;
        }
    }
}