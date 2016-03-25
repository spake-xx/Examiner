<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class PupilController extends Controller{

    /**
     * @Route("/pupil/index/", name="pupil_view_index")
     */
    public function pupilViewIndexAction()
    {

        return $this->render('pupil/view_index.html.twig');
    }

    public function pupilSidebarAction()
    {
        return $this->render('pupil/sidebar.html.twig');
    }
}