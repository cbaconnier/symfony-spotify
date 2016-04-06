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


class ArtistsController extends Controller implements TokensController
{


    /**
     * @Route("/artists", name="artists")
     */
    public function playlistsAction()
    {
        $session = new Session();

        //specify the token to the api
        $api = new SpotifyWebAPI();
        $api->setAccessToken($session->get('accessToken'));


        $limit = 50;
        $artists = null;

        $artists = $api->getUserFollowedArtists(['limit' => $limit])->artists;

        // Spotify imposes a limit of 50, we need to fetch all the playlists
        // Also the api doesn't provide a method to fetch the "next url", so I did manually.
        while($artists->next) {
            $artists_tmp = $api->getUserFollowedArtists(['limit' => $limit, 'after' => $artists->cursors->after])
                ->artists;
            $artists->items = array_merge($artists->items, $artists_tmp->items);
            $artists->next = $artists_tmp->next;
        }

        return $this->render('artists/artists.html.twig', array(
            'artists' => $artists
        ));

    }






}