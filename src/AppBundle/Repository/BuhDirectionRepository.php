<?php

namespace AppBundle\Repository;

/**
 * AuthorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BuhDirectionRepository extends \Doctrine\ORM\EntityRepository
{
    public function getBuhDirection()
    {
        return $this->createQueryBuilder('buhDirection')
            ->orderBy('buhDirection.idDirection', 'ASC');
    }
}
