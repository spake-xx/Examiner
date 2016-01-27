<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Exam;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Question;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Entity\Attempt;
use AppBundle\Entity\Answer;


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
			return $this->redirect('/web/app_dev.php/exam/edit/'.$exam->getId());
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
	public function editExamAction($id, Request $request){
		$em = $this->getDoctrine()->getManager();
		$exam = $em->getRepository('AppBundle:Exam')->find($id);
		
		$question = new Question();
		
		$form = $this->createFormBuilder($question)
					->add('question', TextType::class)
					->add('a', TextType::class)
					->add('b', TextType::class)
					->add('c', TextType::class)
					->add('d', TextType::class)
					->add('correct', ChoiceType::class, array(
							'choices'=>array(
									'Odpowiedź A jest poprawna'=>'a',
									'Odpowiedź B jest poprawna'=>'b',
									'Odpowiedź C jest poprawna'=>'c',
									'Odpowiedź D jest poprawna'=>'d',
									),
							'choices_as_values'=>true,
							'expanded'=>true,
					))
					->add('save', SubmitType::class, array('label'=>'Dodaj'))
					->getForm();
		
		$questrepo = $em->getRepository('AppBundle:Question');
		$questions = $questrepo->findBy(array('exam_id'=>$exam));
		

		
		$form->handleRequest($request);
		
		if($form->isValid()){
			$question->setExamId($exam);
			$em->persist($question);
			$em->flush();
			return $this->redirect($request->getUri());
		}
		
		return $this->render('exam/add.html.twig', array(
				'exam'=>$exam,
				'add_question'=>$form->createView(),
				'questions'=>$questions,
		));
	}
	
	/**
	 * @Route("/solve/exam/{exam}", name="solve_exam")
	 */
	public function solveExamAction($exam, Request $request){
		$session = $this->getRequest()->getSession();
		$em = $this->getDoctrine()->getManager();
		$exam = $em->getRepository('AppBundle:Exam')->find($exam);
		
		
		$attempt = new Attempt();
		$attempt->setExamId($exam);
		
		$em->persist($attempt);
		$em->flush();
		
		echo $attempt->getId();
		$session->set('attempt', $attempt->getId());
		return $this->redirect('/web/app_dev.php/solve/answer/1');
	}
	
	/**
	 * @Route("/solve/answer/{answer_number}", name="solve_question")
	 */
	public function solveQuestionAction($answer_number, Request $request){
		$em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Attempt');
		$repo_questions = $em->getRepository('AppBundle:Question');
		$session = $this->getRequest()->getSession();
		
		$questions = $repo_questions->findAll();
		$question = $questions[$answer_number-1];
		
		$attempt = $repo->find($session->get('attempt'));
		
		
		$answer = new Answer();
		$form = $this->createFormBuilder($answer)
					->add('answer', ChoiceType::class, array(
							'choices'=>array(
								'a)'.$question->getA()=>'a',
								'b)'.$question->getB()=>'b',
								'c)'.$question->getC()=>'c',
								'd)'.$question->getD()=>'d',
						),
						'choices_as_values'=>true,
						'expanded'=>true,
						'label'=>$question->getQuestion(),
						))
					->add('submit', SubmitType::class)
					->getForm();
					
		$form->handleRequest($request);
		if($form->isValid()){
			$answer->setAttemptId($attempt);
			$answer->setQuestionId($question);
			$em->persist($answer);
			$em->flush();
			$answer_number++;
			if(isset($questions[$answer_number])){
				return $this->redirect('/web/app_dev.php/solve/answer/'.$answer_number);
			}else{
				echo "Nie ma już nic";
			}
		}
		
		

		
		return $this->render('exam/solve_question.html.twig', array(
				'question'=>$question,
				'form'=>$form->createView(),
		));
	}
	
}
