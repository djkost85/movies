<?php

namespace Movies\MovieBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ActorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StatsRepository extends EntityRepository
{

	public function getStats()
	{
		return $this->_em
				    ->createQueryBuilder('a,m,c')
				    ->select('count(m) as nbMovies, count(m.duration) as nbMinutesMovie, count(a) as nbActors, count(c) as nbComments')
				    ->from('MoviesMovieBundle:Actor', 'a')
				    ->from('MoviesMovieBundle:Movie', 'm')
				    ->from('MoviesMovieBundle:Comment', 'c')
				    ->getQuery()
				    ->getSingleResult();
	}
}
