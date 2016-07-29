<?php

namespace CmsBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PartenaireRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PartenaireRepository extends EntityRepository
{
    public function getIdItemPartenaire()
        {
            return $this->getEntityManager()
                ->createQuery(
                    'SELECT MAX(a.item_id) FROM CmsBundle:Partenaire a'
                )
                ->getResult();
        }
}
