<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use SpotifyWebAPI;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{
    /**
     * @Route("/signin", name="signin")
     */
    public function signinAction()
    {

        // parameters in app/config/config.yml
        $spotify = new SpotifyWebAPI\Session(
            $this->getParameter('client_id'),
            $this->getParameter('secret_id'),
            $this->getParameter('callbackUrl')
        );

        $scopes = array(
            'playlist-read-private',
            'user-read-private'
        );

        $authorizeUrl = $spotify->getAuthorizeUrl(array(
            'scope' => $scopes
        ));

        return $this->redirect($authorizeUrl);
    }


    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {

        // parameters in app/config/config.yml
        $spotify = new SpotifyWebAPI\Session(
            $this->getParameter('client_id'),
            $this->getParameter('secret_id'),
            $this->getParameter('callbackUrl')
        );
        $api = new SpotifyWebAPI\SpotifyWebAPI();

        // Request a access token using the code from Spotify
        $spotify->requestAccessToken($request->query->get('code'));
        $accessToken = $spotify->getAccessToken();

        // Set the access token on the API wrapper
        $api->setAccessToken($accessToken);

        $session = new Session();
        $session->set('accessToken', $accessToken);

        return $this->redirectToRoute("homepage");

    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAccessAction(){
        $session = new Session();
        if( ! $session->get('accessToken')) return $this->redirectToRoute("signin");


        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken($session->get('accessToken'));
        $track = $api->getTrack('7EjyzZcbLxW7PaaLua9Ksb');
        $track = $api->currentUserFollows('user', 'spotify');

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'track' => $track
        ));

    }

}
