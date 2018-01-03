<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;




//Request::setTrustedProxies(array('127.0.0.1'));
require __DIR__.'/../src/sallesControllers.php';
require __DIR__.'/../src/entrepriseControllers.php';
require __DIR__.'/../src/usersControllers.php';
require __DIR__.'/../web/librairie.php';



//Définition de la route page d'accueil

//////////////////////////////////////////////////////////////////////////

$app->get('/', function () use ($app) {
	$model= new sallesModels();
	$slider = $model -> getMeilleuresSalles($app);
	$model= new Models();
	$categos = $model -> selectCategorie($app);
    return $app['twig']->render('accueil.html.twig', array(
    	'categos' => $categos,
    	'user'=> $app['session']->get('user'),
    	'slider'=> $slider
    ));
})
->bind('homepage')
;


$app->post('/recherche', function () use ($app) {

	$model = new Models();
	$search = $_POST['pays'];
	$status = false;
	$result = "";

	//Je vérifie ce qui a été tapé
	if($search != "")
	{
		//Je recherche en base
		$result = $model->recherchePredictive($app,$search);
		$status = true;
	}
	//Je recherche en base le résultats liés (Model)

	return json_encode(array(
		'status' => $status,
		'result' => $result
	));
})
->bind('json_search')
;


//////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////////

$app->get('/mention_legale', function () use ($app) {
    return $app['twig']->render('mentions_legales.html.twig', array('user'=>$app['session']->get('user')));
})
->bind('mention_leg')
;

//////////////////////////////////////////////////////////////////////////

$app->get('/contact', function () use ($app) {
    return $app['twig']->render('contact.html.twig', array('user'=>$app['session']->get('user')));
})
->bind('contacter')
;

$app->post('/contact', function () use ($app){
$model = new Models();
$contact = $model -> repContact($app,$_POST);
$champs = array("nom", "email", "message");

//SI user exist en session
	//Vérifier que l'utili. exist vraiment en base
		// $_POST['nom'] = $_session['user']['nom']
		// .. idem pour mail
	//Sinon lui dire d'aller se faire enculer
//Sinon
	// Utiliser le $_POST
		//Qui post contient
			// - nom
			// - mail
			// - message

// Vérif du $_POST
	// Si ok enr. en base
	// Sinon traiter l'erreur

		if(verif_form($_POST, $champs)/*, strlen($champs)*/)
		{
			return $app->redirect($app["url_generator"]->generate("homepage"));
		}else{
			return $app->redirect($app["url_generator"]->generate("contacter"));

		}
})
->bind('postcontact')
;


//gestion des erreurs,
$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html.twig',
        'errors/'.substr($code, 0, 2).'x.html.twig',
        'errors/'.substr($code, 0, 1).'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
/////////////////////////////////////////////////////////////////////////




