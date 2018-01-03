<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//Request::setTrustedProxies(array('127.0.0.1'));

$app->get('/', function () use ($app) 
{
    return $app['twig']->render('index.html.twig', array());
})
->bind('homepage')
;
//////////////////////////////////////////////////////////////////////////////////////////////////////////
//creation route page salle + affichage utilisateurs et salles

$app->get('/salles', function () use ($app) {
    return $app['twig']->render('salles.html.twig',array(
        'user' => $app['session']->get('user')
         ));
})
->bind('sallesescape')
;

//verif  saisie  formulaire

$app-> post('/salles', function(Request $request) use ($app)
{
$referant = $request->headers->get('referer');  

    if(isset($_POST))
    {
        $model = new sallesModels();

        if(!empty($_POST['commentaires']) && 
           !empty($_POST['notes']))
            
        {
             $vote = $model ->verifVote($app, $_POST); 
        }
        else
        {
            return $app['twig']->render('champs.html.twig',array());
        }

        if ($vote)// verifie si il a deja voté
        { 
            return $app['twig']->render('dejavote.html.twig',array()); 
        }
        else
        {
            $saisie = $model -> ajoutCommentaires($app, $_POST);  // envoie commentaires en base
            return $app->redirect($referant);  
        }

    }
})
->bind('verif')
;


//route affichage commentaires et note stockés en base

$app->get('/salles/{id}',function($id) use ($app)
{

        $model = new sallesModels();
        $infos = $model->getInfosSalles($app,$id);
        $entreprise = $model->getInfosEntreprise($app,$id);
        $commentaires = $model ->getCommentaires($app,$id);
        $imagesalle = $model->getImagesSalles($app,$id);
        
         $note = $model ->getNotes($app,$id);//charge la note moyenne
         $reste = 5 - $note;
        
            return $app['twig']-> render ('salles.html.twig',array(
            'commentaires' => $commentaires,
            'note' => $note,
            'reste' => $reste, 
            'infos'=> $infos,
            'entreprise'=>$entreprise,
            'imagesalle'=>$imagesalle,
            'user' => $app['session']->get('user')
         ));
})
->bind('comment')
;

//gestion des erreurs

$app->error(function (\Exception $e, Request $request, $code) use ($app)
{
    if ($app['debug'])
    {
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
