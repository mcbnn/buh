<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BuhSchet
 *
 * @ORM\Table(name="buh_schet")
 * @ORM\Entity
 */
class BuhSchet
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_schet", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idSchet;


    /**
     * @var string
     * @ORM\Column(name="schet", type="string", length=100, nullable=false)
     */
    private $schet;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="del", type="datetime", nullable=true)
     */


    private $del;


    /**
     * @return \DateTime
     */

    public function getDel() {
        return $this->del;
    }



    /**
     * @param \DateTime $del
     */
    public function setDel($del) {
        $this->del = $del;
    }

    /**
     * @var \AppBundle\Entity\BuhDirection
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BuhDirection")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_direction", referencedColumnName="id_direction")
     * })
     */
    private $idDirection;

    /**
     * @return int
     */
    public function getIdSchet() {
        return $this->idSchet;
    }

    /**
     * @param int $idSchet
     */
    public function setIdSchet($idSchet) {
        $this->idSchet = $idSchet;
    }

    /**
     * @return string
     */
    public function getSchet() {
        return $this->schet;
    }

    /**
     * @param string $schet
     */
    public function setSchet($schet) {
        $this->schet = $schet;
    }

    /**
     * @return BuhDirection
     */
    public function getIdDirection() {
        return $this->idDirection;
    }

    /**
     * @param BuhDirection $idDirection
     */
    public function setIdDirection($idDirection) {
        $this->idDirection = $idDirection;
    }
}
