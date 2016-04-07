<?php

namespace APIBundle\Controller;

use AppBundle\Controller\SystemController;
use AppBundle\Entity\Result;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Attempt;
use AppBundle\Entity\UserAnswer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
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
        shuffle($answers);

        $image = $em->getRepository("AppBundle:QuestionImage")->findOneBy(array('question'=>$attempt->getQuestion()));
        if($image!=null) {
            $image_url = $image->getWebPath();
        }else{
            $image_url = null;
        }

        $answered = $em->getRepository('AppBundle:UserAnswer')->createQueryBuilder('u');
        $answered = $answered
            ->select('count(DISTINCT a.question)')
            ->where('u.attempt='.$attempt->getId())
            ->innerJoin('u.answer', 'a')
            ->getQuery()
            ->getSingleScalarResult();
        $answered = (int)$answered;

        $quest_repo = $em->getRepository('AppBundle:Question');
        $questions_count = $quest_repo->createQueryBuilder('q')
            ->select('count(q.id)')
            ->where('q.quiz='.$attempt->getSession()->getQuiz()->getId())
            ->getQuery()->getSingleScalarResult();

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

            $answers_json = $serializer->normalize($answers, 'json');

        $response = new JsonResponse();
        $response->setData(array(
            'question' => $attempt->getQuestion()->getQuestion(),
            'answers'=>$answers_json,
            'attempt'=>$attempt->getId(),
            'questions_count'=>$questions_count,
            'answered'=>$answered,
            'image'=>$image_url,
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

        foreach($dane['answer'] as $k=>$v) {
            $answer = $em->getRepository('AppBundle:Answer')->find($k);
            $u_answer = new UserAnswer();
            $u_answer->setAttempt($attempt);
            $u_answer->setAnswer($answer);
            $em->persist($u_answer);
            $em->flush();
        }
        $attempt->setQuestion(null);
        $em->flush();


        $response = new JsonResponse();
        $response->setData(array(
            'success'=>true,
        ));

        return $response;
    }

    /**
     * @Route("/API/session/getPupils/")
     */
    public function getSessionPupilsAction(){
        $dane = json_decode(file_get_contents('php://input'), true);
        $em = $this->getDoctrine()->getManager();
        $pupils_logged = $em->getRepository("AppBundle:Attempt")->createQueryBuilder('a')
            ->where('a.session='.$dane['session'])
            ->andWhere('a.end IS NULL')
            ->getQuery()->getResult();

        if($dane['time']) {
            $session_repo = $em->getRepository('AppBundle:QuizSession');
            $session = $session_repo->find($dane['session']);
            $session->setTime($dane['time']);
            $em->flush();
        }

        foreach($pupils_logged as $k=>$v){
            $answered = $em->getRepository('AppBundle:UserAnswer')->createQueryBuilder('u');
            $answered = $answered->select('count(u.id)')->where('u.attempt='.$v->getId())->getQuery()->getSingleScalarResult();
            $pupils_logged[$k]->answered = (int)$answered;
        }

        $pupils_ended = $em->getRepository("AppBundle:Result")->createQueryBuilder('r');
        $pupils_ended = $pupils_ended->innerJoin('r.attempt', 'a')
                              ->innerJoin('a.session', 's')
                              ->where('a.session='.$dane['session'])
                                ->andWhere('a.end IS NOT NULL')
                                ->getQuery()->getResult();


        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $pupils_logged_json= $serializer->normalize($pupils_logged, 'json');
        $pupils_ended_json= $serializer->normalize($pupils_ended, 'json');

        $response = new JsonResponse();
        $response->setData(array(
            'pupils_logged' => $pupils_logged_json,
            'pupils_ended'  => $pupils_ended_json,
        ));

        return $response;
    }

    /**
     * @Route("/API/refresh_ended/")
     */
    public function refreshEndedAction(){
        $em = $this->getDoctrine()->getManager();
        $attempts = $em->getRepository("AppBundle:Attempt")->createQueryBuilder('a');
        $attempts = $attempts->innerJoin("a.session", "s")
                             ->where("a.end IS NULL")->getQuery()->getResult();

        foreach($attempts as $k=>$v) {
            $session = $v->getSession();
            $time = $session->getTime();
            $time = $time * 60;

            $now_timestamp = time();
            $end_timestamp = $v->getStarted()->getTimestamp() + $time;

            if ($now_timestamp > $end_timestamp) {
                $end_date = new \DateTime();
                $end_date->setTimestamp($end_timestamp);
                print $v->getStarted()->format("H:i:s D")."<br />";

                $attempts[$k]->setEnd($end_date);
                $em->flush();

                $result = new Result();
                $result->setAttempt($v);
                $result->setPoints($em->getRepository('AppBundle:Attempt')->getPointsByAttempt($v));
                $result->setMaxPoints($em->getRepository('AppBundle:Quiz')->getPointsByQuiz($v->getSession()->getQuiz()));

                $em->persist($result);
                $em->flush();
            }

        }

        return new Response(' ');
    }

    /**
     * @Route("/API/end/")
     */
    public function endQuizAction(){
        try {
            $dane = json_decode(file_get_contents('php://input'), true);

            $em = $this->getDoctrine()->getManager();
            $attempt = $em->getRepository("AppBundle:Attempt")->find($dane['attempt']);
            $attempt->setEnd(new \DateTime());

            $result = new Result();
            $result->setAttempt($attempt);
            $result->setPoints($em->getRepository('AppBundle:Attempt')->getPointsByAttempt($attempt));
            $result->setMaxPoints($em->getRepository('AppBundle:Quiz')->getPointsByQuiz($attempt->getSession()->getQuiz()));

            $em->persist($result);
            $em->flush();
        }catch(Exception $e){
            return new JsonResponse('', 500);
        }

        return new JsonResponse('', 200);
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

    /**
     * @Route("/attempt/ajax/attempt/")
     */
    public function attemptGetAttempt()
    {
        $attempt = json_decode(file_get_contents('php://input'),true);
        $em = $this->getDoctrine()->getManager();
        $attempt = $em->getRepository('AppBundle:Attempt')->find($attempt);
        $result = $em->getRepository('AppBundle:Result')->findByAttempt($attempt);
        $user_answers_repo = $em->getRepository('AppBundle:UserAnswer');
        $user_answers = $user_answers_repo->createQueryBuilder('u');
        $user_answers = $user_answers
//            ->select('a.question')->distinct()
            ->where('u.attempt='.$attempt->getId())
            ->innerJoin('u.answer', 'a')
            ->groupBy('a.question')
            ->getQuery()
            ->getResult();


        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $attempt = $serializer->normalize($attempt, 'json');
        $result = $serializer->normalize($result, 'json');
        $user_answers = $serializer->normalize($user_answers, 'json');

        $response = new JsonResponse();
        $response->setData(array(
            'attempt'=>$attempt,
            'result'=>$result,
            'user_answers'=>$user_answers,
        ));
        return $response;
    }

}
