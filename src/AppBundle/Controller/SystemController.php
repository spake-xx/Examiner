<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SystemController extends Controller
{
    protected function getNextQuestion($attempt)
    {
        $questions_answered = array();
        $questions_all = array();


        $em = $this->getDoctrine()->getManager();
        $attempt = $em->getRepository('AppBundle:Attempt')->find($attempt);
        $questions = $em->getRepository('AppBundle:Question')->findByQuiz($attempt->getQuiz());
        $u_answers = $em->getRepository('AppBundle:UserAnswer')->findByAttempt($attempt);

        foreach ($questions as $q) {
            $questions_all[] = $q->getId();
        }

        foreach ($u_answers as $answer) {
            $questions_answered[] = $answer->getAnswer()->getQuestion()->getId();
        }

        $questions_notanswered = array_diff($questions_all, $questions_answered);
        if(empty($questions_notanswered)) return null;
//        if(!$questions_notanswered[array_rand($questions_notanswered)]){
//            print "TOP KEK";
//            return null;
//        }
        $rand = $questions_notanswered[array_rand($questions_notanswered)];
        $question = $em->getRepository('AppBundle:Question')->find($rand);
        if($question) {
            return $question;
        }
//        else{
//            return null;
//        }
    }
}