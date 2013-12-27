<?php

namespace Movies\MovieBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ActorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ActorRepository extends EntityRepository
{

	public function getLastActors($limit)
	{
		return $this->_em
				    ->createQueryBuilder('a')
				    ->select('a')
				    ->from('MoviesMovieBundle:Actor', 'a')
				    ->orderBy('a.id', 'desc')
				    ->setMaxResults($limit)
				    ->getQuery()
				    ->getResult();
	}
}