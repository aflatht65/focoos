<?php
namespace SC\ProviderBundle\Extension;

use SC\UserBundle\Entity\User;

class EmailService {
    protected $container;

    public function __construct($container) {
        $this->container = $container;
    }

    protected function getTranslatedTemplate($lang, $template, array $params = array()) {
        // Make sure the twig locale is correct
        // if(!$this->container->isScopeActive('request')) {
        //     $this->container->enterScope('request');
        //     $request = new Request();
        //     $request->setLocale($lang);
        //     $this->container->set('request', $request);
        // }
        // $oldlang = $this->container->get('request')->getLocale();
        // $this->container->get('request')->setLocale($lang);
        $params['locale'] = $lang;
        $result = $this->container->get('twig')->render($template, $params); // Render the template
        // $this->container->get('request')->setLocale($oldlang);
        return $result;
    }

    protected function getEmailObject(array $options = array()) {
        if(!isset($options['to']) || $options['to'] == "") {
            throw new \Exception('Email must have a to address');
        } else if(!isset($options['subject']) || $options['subject'] == "") {
            throw new \Exception('Email must have a subject');
        } else if(!isset($options['template'])) {
            throw new \Exception('Email must have a note');
        }
        if(!isset($options['translationdomain']) || $options['translationdomain'] == "") {
            $options['translationdomain'] = 'emails';
        }
        if(!isset($options['params'])) {
            $options['params'] = array();
        }
        $lang = isset($options['lang']) ? $options['lang'] : 'el';
        // Send message
        $message = \Swift_Message::newInstance()
                ->setContentType('text/html')
                ->setSubject($this->container->get('translator')->trans($options['subject'], array(), $options['translationdomain'], $lang))
                ->setFrom('notification@socialcouriers.com', $this->container->get('translator')->trans('SocialCouriers', array(), 'emails', $lang))
                //->setReplyTo($replyTo)
                ->setTo($options['to'])
                ->setBody($this->getTranslatedTemplate($lang, 'SCProviderBundle:Emails\transactions:'.$options['template'].'.html.twig', $options['params']));
        if(isset($options['attachmentFromPath']) && $options['attachmentFromPath'] != "") {
            $message->attach(\Swift_Attachment::fromPath($options['attachment']));
        }
        if(isset($options['attachment']) && $options['attachment'] instanceof \Swift_Attachment) {
            $message->attach($options['attachment']);
        }
        if(isset($options['attachment']) && is_array($options['attachment'])) {
            foreach($options['attachment'] as $curAttachment) {
                if(!$curAttachment instanceof \Swift_Attachment) { throw new \Exception('Attachment not instance of Swift_Attachment'); }
                $message->attach($curAttachment);
            }
        }
        if(isset($options['category']) && $options['category'] != "") {
            $message->getHeaders()->addTextHeader('X-SMTPAPI', json_encode(array('category' => array($options['category'])))); // SMTPAPI header
        }
        return $message;
    }

    protected function email(array $options = array()) {
        try {
            $message = $this->getEmailObject($options);
            $this->container->get('mailer')->send($message);
        } catch(\Exception $e) {
            if(strpos($this->container->get('kernel')->getEnvironment(), 'test') === false) {
                $this->container->get('logger')->err('Email '.$options['subject'].' not sent: '.$e->__toString());
                return false;
            } else {
                throw $e;
            }
        }
    }

    public function createWelcomeEmail(User $user) {
        return $this->email(array(
            'to' => $user->getEmail(),
            'subject' => 'Καλώς όρισες στο SocialCouriers!',
            'template' => 'welcome',
            'params' => array('receiver' => $user),
            'category' => 'welcome_email', // SMTPAPI header
        ));
    }
}
