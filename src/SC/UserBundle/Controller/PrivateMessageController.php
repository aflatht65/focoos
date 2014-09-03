<?php

namespace SC\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use SC\UserBundle\Form\Type\PrivateMessageType;
use SC\UserBundle\Entity\User;
use SC\UserBundle\Entity\PrivateMessage;

class PrivateMessageController extends Controller {
    /**
     * @Route("/inbox", name="inbox")
     * @Secure(roles="ROLE_USER")
     */
    public function inboxAction() {
        $luser = $this->container->get('security.context')->getToken()->getUser();
        $conversations = $this->container->get('doctrine')->getRepository('SC\UserBundle\Entity\PrivateMessage')->findLastPrivateMessagesByReceiver($luser);
        foreach($conversations as &$curConversation) {
            $this->container->get('doctrine')->getManager()->refresh($curConversation->getSender());
        }
        return $this->render('SCSiteBundle:PrivateMessage:inbox.html.twig', array(
            'conversations' => $conversations,
        ));
    }

    /**
     * @Route("/inbox/{user}", name="thread")
     * @Secure(roles="ROLE_USER")
     */
    public function threadAction(User $user) {
        $luser = $this->container->get('security.context')->getToken()->getUser();
        $messages = $this->container->get('doctrine')->getRepository('SC\UserBundle\Entity\PrivateMessage')->findPrivateMessages(array(
            'between' => array($luser, $user),
        ));
        return $this->render('SCSiteBundle:PrivateMessage:thread.html.twig', array(
            'user' => $user,
            'messages' => $messages,
        ));
    }

    /**
     * @Route("/inbox/{user}/new_message", name="submit_new_private_message")
     * @Secure(roles="ROLE_USER")
     */
    public function newSubmitCommentAction(User $user) {
        $luser = $this->container->get('security.context')->getToken()->getUser();
        $privateMessage = new PrivateMessage();
        $privateMessage->setSender($luser);
        $privateMessage->setReceiver($user);
        $form = $this->createForm(new PrivateMessageType(), $privateMessage);
        if ('POST' == $this->getRequest()->getMethod()) {
            $form->bind($this->getRequest());
            if($form->isValid()) {
                $this->container->get('doctrine')->getManager()->persist($privateMessage);
                $this->container->get('doctrine')->getManager()->flush($privateMessage);
            }
        }
        $referer = $this->getRequest()->headers->get('referer');
        return new RedirectResponse($referer);
    }
}
