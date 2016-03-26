<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class PupilController extends Controller{

    /**
     * @Route("/pupil/index/", name="pupil_index")
     */
    public function pupilIndexAction()
    {

        return $this->render('pupil/view_index.html.twig');
    }

    public function pupilSidebarAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($this->getUser());
        return $this->render('pupil/sidebar.html.twig', array(
            'user'=>$user,
        ));
    }
}