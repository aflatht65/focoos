<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SC\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;

class ProfileFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->remove('current_password');
    }

    public function buildUserForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array('label' => 'Όνομα', 'max_length' => 20, 'pattern' => '.{3,}', 'attr' => array('placeholder' => 'πχ. Αγγελική')))
            ->add('surname', null, array('label' => 'Επώνυμο', 'max_length' => 20, 'pattern' => '.{3,}', 'attr' => array('placeholder' => 'πχ. Φώκου')))
            ->add('email', 'email', array('label' => 'Email', 'attr' => array('placeholder' => 'πχ. test@test.com')))
            ->add('department', null, array('label' => 'Τμήμα', 'required' => true))
            ->add('newPhoto', 'file', array('label' => 'Φωτογραφία', 'required' => false, 'attr' => array('class' => 'tiny round disabled button')))
            ->add('lessons', 'token_input_entity', array('class' => 'SC\UserBundle\Entity\Lesson', 'label' => 'Μαθήματα', 'required' => true))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
        ));
    }

    public function getName()
    {
        return 'sc_user_profile';
    }
}
