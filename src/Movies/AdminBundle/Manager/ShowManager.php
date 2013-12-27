<?php
namespace Movies\AdminBundle\Manager;

use Serie\SerieBundle\Entity\Serie;
use Serie\SerieBundle\Entity\Actor;
use Serie\SerieBundle\Entity\Genre;

class ShowManager 
{
	
	/**
	 * Récupère les informations d'une série et 
	 * renvoie un objet serie initialisé
	 */
	public static function insertShow($id, $container) {
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://api.themoviedb.org/3/tv/$id?api_key=f8c98b3fcc23cbee0fd7ec65eaaf9e21&append_to_response=credits");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json"));
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response);
        $em = $container->getDoctrine()->getManager();

        $serie = new Serie();
        $serie->setTitle($response->name);
        $serie->setAlias(self::prepareAlias($serie->getTitle()));
        $serie->setNbSeasons($response->number_of_seasons);
        $serie->setNbEpisodes($response->number_of_episodes);
        $serie->setStartDate(new \DateTime($response->first_air_date));
        if($response->in_production)
        	$serie->setEndDate(new \DateTime('1893-01-01'));
        else
        	$serie->setEndDate(new \DateTime($response->last_air_date));
        $serie->setChannel($response->networks[0]->name);
        $serie->setNationality($response->origin_country[0]);
        foreach($response->genres as $g) {
        	$genre = $container->getDoctrine()->getRepository('SerieSerieBundle:Genre')->findBy(array('title' => $g->name));
        	if(!$genre) {
        		$genre = new Genre();
        		$genre->setTitle($g->name);
        		$genre->setAlias(self::prepareAlias($g->name));

        	} else {
        		$genre = current($genre);
        	}

        	$serie->addGenre($genre);	
        	$em->persist($genre);
        }


        
        /*foreach($response->credits->cast as $a) {
        	$actor = self::getActor($a->id);
        	$serie->addActor($actor);
        	$actor->addSerie($serie);
        	$em->persist($actor);
        }*/
        $em->persist($serie);
        $em->flush();

		return $serie;
	}

	public static function insertActor($id, $container, $id_serie) {
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://api.themoviedb.org/3/person/$id?api_key=f8c98b3fcc23cbee0fd7ec65eaaf9e21");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json"));
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response);
        $show = $container->getDoctrine()->getRepository('SerieSerieBundle:Serie')->find($id_serie);
        $actor = new Actor();
        $actor->setName($response->name);
        $actor->setBirthdayDate(new \DateTime($response->birthday));
        $actor->setResume($response->biography);
        $em = $container->getDoctrine()->getManager();
        $show->addActor($actor);
        $actor->addSerie($show);
        $em->persist($actor);
        $em->persist($show);
        $em->flush();
        return $actor;
	}

	public static function prepareAlias($title)
	{
		$alias = str_replace(' ', '-', $title);
		$alias = strtolower($alias);
		$alias = str_replace("'",'-', $alias);
	}
}