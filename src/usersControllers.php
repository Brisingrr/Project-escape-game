<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once ('../web/PHPMailer/autoload.php');




///////////////////////////////////////////////////////////////////////////////////////
$app->get('/inscription', function () use ($app) {                        
    return $app['twig']->render('inscription.html.twig', array());    
})                                                              
->bind('inscri')                                                                                  
;
/////////////////////////////////
$app->post('/inscription', function () use ($app){

    $message = array();

    if(isset($_POST) && !empty($_POST)){
        $nom = htmlentities($_POST['nom']);
        $prenom = htmlentities($_POST['prenom']);
        $pseudo = htmlentities($_POST['pseudo']);
        $code_postal = htmlentities($_POST['code_postal']);
        $ville = htmlentities($_POST['ville']);
        $email = htmlentities($_POST['email']);
        $email2 = htmlentities($_POST['email2']);
        $pass1 = htmlentities($_POST['pass1']);
        $pass2 = htmlentities($_POST['pass2']);

        $model = new Models();

        //Initialisation des message d'erreurs
        $errEmpty = 'ce champ est vide';
        $errMailSyn = 'cet email n\'est pas valide';


        //Vérification de la complétion de chaque champs
        if(empty($nom))
        {
            $message['nom'][] = $errEmpty;
        }
        if(empty($prenom))
        {
            $message['prenom'][] = $errEmpty;
        }
        if(empty($pseudo))
        {
            $message['pseudo'][] = $errEmpty;
        }
        if(empty($code_postal))
        {
            $message['code_postal'][] =  $errEmpty;
        }
        if(empty($ville))
        {
            $message['ville'][] = $errEmpty;

        }

        //Check mail
        if(empty($email))
        {
            $message['email'][] = $errEmpty;
        }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
             $message['email'][] = $errMailSyn;
        }

       // if(strlen($pseudo) < 3){
            //Votre pseudo doit faire plus de 3 caract.
       // }
       // if(!verif_pseudo($pseudo)){
            //Votre pseudo doit contenir (1-# , 3-MAJ, 2-min, 5-nombre)
       //}


        if(empty($email2) || !filter_var($email2, FILTER_VALIDATE_EMAIL))
        {
            $message['email'][] = $errEmpty;
        }
        if(empty($pass1)) 
        {
            $message['pass'][] = $errEmpty;
        }
        if(empty($pass2))
        {
            $message['pass'][] = $errEmpty;
        }

        //Vérification de sécurité
        if($email != $email2)
        {
            $message['email'][] = "<p>Les champs Email sont différents !</p>";
        }
        if($pass1 != $pass2)
        {
            $message['pass'][] = "<p>Les champs Mot de Passe sont différents !</p>";
        }

        //Si tout est ok j'enregistre en base
    if(empty($message))
    {
        //Ajout en base
         
        
                $model->ajoutInscription($app, $_POST);
                $categos = $model -> selectCategorie($app);
                $salleModel = new sallesModels();
                $slider = $salleModel  -> getMeilleuresSalles($app);
                return $app['twig']->render('accueil.html.twig', array(
                    'ajout' => true,
                    'categos' => $categos,
                    'slider'=> $slider
                ));

    }else{
        //Affichage du formulaire avec mes erreurs
        return $app['twig']->render('inscription.html.twig', array('message' => $message));
        }
    }    
})                                                                                   
->bind('ajoute')
;


///////////////////////////////////////////////////////////////////////////////////////
$app->get('/connexion', function () use ($app) {
    $model = new Models(); 
    $categos = $model -> selectCategorie($app);                       
    return $app['twig']->render('connexion.html.twig', array(
        'user'=>"",
        'categos' => $categos            
    ));    
})                                                              
->bind('connect')                                                                                  
;

$app->post('/connexion', function () use ($app){
    $model = new Models();
    $categos = $model -> selectCategorie($app);
    
    //$champs = array("pseudo", "mot_de_passe");
    if (isset($_POST) && !empty($_POST)) 
    {
        $pseudo = htmlentities($_POST['pseudo']);
        $mot_de_passe = strip_tags($_POST['mot_de_passe']);
         //$mot_de_passe = password_hash(strip_tags($_POST['mot_de_passe']),PASSWORD_DEFAULT);
        $connect = $model -> repConnexion($app, $pseudo);

        if (!empty($connect)) 
        {
            $mdp_base = $connect['mot_de_passe'];
            
            if (password_verify($mot_de_passe, $mdp_base)) 
            {
               
                $app['session']-> set('user',array(   /// création variable de session
                     'id'=>$connect['id'],
                     'pseudo'=>$connect['pseudo'],
                     'email'=>$connect['email'],
                     'ville'=>$connect['ville'],
                     'code_postal'=>$connect['code_postal']
                     
                ));
                // echo "<p>Vous êtes connecter</p>";
                // echo "<p><a href='accueil.php'>Retour à l'accueil</a></p>";  
                //header("Refresh:3;URL=accueil.php");
                    $salleModel = new sallesModels();
                    $slider = $salleModel  -> getMeilleuresSalles($app);
                    return $app['twig']->render('accueil.html.twig',
                     array(
                        'connecter' => true,
                        'categos' => $categos,
                        'slider'=> $slider,
                        'user'=>$app['session']->get('user')));  
                         
                        //  |       |       |       |       |       |
                         //$app->redirect($app["url_generator"]->generate("homepage"))
            }
            else
            {
                echo "<p style='padding-top : 30px; padding-left: 20px; color:red;'>Le mot de passe est incorrect !</p>";
                return $app['twig']->render('connexion.html.twig', array(
                    'categos' => $categos,
                    'user'=>$app['session']->get('user')
                ));
            }
        }
        else
        {
            echo "<p style='padding-top : 150px; padding-left: 20px; color:red;'>Les champs sont vides !</p>";
            return $app['twig']->render('connexion.html.twig', array(
                'categos' => $categos,
                'user'=>$app['session']->get('user')
            ));
        }

    }
    else
    {
        echo "<p style='padding-top : 30px; padding-left: 20px; color:red;'>Le deuxième mot de passe est incorrect !</p>";
        return $app['twig']->render('connexion.html.twig', array(
                'categos' => $categos,
                'user'=>$app['session']->get('user')
        ));
    }  
})
->bind('repconnect')                                                                
; 

///////////////////////////////////////////////////////////////////////////////////////
$app->get('/oubli', function () use ($app) {                        
    return $app['twig']->render('oubli.html.twig', array('user'=>$app['session']->get('user')));

    })
->bind('oubli')                                                                
; 


$app->post('/oubli', function () use ($app) {
   
    $champs = array("email");
    if (verif_form($_POST, $champs)){
        
        $email = strip_tags($_POST['email']);
        $model = new Models();
        $result = $model -> recupUserParMail($app, $email);
        $id = $result['id'];
        $result2 = $model->recupTokenParMail($app, $id);

        if(!empty($result)){
            
            $id = $result['id'];
            $nom = $result['nom'];
            $prenom = $result['prenom'];
            
            $token = md5(uniqid(rand(),true));
            if (empty($result2)){
                $model = new Models();
                $result = $model -> creationToken($app, $_POST, $id, $token);
            
            }else{
                $model = new Models();
                $result = $model -> modifToken($app, $_POST, $id, $token);
            }
            
            // création de l'objet PHPMailer
            $mail = new PHPMailer();

            // activer le mode debug
            //$mail -> SMTPDebug=2;
            // activer le SMTP
            $mail -> isSMTP();

            // authentification
            // adresse du serveur
            $mail -> Host = 'tls://toto.o2switch.net:26';
            // on active l'authentification
            $mail -> SMTPAuth = true;
            // nom d'utilisateur + password
            $mail -> Username = 'projet@the-last-of-us-forum.fr';
            $mail -> Password = 'Azertodu89';

            // protocole de sécurité SSL / TLS
            // $mail -> SMTPSecure = 'tls';
            // // port
            // $mail -> Port = 465;

            // corps du mail
            // expéditeur
            $mail -> setFrom('projet@the-last-of-us-forum.fr');
            // destinataire
            $mail -> addAddress($email);
            // adresse de réponse OPTIONNEL
            // $mail -> addReplyTo('reponse@gmail.com');
            // copie carbone OPTIONNEL
            // $mail -> addCC('adresse de copie');
            // copie carbone invisible OPTIONNEL
            // $mail -> addBCC('adresse de copie cachée');

             // piece jointe OPTIONNEL
             // $mail -> addAttachment('chemin du fichier');

             // message
             // autoriser le HTML
             $mail -> isHTML(true);

             $mail -> Subject = 'Redefinir un mot de passe';
             $mail -> Body = "Bonjour ".$nom." ".$prenom.",
             <p>Pour réinitialiser votre mot de passe du compte My Escape Game, cliquez sur le lien qui suit.</p>
             <p> Veuillez remarquer que ce lien expirera sous 48 heures. Si vous n'avez pas demandé cette réinitialisation, vous pouvez ignorer cet e-mail sans craindre pour votre compte.</p>
             <p><a href='localhost/myescapegameFINAL/web/redefinir_mdp/".$id."/".$token."'>Cliquez sur le lien</a></p>
             <p>Passez une bonne journée,</p><p>My Escape Game.</p>";
             // Clients mail qui n'utilisent pas HTML
             // $mail -> AltBody = 'Message alternatif du mail';

             // envoyer le mail
             $mail -> send();

            echo "Un mail contenant le lien pour redefinir votre mot de passe à était envoyé";
            return $app['twig']->render('oubli.html.twig', array('user'=>$app['session']->get('user')));
        }else{
            echo "L'adreese e-mail n'existe pas !";
            return $app['twig']->render('oubli.html.twig', array('user'=>$app['session']->get('user')));
        }
    
    }else{
         echo "Le formulaire n'est pas correctement rempli !";
            return $app['twig']->render('oubli.html.twig', array('user'=>$app['session']->get('user')));
    }
})           
->bind('envoi_token')
;


// création de la route pour le formulaire de redéfinition du mdp
$app->get('/redefinir_mdp/{id}/{token}', function ($id, $token) use ($app){
    $model = new Models();
    $result = $model -> recupTokenPourRedefinirMdp($app, $id, $token);
    if ($result) {
        if(!empty($result)){
            
            return $app['twig']->render('RedefinirOubliMdp.html.twig', 
                array('id' => $id,
                      'token' => $token));
        
        }else{
            
            echo "L'ID et/ou le token ne correspondent pas !";
            return $app['twig']->render('RedefinirOubliMdp.html.twig', 
                array('id' => $id,
                      'token' => $token));
        }
   
    }else{
       
        echo  "Il n'y a pas de token et/ou d'ID dans l'adresse !";
            return $app['twig']->render('RedefinirOubliMdp.html.twig', 
            array('id' => $id,
                  'token' => $token));    
    }
})
->bind('redefinir_mdp')
;

// post d'envoi des données pour modification du mdp chiffré dans la bdd
$app->post('/redefinir_mdp/{id}/{token}', function ($id, $token) use ($app){
    
     $champs = array("mdp1", "mdp2");

     if(verif_form($_POST, $champs)){
           
             $mdp1 = $_POST['mdp1'];
             $mdp2 = $_POST['mdp2'];
             $message2 ="";
             // verification de l'email
             $model = new Models();
             $result = $model -> recupUserParId($app, $id);
             
            if($result){
                // verification mdp 
                //if(preg_match($mdp1)) {                   
                    // vérification des deux mots de passe
                    if($mdp1 == $mdp2){

                        $mdpChiffre = password_hash($mdp1, PASSWORD_DEFAULT);
                        $model = new Models();                     
                        $result = $model -> modifMdp($app, $id, $mdpChiffre);
                        $model = new Models();                     
                        $result = $model -> supprimeToken($app, $id);

                        $message2 = "Votre mot de passe a été changé !";
                        $categos = $model -> selectCategorie($app);
                        return $app['twig']->render('accueil.html.twig', 
                            array("categos" => $categos,
                                  "user" => "",
                                  'user'=>$app['session']->get('user'),
                                  'message2' => $message2,
                                  'id' => $id,
                                  'token' => $token));
                        
                    }else{

                        $message2 = "Les deux mots de passe sont différents !";
                        return $app['twig']->render('RedefinirOubliMdp.html.twig', 
                            array(
                        'message2' => $message2,
                        'id' => $id,
                        'token' => $token));
                    }
                
                /*}else{
                    
                    $message2 = "Le mot de passe doit comporter entre 8 et 15 caractères et doit contenir au moins une lettre et un chiffre";
                    return $app['twig']->render('RedefinirOubliMdp.html.twig', 
                            array('message' => $message,
                        'message2' => $message2,
                        'id' => $id,
                        'token' => $token));
                }*/
            
            }else{
            
            $message2 = "Il y'a un problème avec le formulaire !";
                        return $app['twig']->render('RedefinirOubliMdp.html.twig', 
                                array(
                        'message2' => $message2,
                        'id' => $id,
                        'token' => $token));
            }
        
        }else{
            
            $message2 = "Tous les champs de saisie n'ont pas été rempli !";
                        return $app['twig']->render('RedefinirOubliMdp.html.twig', 
                                array(
                        'message2' => $message2,
                        'id' => $id,
                        'token' => $token));
    }    
})
->bind('redefinir_mdp_envoye')
;


/*$app->get('/redef', function () use ($app) {                        
    return $app['twig']->render('redef.html.twig', array('user'=>$app['session']->get('user')));    
})                                                              
->bind('redef_mdp')                                                                                  
;*/






///////////////////////////////////////////////////////////////////////////////////////
$app->get('/deconnexion', function () use ($app) { 
    $app['session']->remove('user');              
     return $app->redirect($app["url_generator"]->generate("homepage"));

})
->bind('deco')
;
///////////////////////////////////////////////////////////////////////////////////////
$app->get('/profil', function () use ($app) {             
    return $app['twig']->render('profil.html.twig', array('user'=>$app['session']->get('user')));    
})                                                              
->bind('profil_util')                                                                                  
;


//////////////////////////////////////////////////////////////////////////////////////////////
$app->get('/modif_profil', function () use ($app) {             
    return $app['twig']->render('modifprofil.html.twig', array('user'=>$app['session']->get('user')));
})                                                              
->bind('modif_profil')                                                                                  
;

$app->post('/modif_profil', function () use ($app) {
    $user = $app['session']->get('user');
    $id = $user['id'];
//Je vérifie que l'id exist en base si oui j'en profite pour le récupérer (model)*
    //Je verifie que le pseudo en session et égal au pseudo en base
    //Je modifie en base
        //Je modifie en session
            //Recréer la session user en re récupérant ce que je viens de mettre en base
    $model = new Models();
    $result = $model -> recupId($app , $id);
    // var_dump($result);

    if($result["id"] == $id){
        
        if($user['pseudo'] ==$result["pseudo"]){
            $model = new Models();
            $connect = $model -> modifProfil($app ,$_POST, $id);
            
        }
    }
    $app['session']-> set('user',array(   /// création variable de session
                     'id'=>$connect['id'],
                     'pseudo'=>$connect['pseudo'],
                     'email'=>$connect['email'],
                     'ville'=>$connect['ville'],
                     'code_postal'=>$connect['code_postal']
                      ));
//var_dump($user);
    echo "<p style='color:red;'>Votre profil a été modifier !</p>";
    return $app['twig']->render('modifprofil.html.twig', array('user'=>$app['session']->get('user')));
})                                                              
->bind('profil_modifie')                                                                                  
;



$app->get('/redef_mot_de_passe', function () use ($app) {             
    return $app['twig']->render('redefMdp.html.twig', array('user'=>$app['session']->get('user')));
})                                                              
->bind('redef_mdp')                                                                                  
;

$app->post('/redef_mot_de_passe', function () use ($app){             
    
    $champs = array("mdp1", "mdp2", "mdp3");
    
    
    if(verif_form($_POST, $champs)){
        
        $id = $app['session']->get('user')['id'];   
        $mdp1 = strip_tags($_POST['mdp1']);
        $mdp2 = strip_tags($_POST['mdp2']);
        $mdp3 = strip_tags($_POST['mdp3']);
             
        $model = new Models();
        $result = $model -> verifMdp($app, $id);
        
        
        if( password_verify($mdp1,$result['mot_de_passe']) ){
            
            
                // verification mdp 
                //if(preg_match($mdp1)) {                   
                    // vérification des deux mots de passe

            if ($mdp2 == $mdp3){
                $model = new Models();
                $result = $model -> modifProfilMdp($app, $_POST, $id);
                $redef = password_hash(strip_tags($_POST['mdp2']),PASSWORD_DEFAULT);

                 $categos = $model -> selectCategorie($app);
                
                return $app['twig']->render('accueil.html.twig', array("categos" => $categos));
                

            }else{
                return "Les champs nouveau mot de passe ne sont pas identique !";
            }
            
        }else{
            return  'Le champs "ancien mot de passe" est incorecte !';
        }
        
    }else{
        return  "L'ID et/ou le token ne correspondent pas !";
    }
})                                                              
->bind('mdp_redef')                                                                                  
;




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
