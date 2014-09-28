<?php
namespace SC\UserBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use FOS\UserBundle\Entity\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

use SC\CommonBundle\Entity\Photo;

/**
 * @ORM\Entity
 * @ORM\Table(name="Users")
 * @ORM\Entity(repositoryClass="SC\UserBundle\Entity\Repositories\UserRepository")
 */
class User extends BaseUser
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $surname;
    /**
     * @ORM\ManyToOne(targetEntity="SC\CommonBundle\Entity\Photo")
     * @ORM\JoinColumn(name="photo", referencedColumnName="id", onDelete="SET NULL")
     * @var Photo
     */
    protected $photo;
    /**
     * @Assert\File(
     *     mimeTypes={"image/jpeg", "image/pjpeg", "image/png", "image/x-png"}
     * )
     */
    protected $newPhoto;
    /**
     * @ORM\ManyToMany(targetEntity="SC\UserBundle\Entity\Lesson", cascade={"persist"})
     * @ORM\JoinTable(name="students_lessons",
     *      joinColumns={@ORM\JoinColumn(name="lesson_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="student_id", referencedColumnName="id")}
     *      )
     */
    protected $lessons;
    /**
     * @ORM\ManyToOne(targetEntity="SC\UserBundle\Entity\Department", cascade={"persist"})
     * @ORM\JoinColumn(name="department_id", referencedColumnName="id", onDelete="RESTRICT")
     */
    protected $department;
    /**
     * @ORM\OneToMany(targetEntity="SC\SiteBundle\Entity\Post", mappedBy="student")
     */
    protected $posts;
    /**
     * @ORM\OneToMany(targetEntity="SC\UserBundle\Entity\PrivateMessage", mappedBy="sender")
     */
    protected $sentMessages;
    /**
     * @ORM\OneToMany(targetEntity="SC\UserBundle\Entity\PrivateMessage", mappedBy="receiver")
     */
    protected $receivedMessages;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $fbId;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $fbEmail;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $fbAccessToken;

    public function __construct() {
        parent::__construct();
        $this->lessons = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->sentMessages = new ArrayCollection();
        $this->receivedMessages = new ArrayCollection();
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getSurname() {
        return $this->surname;
    }

    public function setSurname($surname) {
        $this->surname = $surname;
    }

    public function getPhoto() {
        if(!isset($this->photo)) { $this->photo = new Photo(); }
        return $this->photo;
    }

    public function setPhoto(Photo $photo) {
        $this->photo = $photo;
    }

    public function getAvatarUrl() {
        return $this->getPhoto()->getImagePath();
    }

    public function getNewPhoto() {
        return $this->newPhoto;
    }

    public function setNewPhoto($newPhoto) {
        $this->newPhoto = $newPhoto;
    }

    public function getLessons() {
        return $this->lessons;
    }

    public function setLessons($lessons) {
        $this->lessons = $lessons;
    }

    public function getAllLessons() {
        global $kernel;
        return $kernel->getContainer()->get('doctrine')->getRepository('SC\UserBundle\Entity\Lesson')->findAll();
    }

    public function getDepartment() {
        return $this->department;
    }

    public function setDepartment($department) {
        $this->department = $department;
    }

    public function getPosts() {
        return $this->posts;
    }

    public function setPosts($posts) {
        $this->posts = $posts;
    }

    public function getSentMessages() {
        return $this->sentMessages;
    }

    public function setSentMessages($sentMessages) {
        $this->sentMessages = $sentMessages;
    }

    public function getReceivedMessages() {
        return $this->receivedMessages;
    }

    public function setReceivedMessages($receivedMessages) {
        $this->receivedMessages = $receivedMessages;
    }

    public function getFbId() {
        return $this->fbId;
    }

    public function setFbId($fbId) {
        $this->fbId = $fbId;
    }

    public function getFbEmail() {
        return $this->fbEmail;
    }

    public function setFbEmail($fbEmail) {
        $this->fbEmail = $fbEmail;
    }

    public function getFbAccessToken() {
        return $this->fbAccessToken;
    }

    public function setFbAccessToken($fbAccessToken) {
        $this->fbAccessToken = $fbAccessToken;
    }

    public function getExpiresAt() {
        return $this->expiresAt;
    }

    public function getCredentialsExpireAt() {
        return $this->credentialsExpireAt;
    }
}