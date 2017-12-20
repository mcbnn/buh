<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Asteriks
 *
 * @ORM\Table(name="BANK")
 * @ORM\Entity
 */
class BANK
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID_BANK", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idBank;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="D_OPERATION", type="date", nullable=false)
     */
    private $dOperation;


    /**
     * @var string
     *
     * @ORM\Column(name="CORESPOND", type="string", length=1000, nullable=false)
     */
    private $corespond;

    /**
     * @var string
     *
     * @ORM\Column(name="NUMDOC", type="string", length=255, nullable=false)
     */
    private $numDoc;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="D_DOC", type="date", nullable=false)
     */
    private $dDoc;

    /**
     * @var string
     *
     * @ORM\Column(name="CREDIT", type="string", length=50, nullable=false)
     */
    private $credit;

    /**
     * @var string
     *
     * @ORM\Column(name="COMMENT", type="string", length=1000, nullable=true)
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="INN_CORESPOND", type="string", length=12, nullable=true)
     */
    private $innCorespond;

    /**
     * @var string
     *
     * @ORM\Column(name="MD5", type="string", length=32, nullable=true)
     */
    private $md5;

    /**
     * @return int
     */
    public function getIdBank() {
        return $this->idBank;
    }

    /**
     * @param int $idBank
     */
    public function setIdBank($idBank) {
        $this->idBank = $idBank;
    }

    /**
     * @return \DateTime
     */
    public function getDOperation() {
        return $this->dOperation;
    }

    /**
     * @param \DateTime $dOperation
     */
    public function setDOperation($dOperation) {
        $this->dOperation = $dOperation;
    }

    /**
     * @return string
     */
    public function getCorespond() {
        return $this->corespond;
    }

    /**
     * @param string $corespond
     */
    public function setCorespond($corespond) {
        $this->corespond = $corespond;
    }

    /**
     * @return string
     */
    public function getNumDoc() {
        return $this->numDoc;
    }

    /**
     * @param string $numDoc
     */
    public function setNumDoc($numDoc) {
        $this->numDoc = $numDoc;
    }

    /**
     * @return \DateTime
     */
    public function getDDoc() {
        return $this->dDoc;
    }

    /**
     * @param \DateTime $dDoc
     */
    public function setDDoc($dDoc) {
        $this->dDoc = $dDoc;
    }

    /**
     * @return string
     */
    public function getCredit() {
        return $this->credit;
    }

    /**
     * @param string $credit
     */
    public function setCredit($credit) {
        $this->credit = $credit;
    }

    /**
     * @return string
     */
    public function getComment() {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment) {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getInnCorespond() {
        return $this->innCorespond;
    }

    /**
     * @param string $innCorespond
     */
    public function setInnCorespond($innCorespond) {
        $this->innCorespond = $innCorespond;
    }

    /**
     * @return string
     */
    public function getMd5() {
        return $this->md5;
    }

    /**
     * @param string $md5
     */
    public function setMd5($md5) {
        $this->md5 = $md5;
    }
    
    
}
