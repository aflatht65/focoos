<?php
namespace SC\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        $builder
            ->add('message', null, array('label' => false, 'attr' => array('placeholder' => 'Γράψε το μήνυμα σου...')))
            ->add('lesson', null, array('label' => false, 'required' => true, 'query_builder' => function(EntityRepository $er) use ($user) {
              $lessonIds = array();
              foreach($user->getLessons() as $curLesson) {
                  $lessonIds[] = $curLesson->getId();
              }
              return $er->createQueryBuilder('l')
                        ->andWhere('l.id IN(:userLessons)')
                        ->setParameter('userLessons', $lessonIds);
              },))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setRequired(array(
            'user',
        ));
        $resolver->setDefaults(array(
            'data_class' => 'SC\SiteBundle\Entity\Post',
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'post';
    }
}