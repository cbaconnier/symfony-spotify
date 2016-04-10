<?php



namespace AppBundle\Controller;

use AppBundle\Entity\Search;
use AppBundle\Form\SearchType;
use Monolog\Logger;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use SpotifyWebAPI\Session as SpotifySession;
use SpotifyWebAPI\SpotifyWebAPI;
use Symfony\Component\HttpFoundation\Session\Session;

use AppBundle\Controller\TokensController;


class SearchController extends Controller implements TokensController
{

    /**
     * @Route("/search", name="search")
     */
    public function searchAction(Request $request){
        $session = new Session();

        //specify the token to the api
        $api = new SpotifyWebAPI();
        $api->setAccessToken($session->get('accessToken'));

        $search = new Search();
        $form = $this->createForm(new SearchType(), $search, ['csrf_protection' => false]);

        $form->handleRequest($request);


       // if($form->isValid()){

            $results = $api->search($form->get('query')->getData(), ['album', 'artist', 'playlist', 'track']);

            return $this->render('search/search.html.twig', array(
                'results' => $results
            ));

        //}



        //return $this->redirectToRoute("playlists");


    }

    /**
     * @Route("/searchForm", name="searchForm")
     */
    public function searchFormAction(Request $request)
    {

        $search = new Search();
        $form = $this->createForm(new SearchType(), $search, ['csrf_protection' => false]);
        return $this->render('searchForm.html.twig', array(
            'form' => $form->createView()
        ));



    }
}