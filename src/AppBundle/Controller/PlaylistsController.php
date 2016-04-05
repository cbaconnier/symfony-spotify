<?php

namespace AppBundle\Controller;

use Monolog\Logger;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use SpotifyWebAPI\SpotifyWebAPI;
use Symfony\Component\HttpFoundation\Session\Session;

use AppBundle\Controller\TokensController;

class PlaylistsController extends Controller implements TokensController
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
        // Also the api doesn't provide a method to fetch the "next url", so I did manually.
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
     */
    public function playlistAction($owner, $playlist){
        $session = new Session();
        if (!$session->get('accessToken')) return $this->redirectToRoute("signin");

        $api = new SpotifyWebAPI();
        $api->setAccessToken($session->get('accessToken'));


        $limit = 100;
        $offset = 0;
        $playlist_arr = null;

        $playlist_tmp = $api->getUserPlaylist($owner, $playlist);
        $playlist_arr = $api->getUserPlaylistTracks($owner, $playlist);

        $playlist_arr->name = $playlist_tmp->name;
        $playlist_arr->description = $playlist_tmp->description;

        // Spotify imposes a limit of 100, we need to fetch all the playlists
        // Also the api doesn't provide a method to fetch the "next url", so I did manually.
        while($playlist_arr->next){
            $offset += $limit;
            $playlist_tmp = $api->getUserPlaylistTracks($owner, $playlist, ['limit' => $limit, 'offset' => $offset]);
            $playlist_arr->items = array_merge($playlist_arr->items, $playlist_tmp->items);
            $playlist_arr->next = $playlist_tmp->next;
        }



        return $this->render('playlists/playlist.html.twig', array(
            'playlist' => $playlist_arr
        ));
    }







}