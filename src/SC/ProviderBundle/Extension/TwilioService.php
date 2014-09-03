<?php
namespace SC\ProviderBundle\Extension;

use SC\UserBundle\Entity\User;

class TwilioService {

    protected $container;
    protected $client;

    public function __construct($container) {
        $this->container = $container;

        $sid = "AC0fa58e65af2100873c41f1f37a16f5b4"; // Your Account SID from www.twilio.com/user/account
        $token = "87985feca8d8f93c9353ecaa3a7c926a"; // Your Auth Token from www.twilio.com/user/account
        $this->client = new \Services_Twilio($sid, $token);
    }

    public function newVerificationSMS(User $user, $tel = null) {
        if(!isset($tel)) {
            throw new \Exception('tel is mandatory');
        }
        $em = $this->container->get('doctrine')->getManager();
        $code = $this->container->get('sc.tokenservice')->randomNumber(4);
        $user->setSmsVerificationCode($code);
        $em->persist($user);
        $em->flush();

        $message = "SOCIALCOURIERS ΚΩΔΙΚΟΣ ΕΠΙΒΕΒΑΙΩΣΗΣ: ".$code;
        return $this->sendSMS($tel, $message);
    }

    public function newSMS(User $user, $text) {
        return $this->sendSMS($user,$text);
    }

    protected function sendSMS($user, $message) {
        if($user instanceof User) {
            if($user->getTel() == '') {
                // throw new \Exception('User '.$user->getId().' has no phone number');
                $this->container->
                    get('logger')->
                    warn('SMS not sent: User '.$user->getId().' has no phone number');
                return false;
            }
            if(strpos($user->getUsername(), 'bot_') !== false) {
                return false;
            }
            $tel = $user->getTel();
        } else {
            $tel = $user;
            if(strpos($tel, '+') === false) {
                $code = '+30';
            } else {
                $code = '';
            }
        }
        if($tel != '') {
            if(strpos($this->container->get('kernel')->getEnvironment(), 'test') === false) {
                try {
                    $sms = $this->client->account->sms_messages->create(
                      '+14698442630', // From a valid Twilio number
                      $code.$tel, // Text this number
                      $message
                    );
                    return $sms;
                } catch(\Services_Twilio_RestException $e) {
                    $logger = $this->container->get('logger');
                    $logger->warn('Twilio SMS send error for user '.$user.': '.$e->__toString());
                    return false;
                }
            } else {
                return true;
            }
        } else {
            return 'not_mobile_number';
        }
    }

    public function newSMSTel($tel, $text) {
        try {
            $sms = $this->client->account->sms_messages->create(
              '+14698442630', // From a valid Twilio number
              $tel, // Text this number
              $text
            );
            return $sms;
        } catch(\Services_Twilio_RestException $e) {
            $logger = $this->container->get('logger');
            $logger->warn('Twilio SMS send error for tel '.$tel.': '.$e->__toString());
            return false;
        }
    }

}
