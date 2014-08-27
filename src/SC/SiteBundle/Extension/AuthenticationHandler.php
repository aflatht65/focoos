<?php
namespace SC\SiteBundle\Extension;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\RememberMe\TokenBasedRememberMeServices;
use Symfony\Component\Security\Http\RememberMe\PersistentTokenBasedRememberMeServices;
use Symfony\Component\Security\Core\Authentication\RememberMe\InMemoryTokenProvider;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use FOS\UserBundle\Model\UserInterface;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, LogoutSuccessHandlerInterface {
    protected $container;

    public function __construct($container) {
        $this->container = $container;
    }

    public function authenticateUser(UserInterface $user) {
        try {
            $this->container->get('security.user_checker')->checkPostAuth($user);
        } catch (AccountStatusException $e) {
            // Don't authenticate locked, disabled or expired users
            return false;
        }

        $providerKey = $this->container->getParameter('fos_user.firewall_name');
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());

        $this->container->get('security.context')->setToken($token);
        return true;
    }

    // This triggers only on form login
    public function onAuthenticationSuccess(Request $request, TokenInterface $token) {
        //$referer = $request->headers->get('referer');
        $securityTargetPath = $request->getSession()->get('_security.public.target_path');
        if($token != null && is_object($token->getUser())) {
            $referer = ($securityTargetPath != null ? $securityTargetPath : $this->container->get('router')->generate('home'));
        }
        if($request->get('login_redirect') != null) {
            $referer = urldecode($request->get('login_redirect'));
        }
        return new RedirectResponse($referer);
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event) {
    }

    public function onLogoutSuccess(Request $request) {
        if($request->get('logout_redirect') != null) {
            $referer = urldecode($request->get('logout_redirect'));
        } else {
            $referer = $this->container->get('router')->generate('home');
        }
        return new RedirectResponse($referer);
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $session = $event->getRequest()->getSession();
        if($session->get('auth') != null) {
            $em = $this->container->get('doctrine')->getManager();
            $user = $em->getRepository('SCUserBundle:User')->findOneBy(array(
                'salt' => $session->get('auth'),
            ));
            $this->container->get('authentication_handler')->authenticateUser($user);
            if($session->get('authRemember') == true) {
                $rememberMeService = new TokenBasedRememberMeServices(
                    array($this->container->get('fos_user.user_manager')),
                    $this->container->getParameter('secret'), 'public', array(
                    'lifetime' => 31536000, // 30 days
                    'path' => '/',
                    'domain' => null,
                    'name' => 'REMEMBERME',
                    'secure' => null,
                    'httponly' => true,
                    'always_remember_me' => null,
                    'remember_me_parameter' => '_remember_me')
                );
                if($event->getResponse() == null) {
                    $response = new Response('');
                }
                $event->getRequest()->request->set('_remember_me', 'on');
                // $rememberMeService->setTokenProvider(new InMemoryTokenProvider());
                $rememberMeService->loginSuccess($event->getRequest(), $response, $this->container->get('security.context')->getToken());
                //forcing cookie write (necessary for REMEMBERME to work with FB login)
                $response->sendHeaders();
            }
            $session->remove('auth');
            $session->remove('authRemember');
        }
    }
}

?>
