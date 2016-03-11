<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Answer;
use AppBundle\Entity\Question;
use AppBundle\Entity\Quiz;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;


class QuizController extends Controller
{
    /**
	 * @Route("/quizes/all", name="all_quizes")
	 */
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

		$quizes = $em->getRepository('AppBundle:Quiz')->findAll();
		return $this->render('teacher/all.html.twig',array(
				'quizes'=>$quizes,
				'add_quiz'=>$form->createView(),

		));
	}

	/**
	 * @Route("/quiz/edit/{id}", name="editQuiz")
	 */
	public function editQuizAction($id, Request $request){
		$em = $this->getDoctrine()->getManager();
		$quiz = $em->getRepository('AppBundle:Quiz')->find($id);

		$question = new Question();

		$form = $this->createFormBuilder($question)
					->add('question', TextType::class)
					->add('save', SubmitType::class, array('label'=>'Dodaj'))
					->getForm();

		$questrepo = $em->getRepository('AppBundle:Question');
		$questions = $questrepo->findBy(array('quiz'=>$quiz));



		$form->handleRequest($request);

		if($form->isValid()){
			$question->setQuiz($quiz);
			$em->persist($question);
			$em->flush();
			return $this->redirectToRoute('editQuestion', array(
				'question'=>$question->getId(),
				'questions'=>$questions,
			));
		}

		return $this->render('teacher/edit_quiz.html.twig', array(
				'quiz'=>$quiz,
				'add_question'=>$form->createView(),
				'questions'=>$questions,
		));
	}


	/**
	 * @Route("/quiz/question/{question}", name="editQuestion")
	 */
	public function editQuestionAction(Request $request, $question){
		$em = $this->getDoctrine()->getManager();
		$question = $em->getRepository('AppBundle:Question')->find($question);
		$answers = $em->getRepository('AppBundle:Answer')->findByQuestion($question);
		$quiz = $question->getQuiz();
		$questions = $em->getRepository('AppBundle:Question')->findByQuiz($quiz);

		$answer = new Answer();
		$answer->setQuestion($question);

		$form = $this->createFormBuilder($answer)
					->add('answer')
					->add('points')
					->add('save', SubmitType::class, array('label'=>"Dodaj odpowiedź"))
					->getForm();
		$form->handleRequest($request);
		if($form->isValid()){
			$em->persist($answer);
			$em->flush();
			$answers = $em->getRepository('AppBundle:Answer')->findByQuestion($question);
		}

		$question_new = new Question();
		$formq = $this->createFormBuilder($question_new)
			->add('question', TextType::class)
			->add('save', SubmitType::class, array('label'=>'Dodaj'))
			->getForm();
		$formq->handleRequest($request);
		if($formq->isValid()) {
			$question_new->setQuiz($quiz);
			$em->persist($question_new);
			$em->flush();
			return $this->redirectToRoute('editQuestion', array(
				'question'=>$question_new->getId(),
			));
		}

		return $this->render('teacher/edit_question.html.twig', array(
			'question' => $question,
			'questions' => $questions,
			'quiz' => $quiz,
			'answers'=>$answers,
			'add_answer'=>$form->createView(),
			'add_question'=>$formq->createView(),
		));
	}
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
}
?>