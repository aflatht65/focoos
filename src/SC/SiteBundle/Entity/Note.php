<?php
namespace SC\SiteBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table()
 * @ORM\Entity()
 * @Gedmo\Uploadable(
 *  pathMethod="getRootPath",
 *  filenameGenerator="SHA1",
 *  allowedTypes="image/jpeg,image/pjpeg,image/png,image/x-png"
 * )
 */
class Note extends Post
{
    /**
     * @ORM\Column(name="path", type="string")
     * @Gedmo\UploadableFilePath
     */
    private $path;
    /**
     * @Assert\File(
     *     mimeTypes={"image/jpeg", "image/pjpeg", "image/png", "image/x-png"}
     * )
     */
    private $note;

    private $noteUrl;

    /**
     * @ORM\Column(name="mime_type", type="string")
     * @Gedmo\UploadableFileMimeType
     */
    private $mimeType;
    /**
     * @ORM\Column(name="size", type="decimal")
     * @Gedmo\UploadableFileSize
     */
    private $size;

    public function getRootPath() {
        $path = __DIR__.'/../../../../web/upload/note';
        return $path;
    }

    public function getWebPath() {
        return AVATAR_BASE_PATH.'upload/note';
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getPath() {
        return $this->path;
    }

    public function setPath($path) {
        $this->path = $path;
    }

    public function getPathObject() {
        return new Image($this->getImagePath());
    }

    public function getImagePath() {
        if($this->getPath() != null) {
            return $this->getWebPath().'/'.basename($this->getPath());
        } else {
            return 'images/p36.png';
        }
    }

    public function getNote() {
        return $this->note;
    }

    public function setNote($note) {
        $this->note = $note;
    }

    public function getNoteUrl() {
        return $this->noteUrl;
    }

    public function setNoteUrl($noteUrl) {
        $this->noteUrl = $noteUrl;
    }

    public function getMimeType() {
        return $this->mimeType;
    }

    public function setMimeType($mimeType) {
        $this->mimeType = $mimeType;
    }

    public function getSize() {
        return $this->size;
    }

    public function setSize($size) {
        $this->size = $size;
    }
}
