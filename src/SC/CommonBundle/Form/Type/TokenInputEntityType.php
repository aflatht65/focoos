<?php

namespace SC\CommonBundle\Form\Type;

use SC\CommonBundle\Form\DataTransformer\CollectionToConcatIdTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TokenInputEntityType extends AbstractType
{
    protected $entityManager;
    protected $translator;

    public function __construct($entityManager, $translator) {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new CollectionToConcatIdTransformer($this->entityManager, $this->translator, $options['translatorPrefix'], $options['class'], $options['delimiter']);
        $builder->addModelTransformer($transformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'delimiter' => ',',
            'translatorPrefix' => '',
        ));
        $resolver->setRequired(array(
            'class',
        ));
    }

    public function getParent() {
        return 'text';
    }

    public function getName()
    {
        return 'token_input_entity';
    }
}