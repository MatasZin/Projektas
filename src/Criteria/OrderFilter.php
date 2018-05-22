<?php

namespace App\Criteria;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class OrderFilter
{
    protected $completeness;

    protected $orderby;

    protected $sortorder;

    public function __construct(Form $form)
    {
        $this->completeness = (int)$form->getData()['completeness'];
        //if($this->completeness){ $this->completeness =  }
        $this->orderby = $form->getData()['orderby'];
        if(!$this->orderby){ $this->orderby = 'orderDate'; }
        $this->sortorder = $form->getData()['sortorder'];
        if(!$this->sortorder){ $this->sortorder = 'ASC'; }
    }

    /**
     * @return int
     */
    public function getCompleteness(): int
    {
        return $this->completeness;
    }

    /**
     * @return string
     */
    public function getOrderby(): string
    {
        return $this->orderby;
    }

    /**
     * @return string
     */
    public function getSortorder(): string
    {
        return $this->sortorder;
    }


}