<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Grade;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;


class GradeController extends Controller{
    /**
     * @Route("/teacher/grade/new", name="teacher_grade_new")
     */
    public function newClassAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $grade = new Grade();

        $form = $this->createFormBuilder($grade)
            ->add('name')
            ->getForm();

        $form->handleRequest($request);

        if($form->isValid()){
            $grade->setTeacher($this->getUser());
            $em->persist($grade);
            $em->flush();
            return $this->redirectToRoute('teacher_grade_register', array(
                'grade'=>$grade->getId()
            ));
        }

        return $this->render('teacher/new_group.html.twig', array(
            'form'=>$form->createView(),
        ));
    }

    /**
     * @Route("/teacher/grade/register/{grade}", name="teacher_grade_register")
     */
    public function registerClassAction(Request $request, $grade)
    {
        $em = $this->getDoctrine()->getManager();
        $grade = $em->getRepository('AppBundle:Grade')->find($grade);
        $pupils = $em->getRepository("AppBundle:User")->findByGrade($grade->getId());
        $form = $this->createFormBuilder($grade)
                ->add('name')
                ->getForm();


        return $this->render('teacher/grade_registration.html.twig', array(
            'grade'=>$grade,
            'pupils'=>$pupils,
//            'form'=>$form->createView(),
        ));
    }

    /**
     * @Route("/r/{grade}", name="pupil_register")
     */
    public function pupilRegisterAction(Request $request, $grade)
    {
        $em = $this->getDoctrine()->getManager();
        $grade = $em->getRepository('AppBundle:Grade')->find($grade);

        $userManager = $this->get('fos_user.user_manager');
        $formFactory = $this->get('fos_user.registration.form.factory');
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(1);
        $user->setRoles(array('ROLE_PUPIL'));
        $user->setGrade($grade);

//        $form = $this->createFormBuilder($user)
//            ->add('username')
//            ->add('email')
//            ->add('firstname')
//            ->add('lastname')
//            ->add('plainPassword', PasswordType::class)
//            ->getForm();
//        $form->handleRequest($request);

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);
        if($form->isValid()){
//            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
            $userManager->updateUser($user);
            $this->addFlash('notice', 'Twoje konto zostało pomyslnie zarejestrowane, teraz możesz sie zalogować ;)');
            return $this->redirectToRoute('fos_user_security_login');
        }

        return $this->render('pupil/register.html.twig', array(
            'form'=>$form->createView(),
            'grade'=>$grade,
        ));
    }
}