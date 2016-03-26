<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\UserBundle;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        $em = $this->getDoctrine()->getManager();

//        $user_logged = $this->container->get('security.context')->getToken()->getUser();
//        $user_logged->getId();
        if($this->getUser()) {


//            $user_logged = $this->getUser()->getId();
            $userManager = $this->get('fos_user.user_manager');

            $user = $userManager->findUserByUsername($this->getUser());

            if ($user->getGrade() == NULL) {
                return $this->redirectToRoute("teacher_index");
            } else {
                return $this->redirectToRoute("pupil_index");
            }
        }
        return $this->redirectToRoute('all_quizes');
    }
}
