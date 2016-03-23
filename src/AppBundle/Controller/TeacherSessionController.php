<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Answer;
use AppBundle\Entity\Question;
use AppBundle\Entity\Quiz;
use AppBundle\Entity\QuizSession;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;


class TeacherSessionController extends Controller
{
    /**
     * @Route("/teacher/index/sessions/active/", name="teacher_active_sessions")
     */
    public function teacherActiveSessionsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $sessions = $em->getRepository("AppBundle:QuizSession")->findAll();

        return $this->render('teacher/active_sessions.html.twig', array(
           'sessions'=>$sessions,
        ));
    }
}
?>