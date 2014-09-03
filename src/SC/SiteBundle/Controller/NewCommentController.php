<?php

namespace SC\SiteBundle\Controller;

use SC\SiteBundle\Entity\Post;
use SC\SiteBundle\Entity\Comment;
use SC\SiteBundle\Form\Type\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;

class NewCommentController extends Controller {
    /**
     * @Route("/post/{post}/comment/submit", name="submit_new_comment")
     * @Secure(roles="ROLE_USER")
     */
    public function newSubmitCommentAction(Post $post) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $comment = new Comment();
        $comment->setPost($post);
        $comment->setStudent($user);
        $form = $this->createForm(new CommentType(), $comment);
        if ('POST' == $this->getRequest()->getMethod()) {
            $form->bind($this->getRequest());
            if($form->isValid()) {
                $this->container->get('doctrine')->getManager()->persist($comment);
                $this->container->get('doctrine')->getManager()->flush($comment);
            }
        }
        $referer = $this->getRequest()->headers->get('referer');
        return new RedirectResponse($referer);
    }
}
