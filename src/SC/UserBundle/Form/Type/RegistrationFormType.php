<?php

namespace SC\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->remove('username')
            ->remove('plainPassword')
            ->remove('email')
            ->add('name', null, array('label' => 'Όνομα', 'max_length' => 20, 'pattern' => '.{3,}', 'attr' => array('placeholder' => 'πχ. Αγγελική')))
            ->add('surname', null, array('label' => 'Επώνυμο', 'max_length' => 20, 'pattern' => '.{3,}', 'attr' => array('placeholder' => 'πχ. Φώκου')))
            ->add('email', 'email', array('label' => 'Email', 'attr' => array('placeholder' => 'πχ. test@test.com')))
            ->add('plainPassword', 'password', array(
                'translation_domain' => 'FOSUserBundle',
                'label' => 'Κωδικός Πρόσβασης',
                'invalid_message' => 'fos_user.password.mismatch',
                'pattern' => '.{5,}',
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'cascade_validation' => true,
        ));
    }

    public function getName()
    {
        return 'sc_user_registration';
    }
}