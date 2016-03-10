<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Answer;
use AppBundle\Entity\Attempt;
use AppBundle\Entity\Question;
use AppBundle\Entity\UserAnswer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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

		$session->set('attempt', $attempt->getId());
		return $this->redirectToRoute('solver_attempt', array(
			'attempt'=>$attempt->getId(),
		));
	}

	/**
	 * @Route("/attempt/{attempt}", name="solver_attempt")
	 */
	public function attemptAction(Request $request, $attempt){
		$em = $this->getDoctrine()->getManager();
        $attempt = $em->getRepository('AppBundle:Attempt')->find($attempt);
        if(empty($attempt->getQuestion())) {
            $attempt->setQuestion($this->getNextQuestion($attempt));
            $em->flush();
        }
        if(empty($attempt->getQuestion())){
            print "KONIEC PYTAŃ !";
            exit();
        }

		$u_answer = new UserAnswer();
		$form = $this->createFormBuilder($u_answer)
					->add('answer', EntityType::class, array(
						'class' => 'AppBundle:Answer',
						'query_builder' => function (EntityRepository $er) use ($attempt) {
							return $er->createQueryBuilder('a')
								->where('a.question='.$attempt->getQuestion()->getId());
						},
                        'choice_label'=>'answer',
                        'expanded'=>true,
                    ))
					->add('submit', SubmitType::class)
					->getForm();

		$form->handleRequest($request);

        if($form->isValid()){
            print $u_answer->getAnswer()->getAnswer();
			$u_answer->setAttempt($attempt);
			$em->persist($u_answer);
			$em->flush();

            $attempt->setQuestion(null);
            $em->flush();
            return $this->redirectToRoute('solver_attempt', array(
                'attempt'=>$attempt->getId(),
            ));
		}

		return $this->render('solver/solve_question.html.twig', array(
			'question'=>$attempt->getQuestion(),
            'form'=>$form->createView(),
		));
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
