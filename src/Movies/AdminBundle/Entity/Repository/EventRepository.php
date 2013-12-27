<?php

namespace Movies\AdminBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * EventRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EventRepository extends EntityRepository
{

	function getEvents($month,$year) {
		$date_debut = $year.'-'.$month.'-01';
		$nbJours = intval(date("t",$month));
		$date_fin = $year.'-'.$month.'-'.$nbJours;
		return $this->_em
					->createQueryBuilder('e')
					->select('e.title, DAYOFMONTH(e.dateEvent) as day')
					->from('MoviesAdminBundle:Event', 'e')
					->where('e.dateEvent >= :date_debut')
					->setParameter('date_debut', $date_debut)
					->andWhere('e.dateEvent <= :date_fin')
					->setParameter('date_fin', $date_fin)
					->getQuery()
					->getArrayResult();
	}
}