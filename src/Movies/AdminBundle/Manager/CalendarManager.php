<?php
namespace Movies\AdminBundle\Manager;

use Serie\SerieBundle\Entity\Serie;

class CalendarManager {
	
	/**
	 * Affiche un calendrier
	 * @param  int $month index du mois
	 * @param  int $year  année
	 * @return String
	 */
	public static function displayCalendar($container, $month='', $year='')
	{
		if ($month == '') {
            $month = date('m');
        }
        if($year == '')
            $year = date('Y');

        return $container->render('MoviesAdminBundle:Calendar:calendar.html.twig',array('month' => $month,'year'=>$year));
	}
}