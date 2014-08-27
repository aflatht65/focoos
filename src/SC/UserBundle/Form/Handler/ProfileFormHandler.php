<?php

namespace SC\UserBundle\Form\Handler;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Form\Model\CheckPassword;
use FOS\UserBundle\Form\Handler\ProfileFormHandler as BaseHandler;

class ProfileFormHandler extends BaseHandler
{
    public function process(UserInterface $user)
    {
        $this->form->setData($user);

        if ('POST' === $this->request->getMethod() || 'PATCH' === $this->request->getMethod()) {
            $form_name = $this->form->getName();
            $form_values = $this->request->get($form_name);

            $this->form->submit($form_values, $this->request->getMethod() !== 'PATCH');

            if ($this->form->isValid()) {
                $this->onSuccess($user);
                return true;
            }

            // Reloads the user to reset its username. This is needed when the
            // username or password have been changed to avoid issues with the
            // security layer.
            $this->userManager->reloadUser($user);
        }

        return false;
    }
}
