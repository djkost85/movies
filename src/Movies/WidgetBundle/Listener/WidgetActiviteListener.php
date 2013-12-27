<?php
namespace Movies\WidgetBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Movies\AdminBundle\Entity\WidgetActivite;
use Movies\MovieBundle\Entity\Movie;
use Movies\MovieBundle\Entity\Comment;
use Movies\MovieBundle\Entity\Actor;
use Movies\MovieBundle\Entity\Repository\MovieRepository;


class WidgetActiviteListener
{
	
	public function postLoad(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		$em = $args->getEntityManager();

		if ($entity instanceof WidgetActivite) {
			$actors = $em->getRepository('MoviesMovieBundle:ActorRepository')
						 ->findLast($entity->getLimit());
			$entity->setActors($actors);

			$movies = $em->getRepository('MoviesMovieBundle:MovieRepository')
						 ->findLast($entity->getLimit());
			$entity->setMovies($movies);

			$comments = $em->getRepository('MoviesMovieBundle:CommentRepository')
						 ->findLast($entity->getLimit());
			$entity->setComments($comments);			
		}
	}
}