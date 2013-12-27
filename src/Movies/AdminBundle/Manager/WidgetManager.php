<?php
namespace Movies\AdminBundle\Manager;

use Movies\WidgetBundle\Entity\WidgetActivite;
use Movies\WidgetBundle\Entity\WidgetStats;

class WidgetManager {


	public static function weatherWidget() {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://api.openweathermap.org/data/2.5/weather?q=Grenoble,fr");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json"));
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response);
		$display = '<ul class="list-unstyled">';
		$display .= self::_getIconWeather($response->weather[0]->icon);
		//$display .= '<p>'.$response->weather[0]->description.'</p>';
		$display .= '</ul>';
		return $display;
	}

	private static function _getIconWeather($icon) {
		$icons = array("01d" => "weather-icon-sun", "01n" => "weather-icon-moon", 
					   "02d" => array("basecloud", "weather-icon-drizzle weather-icon-sunny"), "02n" => array("basecloud", "weather-icon-drizzle weather-icon-night"),
					   "03d" => "weather-icon-cloud", "03n" => "weather-icon-cloud",
					   "04d" => "weather-icon-cloud", "04n" => "weather-icon-cloud",
					   "09d" => array("basecloud", "weather-icon-shower"), "09n" => array("basecloud", "weather-icon-shower weather-icon-night"),
					   "10d" => array("basecloud", "weather-icon-rainy weather-icon-sunny"), "10n" => array("basecloud", "weather-icon-rainy weather-icon-night"),
					   "11d" => array("basethundercloud", "weather-icon-thunder weather-icon-sunny"), "11n" => array("basethundercloud", "weather-icon-thunder weather-icon-night"),
					   "13d" => array("basecloud", "weather-icon-snowy weather-icon-sunny"), "13n" => array("basecloud", "weather-icon-snowy weather-icon-night"),
					   "50d" => "weather-icon-mist", "50n" => "weather-icon-mist");
		$result = $icons[$icon];
		$str = '';
		if(is_array($result)) {
			foreach($result as $i) 
				$str.='<li class="'.$i.'"></li>';
		} else {
			$str .= '<li class="'.$result.'"></li>';	
		}
		return $str;

	}

	public static function setWidgetActivite($em)
	{
		$widgetActivite = new WidgetActivite(3);
		$widgetActivite->setTitle('Activité');

		$movies = $em->getRepository('MoviesMovieBundle:Movie')->getLastMovies($widgetActivite->getLimit());
		$actors = $em->getRepository('MoviesMovieBundle:Actor')->getLastActors($widgetActivite->getLimit());
		$comments = $em->getRepository('MoviesMovieBundle:Comment')->getLastComments($widgetActivite->getLimit());

		$widgetActivite->setMovies($movies);
		$widgetActivite->setActors($actors);
		$widgetActivite->setComments($comments);
		return $widgetActivite;
	}


	public static function setWidgetStats($em)
	{
		$widgetStats = new WidgetStats();
		$stats = $em->getRepository('MoviesMovieBundle:Movie')->getStats();
		
		$widgetStats->setTitle('Statistiques')
					->setNbMovies($stats['nbMovies'])
		    	    ->setNbActors($stats['nbActors'])
		    	    ->setNbComments($stats['nbComments'])
		    	    ->setNbGenres($stats['nbGenres'])
		    	    ->setNbMinutesMovies($stats['nbMinutesMovies']);
		return $widgetStats;
	}

}