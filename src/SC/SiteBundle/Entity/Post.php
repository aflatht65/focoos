<?php
namespace SC\SiteBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="SC\SiteBundle\Entity\Repositories\PostsRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *  "post" = "Post",
 *  "deadline" = "Deadline",
 *  "exercise" = "Exercise",
 *  "note" = "Note"
 * })
 */
class Post
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="SC\UserBundle\Entity\User", inversedBy="posts", cascade={"persist"})
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id", onDelete="RESTRICT")
     */
    protected $student;
    /**
     * @ORM\ManyToOne(targetEntity="SC\UserBundle\Entity\Lesson", cascade={"persist"})
     * @ORM\JoinColumn(name="lesson_id", referencedColumnName="id", onDelete="RESTRICT")
     */
    protected $lesson;
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $message;
    /**
     * @ORM\OneToMany(targetEntity="SC\SiteBundle\Entity\Comment", mappedBy="post")
     */
    protected $comments;

    public function __construct() {
        $this->comments = new ArrayCollection();
    }

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

    public function getLesson() {
        return $this->lesson;
    }

    public function setLesson($lesson) {
        $this->lesson = $lesson;
    }

    public function getMessage() {
        return $this->message;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function getComments() {
        return $this->comments;
    }

    public function setComments($comments) {
        $this->comments = $comments;
    }

    public function getType() {
        return 'post';
    }
}
