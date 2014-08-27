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
 *  filenameGenerator="ALPHANUMERIC"
 * )
 */
class Exercise extends Post
{
    /**
     * @ORM\Column(name="path", type="string")
     * @Gedmo\UploadableFilePath
     */
    private $path;
    /**
     * @Assert\File()
     */
    private $exercise;

    private $exerciseUrl;

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
        $path = __DIR__.'/../../../../web/upload/exercise';
        return $path;
    }

    public function getWebPath() {
        return AVATAR_BASE_PATH.'upload/exercise';
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

    public function getBasename() {
        return basename($this->getPath());
    }

    public function getPathObject() {
        return new Image($this->getImagePath());
    }

    public function getImagePath() {
        if($this->getPath() != null) {
            return $this->getWebPath().'/'.basename($this->getPath());
        }
        return null;
    }

    public function getExercise() {
        return $this->exercise;
    }

    public function setExercise($exercise) {
        $this->exercise = $exercise;
    }

    public function getExerciseUrl() {
        return $this->exerciseUrl;
    }

    public function setExerciseUrl($exerciseUrl) {
        $this->exerciseUrl = $exerciseUrl;
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

    public function getType() {
        return 'exercise';
    }
}
