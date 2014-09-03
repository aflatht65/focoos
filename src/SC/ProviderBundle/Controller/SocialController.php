<?php
namespace SC\ProviderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use SC\ProviderBundle\Exception\FacebookException;

class SocialController extends Controller {
    /**
     * @Route("/facebook_login", name="facebook_login", options={"expose"=true})
     */
    public function facebookLoginAction(Request $request) {
        $app_id = $this->container->getParameter('fb_appId');
        $app_secret = $this->container->getParameter('fb_appSecret');
        $my_url = $request->getSchemeAndHttpHost() . $this->generateUrl('facebook_login');
        $requestattributes = $request->query->all();
        if($request->getSession()->get('_security.public.target_path') != null) { $request->getSession()->set('_security.public.fb_target_path', $request->getSession()->get('_security.public.target_path')); }
        unset($requestattributes['state']);
        unset($requestattributes['code']);
        if(count($requestattributes) > 0) {
            $my_url .= '?'.http_build_query($requestattributes);
        }

        //$code = $_REQUEST["code"];
        $code = $request->get('code');
        $error = $request->get('error');

        if (empty($code) && empty($error)) {
            $this->container->get('session')->set('state', md5(uniqid(rand(), TRUE))); //CSRF protection
            $dialog_url = "https://www.facebook.com/dialog/oauth?client_id="
                    . $app_id . "&redirect_uri=" . urlencode($my_url) . "&state="
                    . $this->container->get('session')->get('state') . "&scope=email";

            return new RedirectResponse($dialog_url);
        } else if(empty($code)) { // Error is not empty
            $this->container->get('session')->getFlashBag()->add('error', 'You have not provided email permission');
            return new RedirectResponse($this->generateUrl('fos_user_registration_register'));
        }

        $state = $request->get('state');
        //if($this->container->get('session')->get('state') && ($this->container->get('session')->get('state') === $state)) {
        $token_url = "https://graph.facebook.com/oauth/access_token?"
                . "client_id=" . $app_id . "&redirect_uri=" . urlencode($my_url)
                . "&client_secret=" . $app_secret . "&code=" . $code;

        $response = file_get_contents($token_url);
        $params = null;
        parse_str($response, $params);

        $graph_url = "https://graph.facebook.com/me?access_token=" . $params['access_token'];

        $fb_user = json_decode(file_get_contents($graph_url));
        $email = $fb_user->email;
        $fb_id = $fb_user->id;

        $em = $this->getDoctrine()->getManager();
        if(!isset($fb_id)) {
            throw new AccessDeniedException('Couldn\'t find facebook id');
        }
        $users = $em->getRepository('\SC\UserBundle\Entity\User')->findBy(array(
            'fbId' => $fb_id
        ));
        if(count($users) > 0) {
            $user = $users[0];
        } else {
            $user = null;
        }
        if (is_null($user)) {
            $userManager = $this->container->get('fos_user.user_manager');
            $user = $userManager->findUserByEmail($email);
        }
        if (!is_null($user)) {
            // User exists but their email is out of date. We simply update it.
            $m = $user->getFbEmail();
            if (is_null($m) || empty($m)) {
                $user->setFbEmail($email);
                $user->setFbId($fb_user->id);
            }
            $user->setFbAccessToken($params['access_token']);
            $em->persist($user);
            $em->flush();

            $this->container->get('authentication_handler')->authenticateUser($user);
            $response = $this->container->get('authentication_handler')->onAuthenticationSuccess($request, $this->container->get('security.context')->getToken());
            $this->container->get('authentication_handler')->onInteractiveLogin(new InteractiveLoginEvent($request, $this->container->get('security.context')->getToken()));
            // Add a remember me Cookie if the parameter was passed
            if($request->get('_remember_me') === 'on') {
                $request->getSession()->set('authRemember', true);
            }
            $request->getSession()->set('auth', $user->getSalt());
            if($request->getSession()->get('_security.public.fb_target_path') != null) {
                $redir = $request->getSession()->get('_security.public.fb_target_path');
                return new RedirectResponse($redir);
            }
            //return new RedirectResponse($this->generateUrl('home'));
            return $response;
        } else {
            // User isn't registered. We redirect to the login form with some fields prefilled
            try {
                $newUser = $this->container->get('sc.facebookservice')->createUserByFbUser($fb_user, $params['access_token']);
                $this->container->get('session')->set('register_prefill', array(
                    'name' => $newUser->getName(),
                    'surname' => $newUser->getSurname(),
                    'email' => $newUser->getEmail(),
                    'fbId' => $newUser->getFbId(),
                    'fbEmail' => $newUser->getFbEmail(),
                    'fbAccessToken' => $newUser->getFbAccessToken(),
                ));
                return new RedirectResponse($this->generateUrl('fos_user_registration_register', $this->getRequest()->query->all()));
            } catch(FacebookException $e) {
                $this->container->get('session')->getFlashBag()->add('error', 'You have not provided email permission');
                return new RedirectResponse($this->generateUrl('home'));
            }
        }

        /* } else {
          return new Response("The state does not match. You may be a victim of CSRF.");
          } */
    }

    /**
     * @Route("/disconnect_from_facebook", name="disconnect_from_facebook", options={"expose"=true})
     */
    public function disconnectAccountFromFbAction(Request $request) {

        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        $user = $this->container->get('security.context')->getToken()->getUser();

        $user->setFbEmail(null);
        $user->setFbId(null);
        $user->setFbAccessToken('');
        $user->setLastfbfriendsupate(null);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return new RedirectResponse($this->getRequest()->headers->get('referer'));
    }
}

?>
