<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Exam;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class ExamController extends Controller
{
	/**
	 * @Route("/quizes/all", name="all_exams")
	 */
	public function AllQuizesAction(Request $request){
		$em = $this->getDoctrine()->getManager();		
		$exam = new Exam();
		
		$form = $this->createFormBuilder($exam)
					 ->add('name', TextType::class)
					 ->add('save', SubmitType::class, array('label'=>"Utwórz"))
					 ->getForm();
		$form->handleRequest($request);
		
		if($form->isValid()){
			$em->persist($exam);
			$em->flush();
		}
		
		$exams = $em->getRepository('AppBundle:Exam')->findAll();
		return $this->render('quiz/all.html.twig',array(
				'exams'=>$exams,
				'add_exam'=>$form->createView(),
				
		));
	}
}
