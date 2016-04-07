<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Answer;
use AppBundle\Entity\Question;
use AppBundle\Entity\QuestionImage;
use AppBundle\Entity\Quiz;
use AppBundle\Entity\QuizSession;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;


class QuizController extends Controller
{

	/**
	 * @Route("/teacher/quiz/new/", name="new_quiz")
	 */
	public function newQuizAction(Request $request){
		$em = $this->getDoctrine()->getManager();
		$quiz = new Quiz();

		$quiz->setTeacher($this->getUser());
		$form = $this->createFormBuilder($quiz)
			->add('name', TextType::class)
			->add('save', SubmitType::class, array('label'=>"Utwórz"))
			->getForm();
		$form->handleRequest($request);

		if($form->isValid()){
			$quiz->setEnabled(1);
			$em->persist($quiz);
			$em->flush();
			return $this->redirectToRoute('editQuiz', array('id'=>$quiz->getId()));
		}

		return $this->render('teacher/new_quiz.html.twig',array(
			'add_quiz'=>$form->createView(),
		));
	}

	/**
	 * @Route("/teacher/quiz/edit/{id}", name="editQuiz")
	 */
	public function editQuizAction($id, Request $request){
		$em = $this->getDoctrine()->getManager();
		$quiz = $em->getRepository('AppBundle:Quiz')->find($id);

		$question = new Question();
		$question->setEnabled(1);

		$form = $this->createFormBuilder($question)
					->add('question', TextareaType::class)
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
	 * @Route("/teacher/quiz/question/{question}", name="editQuestion")
	 */
	public function editQuestionAction(Request $request, $question){
		$em = $this->getDoctrine()->getManager();
		$question = $em->getRepository('AppBundle:Question')->find($question);
		$answers = $em->getRepository('AppBundle:Answer')->createQueryBuilder('a');
		$answers = $answers
			->where('a.question='.$question->getId())
			->andWhere('a.enabled = 1')
			->getQuery()
			->getResult();
		$quiz = $question->getQuiz();
		$questions = $em->getRepository('AppBundle:Question')->findByQuiz($quiz);
		$image = $em->getRepository('AppBundle:QuestionImage')->findOneByQuestion($question);
		if($image){
			$image=$image->getWebPath();
		}
		$correct = $em->getRepository('AppBundle:Answer')->createQueryBuilder('c');
		$correct = $correct
			->where('c.points > 0')
			->andWhere('c.question='.$question->getId())
			->andWhere('c.enabled = 1')
			->getQuery()
			->getResult();
		$answer = new Answer();
		$answer->setEnabled(1);
		$answer->setQuestion($question);

		$form = $this->get('form.factory')->createNamedBuilder('answer', FormType::class, $answer)
					->add('answer')
					->add('points')
					->add('save', SubmitType::class, array('label'=>"Dodaj odpowiedź"))
					->getForm();
		$form->handleRequest($request);
//		if($form->isValid()){
//			if($answer->getPoints()>=1 && $correct){
//				$this->addFlash('notice','W tym pytaniu istnieje już poprawna odpowiedź!');
//				return $this->render('teacher/edit_question.html.twig', array(
//					'question' => $question,
//					'questions' => $questions,
//					'quiz' => $quiz,
//					'answers'=>$answers,
//					'add_answer'=>$form->createView(),
//					'add_question'=>$formq->createView(),
//					'add_image'=>$formfile->createView(),
//					'image'=>$image,
//				));
//			}
//			$em->persist($answer);
//			$em->flush();
//			$answers = $em->getRepository('AppBundle:Answer')->findByQuestion($question);
//		}

		$question_new = new Question();
		$formq = $this->get('form.factory')->createNamedBuilder('question', FormType::class, $question_new)
			->add('question', TextareaType::class)
			->add('save', SubmitType::class, array('label'=>'Dodaj'))
			->getForm();
		$formq->handleRequest($request);
		if($formq->isValid()) {
			$question_new->setEnabled(1);
			$question_new->setQuiz($quiz);
			$em->persist($question_new);
			$em->flush();
			return $this->redirectToRoute('editQuestion', array(
				'question'=>$question_new->getId(),
			));
		}

		$file = new QuestionImage();
		$formfile = $this->get('form.factory')->createNamedBuilder('image', FormType::class, $file)
							->add('file')->getForm();
		$file->setQuestion($question);
		if($request->request->has('image')){
			$formfile->handleRequest($request);
		}
		if($formfile->isValid()){
			$file->upload();
			$em->persist($file);
			$em->flush();
			$this->addFlash('notice', 'Pomyślnie dodano obrazek do pytania.');
		}

		if($form->isValid()){
			if($answer->getPoints()>=1 && $correct){
				$this->addFlash('notice','W tym pytaniu istnieje już poprawna odpowiedź!');
				return $this->render('teacher/edit_question.html.twig', array(
					'question' => $question,
					'questions' => $questions,
					'quiz' => $quiz,
					'answers'=>$answers,
					'add_answer'=>$form->createView(),
					'add_question'=>$formq->createView(),
					'add_image'=>$formfile->createView(),
					'image'=>$image,
				));
			}
			$em->persist($answer);
			$em->flush();
			$answers = $em->getRepository('AppBundle:Answer')->createQueryBuilder('a');
			$answers = $answers
				->where('a.question='.$question->getId())
				->andWhere('a.enabled = 1')
				->getQuery()
				->getResult();
		}

		return $this->render('teacher/edit_question.html.twig', array(
			'question' => $question,
			'questions' => $questions,
			'quiz' => $quiz,
			'answers'=>$answers,
			'add_answer'=>$form->createView(),
			'add_question'=>$formq->createView(),
			'add_image'=>$formfile->createView(),
			'image'=>$image,
		));
	}

	/**
	 * @Route("/teacher/quiz/answer/{answer}/", name="editAnswer")
	 */
	public function editAnswerAction(Request $request, $answer){
		$em = $this->getDoctrine()->getManager();
		$answer = $em->getRepository('AppBundle:Answer')->find($answer);
		$question = $answer->getQuestion();
		$question = $em->getRepository('AppBundle:Question')->find($question);
		$quiz = $answer->getQuestion()->getQuiz();
		$questions = $em->getRepository('AppBundle:Question')->findByQuiz($quiz);

//		$answer = new Answer();
//		$answer->setQuestion($question);
//
//		$form = $this->createFormBuilder($answer)
//			->add('answer')
//			->add('points')
//			->add('save', SubmitType::class, array('label'=>"Dodaj odpowiedź"))
//			->getForm();
//		$form->handleRequest($request);
//		if($form->isValid()){
//			$em->persist($answer);
//			$em->flush();
		$answers = $em->getRepository('AppBundle:Answer')->createQueryBuilder('a');
		$answers = $answers
			->where('a.question='.$question->getId())
			->andWhere('a.enabled != 0')
			->getQuery()
			->getResult();
		$correct = $em->getRepository('AppBundle:Answer')->createQueryBuilder('c');
		$correct = $correct
			->where('c.points > 0')
			->andWhere('c.question='.$question->getId())
			->andWhere('c.enabled != 0')
			->getQuery()
			->getResult();
		$form = $this->createFormBuilder($answer)
			->add('answer')
			->add('points')
			->add('save', SubmitType::class)
			->getForm();
		$form->handleRequest($request);


		$question_new = new Question();
		$formq = $this->createFormBuilder($question_new)
			->add('question', TextareaType::class)
			->add('save', SubmitType::class, array('label'=>'Dodaj'))
			->getForm();
		$formq->handleRequest($request);
		if($formq->isValid()) {
			$question_new->setQuiz($quiz);
			$question_new->setEnabled(1);
			$em->persist($question_new);
			$em->flush();
			return $this->redirectToRoute('editQuestion', array(
				'question'=>$question_new->getId(),
			));
		}
		if($form->isValid()){
			if($answer->getPoints()>=1 && $correct){
				$this->addFlash('notice','W tym pytaniu istnieje już poprawna odpowiedź!');
				return $this->render('teacher/edit_answer.html.twig', array(
					'question' => $question,
					'questions' => $questions,
					'quiz' => $quiz,
					'answers'=>$answers,
					'edit_answer'=>$form->createView(),
					'add_question'=>$formq->createView(),
				));
			}
			$em->persist($answer);
			$em->flush();
			$answers = $em->getRepository('AppBundle:Answer')->findByQuestion($question);
		}

		return $this->render('teacher/edit_answer.html.twig', array(
			'question' => $question,
			'questions' => $questions,
			'quiz' => $quiz,
			'answers'=>$answers,
			'edit_answer'=>$form->createView(),
			'add_question'=>$formq->createView(),
		));
	}

	/**
	 * @Route("/teacher/quiz/remove/answer/{answer}/", name="removeAnswer")
	 */
	public function removeAnswerAction($answer)
	{
		$em = $this->getDoctrine()->getManager();
		$answer = $em->getRepository('AppBundle:Answer')->find($answer);
		$question = $answer->getQuestion()->getId();
		$answer->setEnabled(0);
		$em->persist($answer);
		$em->flush();
		return $this->redirectToRoute('editQuestion', array(
			'question'=>$question,
		));
	}

	/**
	 * @Route("/teacher/quiz/edit/question/{question}", name="editQuestionName")
	 */
	public function editQuestionNameAction(Request $request, $question){
		$em = $this->getDoctrine()->getManager();
		$question = $em->getRepository('AppBundle:Question')->find($question);
		$quiz = $question->getQuiz();
		$questions = $em->getRepository('AppBundle:Question')->findByQuiz($quiz);

		$editQuestionName = $this->createFormBuilder($question)
			->add('question', TextareaType::class)
			->add('save', SubmitType::class)
			->getForm();
		$editQuestionName->handleRequest($request);
		if($editQuestionName->isValid()){
			$em->persist($question);
			$em->flush();
			return $this->redirectToRoute('editQuestion', array(
				'question'=>$question->getId(),
			));
		}

		$question_new = new Question();
		$formq = $this->createFormBuilder($question_new)
			->add('question', TextareaType::class)
			->add('save', SubmitType::class, array('label'=>'Dodaj'))
			->getForm();
		$formq->handleRequest($request);
		if($formq->isValid()) {
			$question_new->setQuiz($quiz);
			$question_new->setEnabled(1);
			$em->persist($question_new);
			$em->flush();
			return $this->redirectToRoute('editQuestion', array(
				'question'=>$question_new->getId(),
			));
		}

		return $this->render('teacher/edit_question_name.html.twig', array(
			'question' => $question,
			'questions' => $questions,
			'quiz' => $quiz,
			'editQuestion'=>$editQuestionName->createView(),
			'add_question'=>$formq->createView(),
		));
	}

	/**
	 * @Route("/teacher/quiz/remove/question/{question}/", name="removeQuestion")
	 */
	public function removeQuestionAction($question)
	{
		$em = $this->getDoctrine()->getManager();
		$question = $em->getRepository('AppBundle:Question')->find($question);
		$answers = $em->getRepository('AppBundle:Answer')->findByQuestion($question);
		$len=count($answers);
		for($i=0;$i<$len;$i++)
		{
			$answers[$i]->setEnabled(0);
			$em->persist($answers[$i]);
		}
		$question->setEnabled(0);
		$em->persist($question);
		$em->flush();
		return $this->redirectToRoute('editQuiz', array(
			'id'=>$question->getQuiz()->getId(),
		));

	}
}
?>