<?php

namespace AppBundle\Controller;

use Monolog\Logger;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use SpotifyWebAPI\SpotifyWebAPI;
use Symfony\Component\HttpFoundation\Session\Session;



class PlaylistsController extends Controller
{


    /**
     * @Route("/playlists", name="playlists")
     */
    public function playlistsAction()
    {
        $session = new Session();
        if (!$session->get('accessToken')) return $this->redirectToRoute("signin");

        $api = new SpotifyWebAPI();
        $api->setAccessToken($session->get('accessToken'));

        $limit = 50;
        $offset = 0;
        $playlist = null;

        $playlists = $api->getMyPlaylists(['limit' => $limit]);

        // Spotify imposes a limit of 50, we need to fetch all the playlists
        // Alsom the api doesn't provide a method to fetch the "next url", so I did manually.
        while($playlists->next){
            $offset += $limit;
            $playlists_tmp = $api->getMyPlaylists(['limit' => $limit, 'offset' => $offset]);
            $playlists->items = array_merge($playlists->items, $playlists_tmp->items);
            $playlists->next = $playlists_tmp->next;
        }


        return $this->render('playlists/playlists.html.twig', array(
            'playlists' => $playlists->items
        ));

    }


    /**
     * @Route("/playlists/{owner}/{playlist}", name="playlist"),
     *     defaults={"owner"="logitux", "playlist"="1H13g4Mda3mrtGgwhxjPkA"}
     */
    public function playlistAction($owner, $playlist){
        $session = new Session();
        if (!$session->get('accessToken')) return $this->redirectToRoute("signin");

        $api = new SpotifyWebAPI();
        $api->setAccessToken($session->get('accessToken'));



        $playlist = $api->getUserPlaylist($owner, $playlist);



        return $this->render('playlists/playlist.html.twig', array(
            'playlist' => $playlist
        ));
    }







}