<?php
namespace SC\SiteBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class Deadline extends Post
{
    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $deadlineDate;

    public function getDeadlineDate() {
        return $this->deadlineDate;
    }

    public function setDeadlineDate($deadlineDate) {
        $this->deadlineDate = $deadlineDate;
    }

    public function getType() {
        return 'deadline';
    }
}
