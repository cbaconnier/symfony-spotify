<?php

namespace AppBundle\Controller;

use Monolog\Logger;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use SpotifyWebAPI\SpotifyWebAPI;
use Symfony\Component\HttpFoundation\Session\Session;



class ArtistsController extends Controller
{


    /**
     * @Route("/artists", name="artists")
     */
    public function playlistsAction()
    {
        $session = new Session();
        if (!$session->get('accessToken')) return $this->redirectToRoute("signin");


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


    /**
     * @Route("/artists/{artist}", name="albums")
     */
    public function albumsAction($artist)
    {
        $session = new Session();
        if (!$session->get('accessToken')) return $this->redirectToRoute("signin");


        $api = new SpotifyWebAPI();
        $api->setAccessToken($session->get('accessToken'));

        $limit = 50;
        $offset = 0;
        $albums = null;


        $albums = $api->getArtistAlbums($artist, ['limit' => $limit]);
        $albums->artistId = $artist;


        // Spotify imposes a limit of 50, we need to fetch all the playlists
        // Also the api doesn't provide a method to fetch the "next url", so I did manually.
        if($albums->next) {
            $offset += $limit;
            $albums_tmp = $api->getArtistAlbums($artist, ['limit' => $limit, 'offset' => $offset]);
            $albums->items = array_merge($albums->items, $albums_tmp->items);
            $albums->next = $albums_tmp->next;
        }

        return $this->render('artists/albums.html.twig', array(
            'albums' => $albums
        ));
    }

    /**
     * @Route("/artists/{artist}/{album}", name="album")
     */
    public function albumAction($artist, $album)
    {
        $session = new Session();
        if (!$session->get('accessToken')) return $this->redirectToRoute("signin");

        $api = new SpotifyWebAPI();
        $api->setAccessToken($session->get('accessToken'));


        $album = $api->getAlbum($album);


        return $this->render('artists/tracks.html.twig', array(
            'album' => $album
        ));

    }




}