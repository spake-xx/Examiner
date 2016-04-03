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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;

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
		$attempt->setUser($this->getUser());
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
            return $this->redirectToRoute('quiz_show_result', array('attempt'=>$attempt));
        }

		$u_answer = new UserAnswer();
		$u_answer->setAttempt($attempt);
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
	 * @Route("/quiz/result/{attempt}", name="quiz_show_result")
	 */
	public function showResultsAction($attempt){
		$em = $this->getDoctrine()->getManager();
		$attempt = $em->getRepository("AppBundle:Attempt")->find($attempt);
		$result = $em->getRepository('AppBundle:Result')->findOneByAttempt($attempt);
		$quiz = $attempt->getSession()->getQuiz();

//		return $this->render('solver/result.html.twig', array(
//			'quiz'=>$quiz,
//			'result'=>$result,
//			'attempt'=>$attempt,
//		));
		return $this->redirectToRoute('pupil_view_attempt', array(
			'attempt'=>$attempt->getId(),
		));
	}
}
