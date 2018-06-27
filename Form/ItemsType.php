<?php

namespace gsiwerd\MyBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use gsiwerd\MyBundle\Entity\Items;

class ItemsType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
    ->setAction($options['action'])
    ->setMethod($options['method'])
    ->add('name', TextType::class, array('label' => 'Nazwa'))
    ->add('amount', TextType::class, array('label' => 'Liczba'))
    ->add('save', SubmitType::class, array('label' => $options['label']));
  }
}
