<?php

namespace Movies\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/admin")
 */
class UserController extends Controller
{
    /**
     * @Route("/users/{page}")
     * @Template()
     */
    public function indexAction($page)
    {
        
    }
}
