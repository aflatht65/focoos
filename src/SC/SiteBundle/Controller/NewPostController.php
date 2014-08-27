<?php

namespace SC\SiteBundle\Controller;

use SC\SiteBundle\Entity\Post;
use SC\SiteBundle\Entity\Exercise;
use SC\SiteBundle\Entity\Deadline;
use SC\SiteBundle\Entity\Note;
use SC\SiteBundle\Form\Type\PostType;
use SC\SiteBundle\Form\Type\ExerciseType;
use SC\SiteBundle\Form\Type\DeadlineType;
use SC\SiteBundle\Form\Type\NoteType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;

class NewPostController extends Controller {
    /**
     * @Route("/post/new", name="new_post")
     * @Secure(roles="ROLE_USER")
     */
    public function newPostAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $postForm = $this->createForm(new PostType(), new Post(), array('user' => $user));
        $exerciseForm = $this->createForm(new ExerciseType(), new Exercise(), array('user' => $user));
        $deadlineForm = $this->createForm(new DeadlineType(), new Deadline(), array('user' => $user));
        $noteForm = $this->createForm(new NoteType(), new Note(), array('user' => $user));
        return $this->render('SCSiteBundle::new_post.html.twig', array(
            'postForm' => $postForm->createView(),
            'exerciseForm' => $exerciseForm->createView(),
            'deadlineForm' => $deadlineForm->createView(),
            'noteForm' => $noteForm->createView(),
        ));
    }

    /**
     * @Route("/post/new/post", name="submit_new_post")
     * @Secure(roles="ROLE_USER")
     */
    public function newSubmitPostAction() {

    }

    /**
     * @Route("/post/new/exercise", name="submit_new_exercise")
     * @Secure(roles="ROLE_USER")
     */
    public function newSubmitExerciseAction() {

    }

    /**
     * @Route("/post/new/deadline", name="submit_new_deadline")
     * @Secure(roles="ROLE_USER")
     */
    public function newSubmitDeadlineAction() {

    }

    /**
     * @Route("/post/new/note", name="submit_new_note")
     * @Secure(roles="ROLE_USER")
     */
    public function newSubmitNoteAction() {

    }

    protected function submitForm($formType, $entity) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $form = $this->createForm($formType, $entity);
        if ('POST' == $this->getRequest()->getMethod()) {
            $form->bind($this->getRequest());
            if($form->isValid()) {
                $this->container->get('doctrine')->getManager()->persist($entity);
                // Photo
                if($entity->getNewPhoto() != null) {
                    $entity->getPhoto()->setPhoto($entity->getNewPhoto());
                    $this->container->get('doctrine')->getManager()->persist($entity->getPhoto());
                    $this->container->get('stof_doctrine_extensions.uploadable.manager')->markEntityToUpload($entity->getPhoto(), $entity->getPhoto()->getPhoto());
                }
                // End photo
                $this->container->get('doctrine')->getManager()->flush($entity);
                return new RedirectResponse($this->container->get('router')->generate('home'));
            }
        }
        return $this->render('SCSiteBundle::new_post.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
