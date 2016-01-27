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
			return $this->redirectToRoute('editExam', $exam->getId());
		}
		
		$exams = $em->getRepository('AppBundle:Exam')->findAll();
		return $this->render('exam/all.html.twig',array(
				'exams'=>$exams,
				'add_exam'=>$form->createView(),
				
		));
	}
	
	/**
	 * @Route("/exam/edit/{id}", name="editExam")
	 */
	public function editExamAction($id){
		$em = $this->getDoctrine()->getManager();
		$exam = $em->getRepository('AppBundle:Exam')->find($id);
		
		return $this->render('exam/add.html.twig', array(
				'exam'=>$exam,
		));
	}
	
}
