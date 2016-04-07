<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Answer;
use AppBundle\Entity\Attempt;
use AppBundle\Entity\Question;
use AppBundle\Entity\Quiz;
use AppBundle\Entity\QuizSession;
use AppBundle\Entity\UserAnswer;
use AppBundle\Entity\QuestionImage;
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
//        $question_image = $em->getRepository('AppBundle:QuestionImage')->findAll();

        return $this->render('/attempt/view_attempt.html.twig', array(
            'result'=>$result,
            'user_answers'=>$user_answers,
            'answers'=>$answers,
            'attempt'=>$attempt,
//            'question_image'=>$question_image,
        ));
    }

    /**
     * @Route("/attempt/view/teacher_attempt/{attempt}/", name="teacherAttemptView")
     */
    public function teacherAttemptViewAction($attempt)
    {
        $em = $this->getDoctrine()->getManager();
        $attempt = $em->getRepository('AppBundle:Attempt')->find($attempt);
        $result = $em->getRepository('AppBundle:Result')->find($attempt);
        $user_answers = $em->getRepository('AppBundle:UserAnswer')->findByAttempt($attempt);
        $answers = $em->getRepository('AppBundle:Answer')->findAll();
//        $question_image = $em->getRepository('AppBundle:QuestionImage')->findAll();

        return $this->render('/attempt/view_teacher_attempt.html.twig', array(
            'result'=>$result,
            'user_answers'=>$user_answers,
            'answers'=>$answers,
            'attempt'=>$attempt,
//            'question_image'=>$question_image,
        ));
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
        $user_answers = $em->getRepository('AppBundle:UserAnswer')->findByAttempt($attempt);
        $answers = $em->getRepository('AppBundle:Answer')->findAll();
        $question_image = $em->getRepository('AppBundle:QuestionImage')->findAll();

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $attempt = $serializer->normalize($attempt, 'json');
        $result = $serializer->normalize($result, 'json');
        $user_answers = $serializer->normalize($user_answers, 'json');
        $answers = $serializer->normalize($answers, 'json');
        $question_image = $serializer->normalize($question_image, 'json');

        $response = new JsonResponse();
        $response->setData(array(
            'attempt'=>$attempt,
            'result'=>$result,
            'user_answers'=>$user_answers,
//            'answers'=>$answers,
//            'question_image'=>$question_image,
        ));
        return $response;
    }




//  JEDNAK NIE POTRZEBNE
    /**
     * @Route("/attempt/ajax/question/")
     */
    public function attemptGetQuestion()
    {
        $dane = json_decode(file_get_contents('php://input'),true);
        $em = $this->getDoctrine()->getManager();
        $question = $em->getRepository('AppBundle:Question')->find($dane['question']);
        $attempt = $em->getRepository('AppBundle:Attempt')->find($dane['attempt']);
//        $question = $em->getRepository('AppBundle:Question')->find($question);
        $image = $em->getRepository('AppBundle:QuestionImage')->findOneByQuestion($question);
        $result = $em->getRepository('AppBundle:Result')->find($attempt);
        $user_answers = $em->getRepository('AppBundle:UserAnswer')->findByAttempt($attempt);
        $answers = $em->getRepository('AppBundle:Answer')->findByQuestion($question);
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        if($image){
            $image = $image->getWebPath();
        }
        $question = $serializer->normalize($question, 'json');
        $image = $serializer->normalize($image, 'json');
        $result = $serializer->normalize($result, 'json');
        $user_answers = $serializer->normalize($user_answers, 'json');
        $answers = $serializer->normalize($answers, 'json');
        $response = new JsonResponse();
        $response->setData(array(
            'question'=>$question,
            'image'=>$image,
//            'result'=>$result,
            'user_answers'=>$user_answers,
            'answers'=>$answers,
        ));
        return $response;
    }

}
?>