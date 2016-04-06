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


class AlbumsController extends Controller implements TokensController
{
    /**
     * @Route("/albums/{artist}", name="albums")
     */
    public function albumsAction($artist)
    {
        $session = new Session();

        //specify the token to the api
        $api = new SpotifyWebAPI();
        $api->setAccessToken($session->get('accessToken'));

        $limit = 50;
        $offset = 0;
        $albums = null;


        $albums = $api->getArtistAlbums($artist, ['limit' => $limit]);
        $albums->artistId = $artist;


        // Spotify imposes a limit of 50, we need to fetch all the playlists
        // Also the api doesn't provide a method to fetch the "next url", so I did manually.
        while($albums->next) {
            $offset += $limit;
            $albums_tmp = $api->getArtistAlbums($artist, ['limit' => $limit, 'offset' => $offset]);
            $albums->items = array_merge($albums->items, $albums_tmp->items);
            $albums->next = $albums_tmp->next;
        }

        //Spotify provide albums with duplicates, weeee
        $albums_ref = $albums;

        foreach($albums->items as $key => $val){
            foreach($albums_ref->items as $refKey => $refVal){
                if($key != $refKey && strtolower($val->name) == strtolower($refVal->name)){
                    unset($albums->items[$key]);
                }
            }
        }

        //$albums->items = array_map('json_decode', $items);

        return $this->render('albums/albums.html.twig', array(
            'albums' => $albums
        ));
    }

}