<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Answer;
use AppBundle\Entity\Attempt;
use AppBundle\Entity\Question;
use AppBundle\Entity\Quiz;
use AppBundle\Entity\QuizSession;
use AppBundle\Entity\UserAnswer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AttemptController extends Controller
{
    /**
     * @Route("/attempt/view/attempt/{attempt}/", name="attemptView")
     */
    public function attemptViewAction($attempt)
    {
        $em = $this->getDoctrine()->getManager();
        $attempt = $em->getRepository('AppBundle:Attempt')->find($attempt);
        $result = $em->getRepository('AppBundle:Result')->find($attempt);
        $user_answers = $em->getRepository('AppBundle:UserAnswer')->findByAttempt($attempt);
        $answers = $em->getRepository('AppBundle:Answer')->findAll();

        return $this->render('/attempt/view_attempt.html.twig', array(
            'result'=>$result,
            'user_answers'=>$user_answers,
            'answers'=>$answers,
            'attempt'=>$attempt,
        ));
    }



//  JEDNAK NIE POTRZEBNE
//    /**
//     * @Route("/attempt/ajax/question/")
//     */
//    public function attemptGetQuestion()
//    {
//        $question = json_decode(file_get_contents('php://input'),true);
//        $em = $this->getDoctrine()->getManager();
//        $question = $em->getRepository('AppBundle:Question')->find($question);
//
//
//        $encoders = array(new XmlEncoder(), new JsonEncoder());
//        $normalizers = array(new ObjectNormalizer());
//        $serializer = new Serializer($normalizers, $encoders);
//
//        $question = $serializer->normalize($question, 'json');
//
//        $response = new JsonResponse();
//        $response->setData(array(
//            'question'=>$question,
//        ));
//        return $response;
//    }
}
?>