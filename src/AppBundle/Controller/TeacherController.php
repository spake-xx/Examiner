<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class TeacherController extends Controller{
    /**
     * @Route("/teacher/index/", name="teacher_index")
     */
    public function teacherIndexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $quizes = $em->getRepository("AppBundle:Quiz")->findAll();
        $classess = $em->getRepository("AppBundle:Grade")->findAll();


        return $this->render('teacher/view_panel.html.twig', array(
            'quizes'=>$quizes,
            'classess'=>$classess,
        ));
    }
}