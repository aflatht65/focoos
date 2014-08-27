<?php

namespace SC\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SCUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
