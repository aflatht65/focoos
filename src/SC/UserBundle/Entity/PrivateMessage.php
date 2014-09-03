<?php
namespace SC\UserBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="SC\UserBundle\Entity\Repositories\PrivateMessageRepository")
 */
class PrivateMessage
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="SC\UserBundle\Entity\User", inversedBy="sentMessages", cascade={"persist"})
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id", onDelete="RESTRICT")
     */
    protected $sender;
    /**
     * @ORM\ManyToOne(targetEntity="SC\UserBundle\Entity\User", inversedBy="receivedMessages", cascade={"persist"})
     * @ORM\JoinColumn(name="receiver_id", referencedColumnName="id", onDelete="RESTRICT")
     */
    protected $receiver;
    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isRead = false;
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

    public function getSender() {
        return $this->sender;
    }

    public function setSender($sender) {
        $this->sender = $sender;
    }

    public function getReceiver() {
        return $this->receiver;
    }

    public function setReceiver($receiver) {
        $this->receiver = $receiver;
    }

    public function getIsRead() {
        return $this->isRead;
    }

    public function setIsRead($isRead) {
        $this->isRead = $isRead;
    }

    public function getMessage() {
        return $this->message;
    }

    public function setMessage($message) {
        $this->message = $message;
    }
}
