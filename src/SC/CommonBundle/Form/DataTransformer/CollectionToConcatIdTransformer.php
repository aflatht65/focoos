<?php
namespace SC\CommonBundle\Form\DataTransformer;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Translation\TranslatorInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CollectionToConcatIdTransformer implements DataTransformerInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $om;
    protected $translator;
    protected $translatorPrefix;
    protected $entityClass;
    protected $delimiter;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, TranslatorInterface $translator, $translatorPrefix, $entityClass, $delimiter)
    {
        $this->om = $om;
        $this->translator = $translator;
        $this->translatorPrefix = $translatorPrefix;
        $this->entityClass = $entityClass;
        $this->delimiter = $delimiter;
    }

    /**
     * @param mixed $entity
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     *
     * @return integer
     */
    public function transform($collection)
    {
        $ids = array();
        if($collection instanceof Collection) {
            foreach($collection as $curElement) {
                $element = array();
                $element['id'] = $curElement->getId();
                $tprefix = $this->translatorPrefix != '' ? $this->translatorPrefix.'.' : '';
                $element['name'] = $this->translator->trans($tprefix.$curElement->getName());
                $ids[] = $element;
            }
        }
        //throw new TransformationFailedException("$this->entityType object must be provided");
        return (count($ids) > 0 ? json_encode($ids) : '');
    }

    /**
     * @param mixed $id
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     *
     * @return mixed|object
     */
    public function reverseTransform($concatIds)
    {
        if($concatIds != '') {
            $ids = explode($this->delimiter, $concatIds);
        } else {
            $ids = array();
        }
        $collection = new ArrayCollection();
        foreach($ids as $id) {
            $entity = $this->om->getRepository($this->entityClass)->findOneBy(array("id" => $id));
            if (null === $entity) {
                throw new TransformationFailedException(sprintf(
                    'A %s with id "%s" does not exist!',
                    $this->entityType,
                    $id
                ));
            }
            if(!$collection->contains($entity)) { // Prevent duplicates
                $collection->add($entity);
            }
        }

        return $collection;
    }

}