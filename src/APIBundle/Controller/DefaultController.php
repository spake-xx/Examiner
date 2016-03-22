<?php

namespace APIBundle\Controller;

use AppBundle\Controller\SystemController;
use Doctrine\ORM\EntityRepository;
use Proxies\__CG__\AppBundle\Entity\Attempt;
use Proxies\__CG__\AppBundle\Entity\UserAnswer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DefaultController extends SystemController
{
    /**
     * @Route("/API/getAnswer/{attempt}")
     */
    public function indexAction(Request $request, $attempt)
    {
        $em = $this->getDoctrine()->getManager();
        $attempt = $em->getRepository('AppBundle:Attempt')->find($attempt);
        if(empty($attempt->getQuestion())) {
            $attempt->setQuestion($this->getNextQuestion($attempt));
            $em->flush();
        }
        if(empty($attempt->getQuestion())){
            return new JsonResponse(false);
        }

        
        $answers = $em->getRepository('AppBundle:Answer');
        $query = $answers->createQueryBuilder('a')
								->where('a.question='.$attempt->getQuestion()->getId())->getQuery();
        $answers = $query->getResult();

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

            $answers_json = $serializer->normalize($answers, 'json');

        $response = new JsonResponse();
        $response->setData(array(
            'question' => $attempt->getQuestion()->getQuestion(),
            'answers'=>$answers_json,
            'attempt'=>$attempt->getId(),
        ));

        return $response;
    }

    /**
     * @Route("/ajax/sendAnswer/", name="ajax_send_answer")
     */
    public function sendAnswerAjaxAction(Request $request)
    {
        $dane = json_decode(file_get_contents('php://input'), true);
        $em = $this->getDoctrine()->getManager();
        $attempt = $em->getRepository('AppBundle:Attempt')->find($dane['attempt']);
        $answer = $em->getRepository('AppBundle:Answer')->find($dane['id']);

        $u_answer = new UserAnswer();
        $u_answer->setAttempt($attempt);
        $u_answer->setAnswer($answer);
        $em->persist($u_answer);
        $em->flush();

        $attempt->setQuestion(null);
        $em->flush();


        $response = new JsonResponse();
        $response->setData(array(
            'success'=>true,
        ));

        return $response;
    }

    /**
     * @Route("/ajax/solve/{attempt}", name="ajax_solve")
     */
    public function solveAjaxAction(Request $request, $attempt)
    {
        return $this->render('solver/solve_question2.html.twig', array(
            'attempt'=>$attempt,
        ));
    }


    /**
     * @Route("/ajax/quiz/{quizsession}", name="ajax_start")
     */
    public function startQuizAction($quizsession, Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $quizsession = $em->getRepository('AppBundle:QuizSession')->find($quizsession);


        $attempt = new Attempt();
        $attempt->setSession($quizsession);
        $attempt->setUser($this->getUser());

        $em->persist($attempt);
        $em->flush();

        $session->set('attempt', $attempt->getId());
        return $this->redirectToRoute('ajax_solve', array(
            'attempt'=>$attempt->getId(),
        ));
    }


    /**
     * @Route("/API/time/{attempt}")
     */
    public function checkTimeAction($attempt){
        $em = $this->getDoctrine()->getManager();
        $attempt = $em->getRepository("AppBundle:Attempt")->find($attempt);
        $time = $attempt->getSession()->getTime();
        $time_s = $time * 60;

        $start = $attempt->getStarted()->getTimestamp();
        $end = $start+$time_s;
        $now = new \DateTime();
        $now = $now->getTimestamp();

        $remaining = $end-$now;

        $response = new JsonResponse();
        $response->setData(array(
            'time'=>$remaining,
        ));
        return $response;
    }

}
