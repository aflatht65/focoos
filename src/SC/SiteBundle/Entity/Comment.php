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
class Comment
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="SC\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id", onDelete="RESTRICT")
     */
    protected $student;
    /**
     * @ORM\ManyToOne(targetEntity="SC\SiteBundle\Entity\Post", inversedBy="comments", cascade={"persist"})
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", onDelete="RESTRICT")
     */
    protected $post;
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $message;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getStudent() {
        return $this->student;
    }

    public function setStudent($student) {
        $this->student = $student;
    }

    public function getPost() {
        return $this->post;
    }

    public function setPost($post) {
        $this->post = $post;
    }

    public function getMessage() {
        return $this->message;
    }

    public function setMessage($message) {
        $this->message = $message;
    }
}
