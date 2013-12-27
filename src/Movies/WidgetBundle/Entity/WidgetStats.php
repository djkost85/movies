<?php
namespace Movies\WidgetBundle\Entity;

use Movies\MovieBundle\Entity\Movie;
use Movies\MovieBundle\Entity\Actor;
use Movies\MovieBundle\Entity\Comment;

use Doctrine\Common\Collections\ArrayCollection;

class WidgetStats extends Widget
{

	private $nbActors;

	private $nbMovies;

	private $nbComments;

	private $nbGenres;

	private $nbMinutesMovies;


	public function getNbActors()
	{
		return $this->nbActors;
	}

	public function getNbMovies()
	{
		return $this->nbMovies;
	}

	public function getnbComments()
	{
		return $this->nbComments;
	}

	public function getNbGenres()
	{
		return $this->nbGenres;
	}

	public function getNbMinutesMovies()
	{
		return $this->nbMinutesMovies;
	}

	public function setNbActors($nb)
	{
		$this->nbActors = $nb;
		return $this;
	}

	public function setNbMovies($nb)
	{
		$this->nbMovies = $nb;
		return $this;
	}

	public function setnbComments($nb)
	{
		$this->nbComments = $nb;
		return $this;
	}

	public function setNbGenres($nb)
	{
		$this->nbGenres = $nb;
		return $this;
	}

	public function setNbMinutesMovies($nb)
	{
		$this->nbMinutesMovies = $nb;
		return $this;
	}

	public function renderWidget()
	{
		$str = $this->renderHeaderWidget();
		$str .= '<div class="widget-content">';
		$str .= '<ul class="list-unstyled">';
		$str .= '<li>'.$this->getNbActors().' acteurs</li>';
		$str .= '<li>'.$this->nbMovies.' films</li>';
		$str .= '<li>'.$this->nbGenres.' genres</li>';
		$str .= '</ul>';
		$str .= '<ul class="list-unstyled">';
		$str .= '<li>'.$this->nbComments.' commentaires</li>';
		$str .= '<li>'.$this->nbMinutesMovies.' minutes</li>';
		$str .= '</ul>';
		$str .= '</div>';
		$str .= $this->renderFooterWidget();
		return $str;
	}

}