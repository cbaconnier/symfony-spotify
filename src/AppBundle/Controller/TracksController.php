<?php



namespace AppBundle\Controller;

use Monolog\Logger;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use SpotifyWebAPI\Session as SpotifySession;
use SpotifyWebAPI\SpotifyWebAPI;
use Symfony\Component\HttpFoundation\Session\Session;

use AppBundle\Controller\TokensController;


class TracksController extends Controller implements TokensController
{

    /**
     * @Route("/tracks/{album}", name="album")
     */
    public function albumAction($album)
    {
        $session = new Session();

        //specify the token to the api
        $api = new SpotifyWebAPI();
        $api->setAccessToken($session->get('accessToken'));


        $album = $api->getAlbum($album);

        return $this->render('tracks/album.html.twig', array(
            'album' => $album
        ));

    }
}