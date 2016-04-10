<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('query', 'text');
        $builder->setMethod('GET');
    }

    public function getName(){
        return null;
    }

    public function getQuery()
    {
        return 'query';
    }
}