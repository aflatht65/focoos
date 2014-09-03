<?php
namespace SC\SiteBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DeadlineType extends PostType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('deadlineDate', null, array('label' => false, 'widget' => 'single_text', 'attr' => array('placeholder' => 'Επίλεξε την ημερομηνία...', 'data-date-format' => 'dd-mm-yyyy', 'class' => 'date')))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => 'SC\SiteBundle\Entity\Deadline',
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'deadline';
    }
}