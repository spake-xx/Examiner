<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Answer;
use AppBundle\Entity\Attempt;
use AppBundle\Entity\Question;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class SolveController extends SystemController
{
    /**
     * @Route("/start/quiz/{quiz}", name="start_quiz")
     */
    public function startQuizAction($quiz, Request $request)
    {
		$session = $request->getSession();
		$em = $this->getDoctrine()->getManager();
		$quiz = $em->getRepository('AppBundle:Quiz')->find($quiz);


		$attempt = new Attempt();
		$attempt->setQuiz($quiz);

		$em->persist($attempt);
		$em->flush();

		echo $attempt->getId();
		$session->set('attempt', $attempt->getId());
		return $this->redirect('/solve/answer/1');
	}

	/**
	 * @Route("/attempt/{attempt}")
	 */
	public function attemptAction(Request $request, $attempt){
		$em = $this->getDoctrine()->getManager();

		$question = $this->getQuestion($attempt);

		print $question->getQuestion();


	}

	/**
	 * @Route("/quiz/result/{attempt}", name="result")
	 */
	public function resultAction($attempt, Request $request){
		$em = $this->getDoctrine()->getManager();

		$answer_repo = $em->getRepository('AppBundle:Answer');
		$attempt = $em->getRepository('AppBundle:Attempt')->find($attempt);
		$answers = $answer_repo->findByAttempt($attempt);

		$wynik = 0;

		foreach($answers as $k=>$v){
			if($v->getQuestionId()->getCorrect()==$v->getAnswer()){
				$wynik++;
			}
		}

		print $wynik;
	}



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
