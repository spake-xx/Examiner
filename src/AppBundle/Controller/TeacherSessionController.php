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
use Symfony\Component\Validator\Constraints\Date;


class TeacherSessionController extends Controller
{
    /**
     * @Route("/teacher/index/sessions/active/", name="teacher_session_active")
     */
    public function teacherActiveSessionsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $sessions_all = $em->getRepository("AppBundle:QuizSession");
        $qb = $sessions_all->createQueryBuilder('s');

        
        $sessions = $qb
            ->where('s.end is null')->getQuery()->getResult();


        return $this->render('session/active_sessions.html.twig', array(
           'sessions'=>$sessions,
        ));
    }

    /**
     * @Route("/teacher/index/sessions/all/", name="teacher_session_all")
     */
    public function teacherAllSessionsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $sessions = $em->getRepository("AppBundle:QuizSession")->findAll();


        return $this->render('session/all_sessions.html.twig', array(
            'sessions'=>$sessions,
        ));
    }

    /**
     * @Route("teacher/index/session/view/{session}/", name="teacher_session_view")
     */
    public function teacherSessionViewAction($session)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $em->getRepository('AppBundle:QuizSession')->find($session);

        return $this->render('session/view_session.html.twig', array(
            'session'=>$session,
        ));
    }

    /**
     * @Route("/teacher/index/session/end/" name="teacher_session_end")
     */
    public function teacherSessionEndAction($session)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $em->getRepository("AppBundle:QuizSession")->find($session);
        $session->setEnd(new \DateTime(date('Y-m-d H:i:s')));
        $em->persist($session);
        $em->flush();
        $this->addFlash('notice','Finished');
        return $this->redirectToRoute('teacher_session_view', array(
           'session'=>$session->getID(),
        ));
    }

//    /**
//     * @Route("/teacher/index/session/new/", name="teacher_session_new")
//     */
//    public function teacherSessionNewAction(Request $request)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $quiz_session = new QuizSession();
//        $addSession = $this->createFormBuilder($quiz_session)
//            ->add('quiz', EntityType::class, array(
//                'class' => 'AppBundle:Quizez',
//                'choice_label' => 'name',
//                'query_builder' => function (EntityRepository $er){
//                    return $er->createQueryBuilder('s');
//                },
//            ))
//            ->add('time')
//            ->getForm();
//        $addSession->handleRequest($request);
//        if($addSession->isValid()){
//            $em->persist($quiz_session);
//            $em->flush();
//            $this->redirectToRoute('teacher_session_view');
//        }
//    }
}
?>