<?php

namespace SC\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use SC\UserBundle\Form\Type\ProfilePhotoType;
use SC\UserBundle\Entity\User;

class FrontpageController extends Controller {
    /**
     * @Route("/", name="home")
     * @Secure(roles="ROLE_USER")
     */
    public function frontpageAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $lessonIds = array();
        foreach($user->getLessons() as $curLesson) {
            $lessonIds[] = $curLesson->getId();
        }
        $posts = $this->container->get('doctrine')->getRepository('SC\SiteBundle\Entity\Post')->findPosts(array(
            'lessons' => $lessonIds,
        ));
        return $this->render('SCSiteBundle::frontpage.html.twig', array(
            'posts' => $posts,
        ));
    }

    /**
     * @Route("upload_photo", name="upload_photo")
     * @Secure(roles="ROLE_USER")
     */
    public function uploadPhoto() {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new ProfilePhotoType(), $user);
        if ('POST' == $this->getRequest()->getMethod()) {
            $form->bind($this->getRequest());
            if($form->isValid()) {
                // Photo
                if($user->getNewPhoto() != null) {
                    $user->getPhoto()->setPhoto($user->getNewPhoto());
                    $this->container->get('doctrine')->getManager()->persist($user->getPhoto());
                    $this->container->get('stof_doctrine_extensions.uploadable.manager')->markEntityToUpload($user->getPhoto(), $user->getPhoto()->getPhoto());
                }
                // End photo
                $this->container->get('doctrine')->getManager()->persist($user);
                $this->container->get('doctrine')->getManager()->flush($user);
            }
        }
        return new Response('/'.$user->getAvatarUrl());
    }

    /**
     * @Route("/profile/{user}", name="other_profile")
     * @Secure(roles="ROLE_USER")
     */
    public function showOtherAction(User $user) {
        return $this->render('SCUserBundle:Profile:show.html.twig', array(
            'user' => $user,
        ));
    }
}
