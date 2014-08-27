<?php
namespace SC\CommonBundle\Extension;

use JMS\SecurityExtraBundle;

class TokenService {

    protected $container;

    public function __construct($container) {
        $this->container = $container;
    }

    public function randomNumber($length = 5) {
        $num = '';
        for($i = 0; $i < $length; $i++) {
            $num .= mt_rand ( 0, 9 );
        }
        return $num;
    }

    /**
     * Return a random alphanumeric string
     *
     * @return string
     */
    public function randomString() {
        $generator = $this->container->get('security.secure_random');
        $bytes = $generator->nextBytes(16);
        return bin2hex($bytes);
    }
}
