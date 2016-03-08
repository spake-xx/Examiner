<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Question;
use AppBundle\Entity\Quiz;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;


class QuizController extends Controller
{
    /**
    //	 * @Route("/quizes/all", name="all_quizes")
    //	 */
	public function AllQuizesAction(Request $request){
		$em = $this->getDoctrine()->getManager();
		$quiz = new Quiz();

		$form = $this->createFormBuilder($quiz)
					 ->add('name', TextType::class)
					 ->add('save', SubmitType::class, array('label'=>"Utwórz"))
					 ->getForm();
		$form->handleRequest($request);

		if($form->isValid()){
			$em->persist($quiz);
			$em->flush();
			return $this->redirect('/quiz/edit/'.$quiz->getId());
		}

		$exams = $em->getRepository('AppBundle:Quiz')->findAll();
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
		$exam = $em->getRepository('AppBundle:Quiz')->find($id);

		$question = new Question();

		$form = $this->createFormBuilder($question)
					->add('question', TextType::class)
					->add('save', SubmitType::class, array('label'=>'Dodaj'))
					->getForm();

		$questrepo = $em->getRepository('AppBundle:Question');
		$questions = $questrepo->findBy(array('quiz'=>$exam));



		$form->handleRequest($request);

		if($form->isValid()){
			$question->setQuiz($exam);
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
	public function solveExamAction($exam, Request $request)
	{
	}

//		$session = $request->getSession();
//		$em = $this->getDoctrine()->getManager();
//		$exam = $em->getRepository('AppBundle:Exam')->find($exam);
//
//
//		$attempt = new Attempt();
//		$attempt->setExamId($exam);
//
//		$em->persist($attempt);
//		$em->flush();
//
//		echo $attempt->getId();
//		$session->set('attempt', $attempt->getId());
//		return $this->redirect('/solve/answer/1');
//	}
//
//	/**
//	 * @Route("/quiz/result/{attempt}", name="result")
//	 */
//	public function resultAction($attempt, Request $request){
//		$em = $this->getDoctrine()->getManager();
//
//		$answer_repo = $em->getRepository('AppBundle:Answer');
//		$attempt = $em->getRepository('AppBundle:Attempt')->find($attempt);
//		$answers = $answer_repo->findByAttempt($attempt);
//
//		$wynik = 0;
//
//		foreach($answers as $k=>$v){
//			if($v->getQuestionId()->getCorrect()==$v->getAnswer()){
//				$wynik++;
//			}
//		}
//
//		print $wynik;
//	}
//
//	/**
//	 * @Route("/solve/answer/{answer_number}", name="solve_question")
//	 */
//	public function solveQuestionAction($answer_number, Request $request){
//		$em = $this->getDoctrine()->getManager();
//		$repo = $em->getRepository('AppBundle:Attempt');
//		$repo_questions = $em->getRepository('AppBundle:Question');
//		$session = $request->getSession();
//
//		$questions = $repo_questions->findAll();
//		$question = $questions[$answer_number-1];
//
//		$attempt = $repo->find($session->get('attempt'));
//
//
//		$answer = new Answer();
//		$form = $this->createFormBuilder($answer)
//					->add('answer', ChoiceType::class, array(
//							'choices'=>array(
//								'a)'.$question->getA()=>'a',
//								'b)'.$question->getB()=>'b',
//								'c)'.$question->getC()=>'c',
//								'd)'.$question->getD()=>'d',
//						),
//						'choices_as_values'=>true,
//						'expanded'=>true,
//						'label'=>$question->getQuestion(),
//						))
//					->add('submit', SubmitType::class)
//					->getForm();
//
//		$form->handleRequest($request);
//		if($form->isValid()){
//			$answer->setAttempt($attempt);
//			$answer->setQuestionId($question);
//			$em->persist($answer);
//			$em->flush();
//			$answer_number++;
//			if(isset($questions[$answer_number-1])){
//				return $this->redirect('/solve/answer/'.$answer_number);
//			}else{
//				return $this->redirectToRoute('result', array('attempt'=>$attempt->getId()));
//			}
//		}
//
//
//
//
//		return $this->render('exam/solve_question.html.twig', array(
//				'question'=>$question,
//				'form'=>$form->createView(),
//		));
//	}
}
?>