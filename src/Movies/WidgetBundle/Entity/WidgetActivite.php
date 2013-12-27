<?php
namespace Movies\WidgetBundle\Entity;

use Movies\MovieBundle\Entity\Movie;
use Movies\MovieBundle\Entity\Actor;
use Movies\MovieBundle\Entity\Comment;

use Doctrine\Common\Collections\ArrayCollection;

class WidgetActivite extends Widget
{

	private $movies;

	private $actors;

	private $comments;

	private $limit;

	public function __construct($limit)
	{
		//parent::__construct();
		$this->limit = $limit;
	}

	public function getMovies()
	{
		return $this->movies;
	}

	public function setMovies(array $movies)
	{
		$this->movies = $movies;
		return $this;
	}

	public function getActors()
	{
		return $this->actors;
	}

	public function setActors(array $actors)
	{
		$this->actors = $actors;
		return $this; 	
	}	

	public function getComments()
	{
		return $this->comments;
	}

	public function setComments(array $comments)
	{
		$this->comments = $comments;
		return $this;
	}

	public function getLimit()
	{
		return $this->limit;
	}

	public function setLimit($limit)
	{
		$this->limit = $limit;
		return $this;
	}

	public function renderWidget()
	{
		$str = $this->renderHeaderWidget();
		$str .= '<div class="widget-content">';
		$str .= '<ul class="list-unstyled">';
		$str .= '<li>Last actors added</li>';
		$str .= '<ul class="list-widget">';
		if(is_array($this->actors)) {
			foreach($this->actors as $actor) {
				$movies = $actor->getMovies();
				$str .= '<li><a href=""><img src="/'.$actor->getWebPath().'" height="45px" /> '.$actor->getName().' ('.$movies[0]->getTitle().')</a></li>';
			}
		}
		$str .= '</ul>';
		$str.= '</ul>';
		$str .= '<ul class="list-unstyled">';
		$str .= '<li>Last movies added</li>';
		$str .= '<ul class="list-widget">';
		if(is_array($this->movies)) {
			foreach($this->movies as $movie) {
				$str .= '<li><a href=""><img src="/'.$movie->getWebPath().'" height="45px" /> '.$movie->getTitle().'</a></li>';
			}
		}
		$str .= '</ul>';
		$str .= '</ul>';
		if(is_array($this->comments) && !empty($this->comments)) {
			$str .= '<li>Last comments added</li>';
			$str .= '<ul class="list-widget">';
			foreach($this->comments as $comment) {
				$str .= '<li><a href="">'.$comment->getUser()->getDisplayName().'</a></li>';
			}
			$str .= '</ul>';
		}
		$str .= '</ul>';
		$str .= '</div>';
		$str .= $this->renderFooterWidget();
		return $str;
	}
}