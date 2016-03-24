<?php

namespace AppBundle\Controller;

use AppBundle\Entity\QuizSession;
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
//                'query_builder' => function (EntityRepository $er){
//                    return $er->createQueryBuilder('s');
//                },
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
        $quizes = $em->getRepository("AppBundle:Quiz")->findAll();
        $classes = $em->getRepository("AppBundle:Grade")->findAll();


        return $this->render('teacher/sidebar.html.twig', array(
            'quizes'=>$quizes,
            'classes'=>$classes,
        ));
    }
}