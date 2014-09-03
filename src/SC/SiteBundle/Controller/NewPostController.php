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
        $post = new Post();
        $exercise = new Exercise();
        $deadline = new Deadline();
        $note = new Note();
        if($this->getRequest()->get('lesson') != null) {
            $lesson = $this->container->get('doctrine')->getRepository('SC\UserBundle\Entity\Lesson')->find($this->getRequest()->get('lesson'));
            $post->setLesson($lesson);
            $exercise->setLesson($lesson);
            $deadline->setLesson($lesson);
            $note->setLesson($lesson);
        }
        $postForm = $this->createForm(new PostType(), $post, array('user' => $user));
        $exerciseForm = $this->createForm(new ExerciseType(), $exercise, array('user' => $user));
        $deadlineForm = $this->createForm(new DeadlineType(), $deadline, array('user' => $user));
        $noteForm = $this->createForm(new NoteType(), $note, array('user' => $user));
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
        return $this->submitForm(new PostType(), new Post(), function() {});
    }

    /**
     * @Route("/post/new/exercise", name="submit_new_exercise")
     * @Secure(roles="ROLE_USER")
     */
    public function newSubmitExerciseAction() {
        return $this->submitForm(new ExerciseType(), new Exercise(), function($entity) {
            // Photo
            if($entity->getExercise() != null) {
                $this->container->get('stof_doctrine_extensions.uploadable.manager')->markEntityToUpload($entity, $entity->getExercise());
            }
            // End photo
        });
    }

    /**
     * @Route("/post/new/deadline", name="submit_new_deadline")
     * @Secure(roles="ROLE_USER")
     */
    public function newSubmitDeadlineAction() {
        return $this->submitForm(new DeadlineType(), new Deadline(), function() {});
    }

    /**
     * @Route("/post/new/note", name="submit_new_note")
     * @Secure(roles="ROLE_USER")
     */
    public function newSubmitNoteAction() {
        return $this->submitForm(new NoteType(), new Note(), function($entity) {
            // Photo
            if($entity->getNote() != null) {
                $this->container->get('stof_doctrine_extensions.uploadable.manager')->markEntityToUpload($entity, $entity->getNote());
            }
            // End photo
        });
    }

    protected function submitForm($formType, $entity, $callback) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $entity->setStudent($user);
        $form = $this->createForm($formType, $entity, array('user' => $user));
        if ('POST' == $this->getRequest()->getMethod()) {
            $form->bind($this->getRequest());
            if($form->isValid()) {
                $this->container->get('doctrine')->getManager()->persist($entity);
                $callback($entity);
                $this->container->get('doctrine')->getManager()->flush($entity);
                return new RedirectResponse($this->container->get('router')->generate('home'));
            }
        }
        return new RedirectResponse($this->container->get('router')->generate('new_post'));
    }
}
