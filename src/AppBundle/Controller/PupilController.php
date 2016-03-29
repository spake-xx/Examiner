<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Attempt;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class PupilController extends Controller{

    /**
     * @Route("/pupil/index/", name="pupil_index")
     */
    public function pupilIndexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $attempts = $em->getRepository('AppBundle:Attempt')->findByUser($this->getUser());
        $results = $em->getRepository('AppBundle:Result')->findAll();
        return $this->render('pupil/view_index.html.twig', array(
            'attempts' => $attempts,
            'results' => $results,
        ));
    }

    public function pupilSidebarAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($this->getUser());
        return $this->render('pupil/sidebar.html.twig', array(
            'user'=>$user,
        ));
    }

    /**
     * @Route("/pupil/view/attempt/{attempt}/", name="pupil_view_attempt")
     */
    public function pupilViewAttemptAction($attempt)
    {
        $em = $this->getDoctrine()->getManager();
        $attempt = $em->getRepository('AppBundle:Attempt')->find($attempt);
        $result = $em->getRepository('AppBundle:Result')->find($attempt);
        $user_answers = $em->getRepository('AppBundle:UserAnswer')->findByAttempt($attempt);
        return $this->render('pupil/view_attempt.html.twig', array(
            'attempt'=>$attempt,
            'result' => $result,
            'user_answers'=>$user_answers,
        ));
    }
}