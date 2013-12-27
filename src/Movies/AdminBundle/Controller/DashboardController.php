<?php

namespace Movies\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Movies\WidgetBundle\Entity\WidgetActivite;
use Movies\AdminBundle\Manager\WidgetManager;


class DashboardController extends Controller
{

	/**
	 * Affiche le dashboard de la page d'accueil
	 * @return array
	 * @Route("/dashboard")
	 * @Template("MoviesAdminBundle:Dashboard:index.html.twig")
	 */
	public function indexAction()
	{
		$widgets = array();
		
		$em = $this->getDoctrine()->getManager();
		$widgetActivite = WidgetManager::setWidgetActivite($em);
		$widgetStats = WidgetManager::setWidgetStats($em);

		$widgets[] = $widgetActivite;
		$widgets[] = $widgetStats;
		return array('widgets' => $widgets);
	}
}