<?php
namespace SC\ProviderBundle\Extension;

use SC\ProviderBundle\Exception\FacebookException;

class FacebookService {
    protected $container;
    protected $namespace;

    public function __construct($container) {
        $this->container = $container;
        $this->namespace = $this->container->getParameter('fb_appNamespace');
    }

    public function isTokenValid($accessToken) {
        $graph_url = "https://graph.facebook.com/me?access_token=" . $accessToken ;
        $output = $this->performRequest($graph_url);

        // handle error; error output
        if (isset($output->error)) {
            return false;
        }

        return true;
    }

    public function getTokenData($accessToken) {
        $graph_url = "https://graph.facebook.com/debug_token?input_token=".$accessToken."&access_token=".$this->container->getParameter('fb_appId')."|".$this->container->getParameter('fb_appSecret');
        $output = $this->performRequest($graph_url);

        // handle error; error output
        if (isset($output->error)) {
            return false;
        }

        return $output;
    }

    public function getFbUserByToken($accessToken, &$error = null) {
        if($accessToken == '') { $error = array('message' => 'no_token', 'code' => 0, 'error_subcode' => 0); return false; }
        $graph_url = "https://graph.facebook.com/me?access_token=" . $accessToken ;
        $output = $this->performRequest($graph_url);

        // handle error; error output
        if (isset($output->error)) {
            $error = $output->error;
            return false;
        }

        return $output;
    }

    public function createUserByFbUser($fb_user, $access_token) {
        if(!isset($fb_user->email)) {
            throw new FacebookException('The fb_user does not have the email scope. Cannot proceed.');
        }
        $user = $this->container->get('fos_user.user_manager')->createUser();
        $user->setUsername($this->container->get('sc.tokenservice')->randomString());
        $user->setPlainPassword($this->container->get('sc.tokenservice')->randomString());
        $user->setName($fb_user->first_name);
        $user->setSurname($fb_user->last_name);
        $user->setEmail($fb_user->email);
        $user->setFbId($fb_user->id);
        $user->setFbAccessToken($access_token);
        return $user;
    }

    protected function performRequest($graph_url, $toArray = false) {
        // make request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $graph_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $output = curl_exec($ch);

        // convert response
        $output = json_decode($output, $toArray);

        if(!isset($output->error) && curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
            $output->error = 'Non-200 HTTP code '.curl_getinfo($ch, CURLINFO_HTTP_CODE);
        }

        curl_close($ch);

        return $output;
    }

}
