<?php

namespace AppBundle\EventListener;

use AppBundle\Controller\TokensController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\HttpFoundation\Session\Session;
use SpotifyWebAPI\Session as SpotifySession;

class TokensListener
{
    private $client_id;
    private $secret_id;
    private $callbackUrl;

    public function __construct($client_id, $secret_id, $callbackUrl)
    {
        $this->client_id = $client_id;
        $this->secret_id = $secret_id;
        $this->callbackUrl = $callbackUrl;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof TokensController) {
            $session = new Session();
            if (!$session->get('accessToken')){
                $url = $this->router->generate("signin");
                $event->setResponse(new RedirectResponse($url));
            }

            //refresh the token
            if($session->get('tokenExpiration') >= time()){

                $spotify = new SpotifySession(
                    $this->client_id,
                    $this->secret_id,
                    $this->callbackUrl
                );

                $spotify->refreshAccessToken($session->get('refreshToken'));

                $session->set('accessToken', $spotify->getAccessToken());
                //$session->set('refreshToken', $spotify->getRefreshToken());
                $session->set('tokenExpiration', $spotify->getTokenExpiration());
            }
        }
    }
}