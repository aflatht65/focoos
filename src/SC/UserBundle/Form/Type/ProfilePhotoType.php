<?php
namespace SC\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfilePhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('newPhoto', 'file', array('label' => 'Φωτογραφία', 'required' => false, 'attr' => array('class' => 'tiny round disabled button')));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'data_class' => 'SC\UserBundle\Entity\User',
        ));
    }

    public function getName()
    {
        return 'sc_user_photo';
    }
}
