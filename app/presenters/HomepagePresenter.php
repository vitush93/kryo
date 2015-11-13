<?php

namespace App\Presenters;


use App\Utils\BootstrapForm;
use Nette\Application\UI\Form;

class HomepagePresenter extends BasePresenter
{
    function orderFormSucceeded(Form $form, $values)
    {
        dump($values);
        die;
    }

    protected function createComponentOrderForm()
    {
        $form = new Form();

        $form->addSelect('type', 'Type', [
            'nitrogen' => 'Nitrogen',
            'helium' => 'Helium'
        ])->setRequired();
        $form->addSubmit('process', 'Create order');

        $form->onSuccess[] = $this->orderFormSucceeded;

        return BootstrapForm::makeBootstrap($form);
    }
}
