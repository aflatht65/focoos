<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SC\UserBundle\Form\Handler;

use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseHandler;
use FOS\UserBundle\Model\UserInterface;

class RegistrationFormHandler extends BaseHandler {
    private $tokenService;
    private $em;

    public function setTokenService($tokenService) {
        $this->tokenService = $tokenService;
    }

    public function setEntityManager($em) {
        $this->em = $em;
    }

    public function setUploadableManager($uploadableManager) {
        $this->uploadableManager = $uploadableManager;
    }

    public function process($confirmation = false) {
        $user = $this->userManager->createUser();
        $register_prefill = $this->request->getSession()->get('register_prefill');
        // Prefill form fields if the session variable has been set
        if(is_array($register_prefill)) {
            foreach($register_prefill as $curField => $curValue) {
                $method = 'set'.ucfirst($curField);
                $user->$method($curValue);
            }
        }

        $this->form->setData($user);
        $this->request->getSession()->remove('register_prefill'); // Remove prefilled data

        if ('POST' == $this->request->getMethod()) {

            $user->setUsername($this->tokenService->randomString());

            $this->form->bind($this->request);
            if ($this->form->isValid()) {
                $user = $this->form->getData();
                // Photo
                if($user->getNewPhoto() != null) {
                    $user->getPhoto()->setPhoto($user->getNewPhoto());
                    $this->em->persist($user->getPhoto());
                    $this->uploadableManager->markEntityToUpload($user->getPhoto(), $user->getPhoto()->getPhoto());
                }
                // End photo
                $this->onSuccess($user, $confirmation);
                return true;
            }
        }

        return false;
    }
}
