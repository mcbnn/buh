<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BuhHistrory
 *
 * @ORM\Table(name="buh_history")
 * @ORM\Entity
 */
class BuhHistory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_historys", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idHistory;

    /**
     * @var integer
     * @ORM\Column(name="id_user", type="integer", nullable=false)
     */
    private $idUser;

    /**
     * @var string
     *
     * @ORM\Column(name="file", type="string", length=500, nullable=false)
     */
    private $file;

    /**
     * @return int
     */

    /**
     * @var string
     * @Assert\File(mimeTypes={ "text/plain" })
     */
    public $attachment;

    public function getIdHistory() {
        return $this->idHistory;
    }

    /**
     * @param int $idHistory
     */
    public function setIdHistory($idHistory) {
        $this->idHistory = $idHistory;
    }

    /**
     * @return int
     */
    public function getIdUser() {
        return $this->idUser;
    }

    /**
     * @param int $idUser
     */
    public function setIdUser($idUser) {
        $this->idUser = $idUser;
    }

    /**
     * @return string
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * @param string $file
     */
    public function setFile($file) {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getAttachment()
    {
        return $this->attachment;
    }


}
