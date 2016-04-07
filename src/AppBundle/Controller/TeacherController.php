<?php

namespace AppBundle\Controller;

use AppBundle\Entity\QuizSession;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Quiz;


class TeacherController extends Controller{
    /**
     * @Route("/teacher/index/", name="teacher_index")
     */
    public function teacherIndexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $quiz_session = new QuizSession();
        $addSession = $this->createFormBuilder($quiz_session)
            ->add('quiz', EntityType::class, array(
                'class' => 'AppBundle:Quiz',
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er){
                   return $er->createQueryBuilder('q')->where('q.teacher='.$this->getUser()->getId());
                },
            ))
            ->add('time')
            ->getForm();
        $addSession->handleRequest($request);
        if($addSession->isValid()){
            $em->persist($quiz_session);
            $em->flush();
            $session = $em->getRepository("AppBundle:QuizSession")->find($quiz_session)->getID();
            return $this->redirectToRoute('teacher_session_view',array('session'=>$session));
        }

        return $this->render('teacher/view_panel.html.twig', array(
            'addSession'=>$addSession->createView(),
        ));
    }

    public function teacherSidebarAction()
    {
        $em = $this->getDoctrine()->getManager();
        $quizes = $em->getRepository("AppBundle:Quiz")->findByTeacher($this->getUser()->getId());
        $classes = $em->getRepository("AppBundle:Grade")->findByTeacher($this->getUser()->getId());


        return $this->render('teacher/sidebar.html.twig', array(
            'quizes'=>$quizes,
            'classes'=>$classes,
            'user'=>$this->getUser(),
        ));
    }

    /**
     * @Route("/teacher/view/attempt/{attempt}/", name="teacher_view_attempt")
     */
    public function teacherViewAttemptAction($attempt)
    {
        $em = $this->getDoctrine()->getManager();
        $attempt = $em->getRepository('AppBundle:Attempt')->find($attempt);
        $result = $em->getRepository('AppBundle:Result')->find($attempt);
        $user_answers = $em->getRepository('AppBundle:UserAnswer')->findByAttempt($attempt);
        $answers = $em->getRepository('AppBundle:Answer')->findAll();
        return $this->render('teacher/view_attempt.html.twig', array(
            'attempt'=>$attempt,
            'result' => $result,
            'user_answers'=>$user_answers,
            'answers'=>$answers,
        ));
    }
}