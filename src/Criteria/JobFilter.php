<?php
/**
 * Created by PhpStorm.
 * User: paulius
 * Date: 5/18/18
 * Time: 3:25 PM
 */

namespace App\Criteria;

use Symfony\Component\Form\Form;

class JobFilter extends OrderFilter
{
    public function __construct(Form $form)
    {
        $this->completeness = (int)$form->getData()['completeness'];
        $this->orderby = $form->getData()['orderby'];
        if(!$this->orderby){ $this->orderby = 'status'; }
        $this->sortorder = $form->getData()['sortorder'];
        if(!$this->sortorder){ $this->sortorder = 'DESC'; }
    }
}