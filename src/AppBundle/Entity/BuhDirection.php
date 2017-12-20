<?php

namespace AppBundle\Entity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BuhDirection
 *
 * @ORM\Table(name="buh_direction")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BuhDirectionRepository")
 * @UniqueEntity(
 *     fields={"firebird"},
 *     message="Подключение уникально"
 * )
 */
class BuhDirection
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_direction", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idDirection;


    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=100, nullable=false)
     */
    private $alias;

    /**
     * @var string
     *
     * @ORM\Column(name="full_name", type="string", length=500, nullable=false)
     */
    private $fullName;

    /**
     * @var string
     *
     * @ORM\Column(name="short_name", type="string", length=500, nullable=false)
     */
    private $shortName;

    /**
     * @var string
     *
     * @ORM\Column(name="firebird", type="string", length=100, nullable=true)
     */
    private $firebird;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BuhSchet", mappedBy="idDirection")
     */
    private $schet;

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSchet() {
        return $this->schet;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $schet
     */
    public function setSchet($schet) {
        $this->schet = $schet;
    }

    /**
     * @return int
     */
    public function getIdDirection() {
        return $this->idDirection;
    }

    /**
     * @param int $idDirection
     */
    public function setIdDirection($idDirection) {
        $this->idDirection = $idDirection;
    }

    /**
     * @return string
     */
    public function getAlias() {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias) {
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getFullName() {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     */
    public function setFullName($fullName) {
        $this->fullName = $fullName;
    }

    /**
     * @return string
     */
    public function getShortName() {
        return $this->shortName;
    }

    /**
     * @param string $shortName
     */
    public function setShortName($shortName) {
        $this->shortName = $shortName;
    }

    /**
     * @return string
     */
    public function getFirebird() {
        return $this->firebird;
    }

    /**
     * @param string $firebird
     */
    public function setFirebird($firebird) {
        $this->firebird = $firebird;
    }

}
