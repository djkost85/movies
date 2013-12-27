<?php
namespace Movies\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Movies\AdminBundle\Form\MovieSearchType;
use Movies\MovieBundle\Entity\Movie;
use Movies\MovieBundle\Entity\Language;
use Movies\MovieBundle\Entity\Actor;
use Movies\MovieBundle\Entity\Genre;
use Movies\MovieBundle\Classes\IMDbMovie;
use Movies\MovieBundle\Classes\IMDbPerson;


class MovieController extends Controller
{
	protected $title = 'Movies';

    /**
     * @Route("/movies", name="_admin_movies")
     * @Template("::index.html.twig")
     */
    public function indexAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
    	$movie = new Movie();
    	$form = $this->createForm(new MovieSearchType(), $movie);
    	$query = $this->getDoctrine()->getRepository('MoviesMovieBundle:Movie')->getMoviesQuery();

    	if ($request->isMethod('POST')) {
    		$data = $request->request->get('movieSearch');
    		$url = $data['title'];
	        $imdb = new IMDbMovie($url);
	        $imdbMovie = $imdb->getMainDetails();
	        $imdbMovie = json_decode($imdbMovie);
	        //echo '<pre>'.print_r($imdbMovie->data->image,true).'</pre>'; die;
	        $movie->setTitle($imdbMovie->data->title);
	        $movie->setAlias($this->prepareAlias($imdbMovie->data->title));
	        $movie->setResume($imdbMovie->data->plot->outline);
	        $movie->setDuration($data['duration']);
	        $movie->setYear($imdbMovie->data->year);
	        $actors = $imdbMovie->data->cast_summary;
	        foreach ($actors as $imdbActor) {
	        	$actor = $this->_addActor($imdbActor);
	        	$movie->addCast($actor);
	        	$actor->addMovie($movie);
	        	$em->persist($actor);
	        }

	        foreach ($imdbMovie->data->genres as $genreMovie) {
	        	$genre = $this->_addGenre($genreMovie);
	        	$movie->addGenre($genre);
	        	$genre->addMovie($movie);
	        	$em->persist($genre);
	        }
	        $dir = $movie->getUploadRootDir();
	        $i=1;
	        $images = array();
		    if (isset($imdbMovie->data->photos)) {
		        foreach ($imdbMovie->data->photos as $image) {
		        	$filename = $movie->getAlias().'-'.$i.'.jpg';
		    		$this->_getImageFromUrl($imdbMovie->data->photos[($i-1)]->image->url, $dir.'/', $filename);
		    		$images[] = $filename;
		        	$i++;
		        }
		    }
	        $movie->setImages($images);

	        $filename = $movie->getAlias().'.jpg';
    		$this->_getImageFromUrl($imdbMovie->data->image->url, $dir.'/', $filename);
    		$movie->setFeature($filename);

	        $language = $em->getRepository('MoviesMovieBundle:Language')->find(1);
	        $movie->setLanguage($language);
	        $movie->setQuality('1080p');
	        $movie->setNote(0);
	        $language->addMovie($movie);
	        $em->persist($movie);
	        $em->flush();
    	}		 
    	
    	$columns = $movie->getColumns();			  

    	$nb_elem = 10;

    	/*$paginator  = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
				$query,
				$page,
				$nb_elem
		);*/


        return array('title' => $this->title,
        			 'columns' => $columns, 
        			 'datas' => $query->getResult(), 
        			 //'pagination' => $pagination,
        			 'form' => $form->createView());
        
    }

    private function _addActor($actorMovie)
    {
    	$em = $this->getDoctrine()->getManager();
    	$actor = $em->getRepository('MoviesMovieBundle:Actor')->findBy(array('name' => $actorMovie->name->name));
    	if(count($actor) == 1) {
    		$actor = current($actor);
    		return $actor;
    	}

    	$actor = new Actor();
    	$imdbActor = new IMDbPerson("http://www.imdb.com/name/".$actorMovie->name->nconst);
    	$imdbActor = $imdbActor->getMainDetails();
    	$imdbActor = json_decode($imdbActor);
    	$actor->setName($actorMovie->name->name);
    	$actor->setAlias($this->prepareAlias($actorMovie->name->name));
    	$dir = $actor->getUploadRootDir();
    	if(isset($actorMovie->name->image)) {
	    	$filename = $actor->getAlias().'.jpg';
	    	$this->_getImageFromUrl($actorMovie->name->image->url, $dir.'/', $filename);
	    	$actor->setFeature($filename);
    	}
    	if(isset($imdbActor->data->birth))
    		$actor->setBirthdayDate(new \DateTime($imdbActor->data->birth->date->normal));
    	if(isset($imdbActor->data->bio))
    		$actor->setResume($imdbActor->data->bio);
    	return $actor;
    }

    private function _getImageFromUrl($link, $dir, $name)
    {
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_POST, 0);
	    curl_setopt($ch,CURLOPT_URL,$link);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $result=curl_exec($ch);
	    curl_close($ch);
	    $savefile = fopen($dir . $name, 'w');
	    fwrite($savefile, $result);
    	fclose($savefile);
    }


    private function prepareAlias($title)
	{
		$alias = str_replace(' ', '-', $title);
		$alias = strtolower($alias);
		$alias = str_replace("'",'-', $alias);
		return $alias;
	}
   

    private function _addGenre($genreMovie)
    {
    	$em = $this->getDoctrine()->getManager();
    	$genre = $em->getRepository('MoviesMovieBundle:Genre')->findBy(array('title' => $genreMovie));
    	if(count($genre) == 1) {
    		$genre = current($genre);
    		return $genre;
    	}

    	$genre = new Genre();
    	$genre->setTitle($genreMovie);
    	$genre->setAlias($genreMovie);
    	return $genre;
    }
}