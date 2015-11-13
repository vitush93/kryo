<?php

namespace App\Utils;


use Nette\Application\UI\Form;
use Nette\Forms\Controls\Button;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Controls\CheckboxList;
use Nette\Forms\Controls\MultiSelectBox;
use Nette\Forms\Controls\RadioList;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\TextBase;

class BootstrapForm
{

    /**
     * @param Form $form
     * @return Form
     */
    public static function makeBootstrap(Form $form)
    {
        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = null;
        $renderer->wrappers['pair']['container'] = 'div class=form-group';
        $renderer->wrappers['pair']['.error'] = 'has-error';
        $renderer->wrappers['control']['container'] = 'div class=col-sm-9';
        $renderer->wrappers['label']['container'] = 'div class="col-sm-3 control-label"';
        $renderer->wrappers['control']['description'] = 'span class=help-block';
        $renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';

        $form->getElementPrototype()->class('form-horizontal');

        foreach ($form->getControls() as $control) {
            if ($control instanceof Button) {
                $control->setAttribute('class', empty($usedPrimary) ? 'btn btn-primary' : 'btn btn-default');
                $usedPrimary = true;

            } elseif ($control instanceof TextBase || $control instanceof SelectBox
                || $control instanceof MultiSelectBox
            ) {
                $control->setAttribute('class', 'form-control');

            } elseif ($control instanceof Checkbox || $control instanceof CheckboxList
                || $control instanceof RadioList
            ) {
                $control->getSeparatorPrototype()->setName('div')->class($control->getControlPrototype()->type);
            }
        }

        return $form;
    }
}