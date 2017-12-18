<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>Page Entreprise</title>

		<!-- CSS -->
		
		<!--  -->
		<link rel="stylesheet" href="css/styles.css" type="text/css">

		<!-- JavaScript -->

		<script type="text/javascript" src="js/jquery-3.2.1.js"></script>
		<script type="text/javascript" src="js/bootstrap.js"></script>
		<script type="text/javascript" src="js/scripts.js"></script>
	</head>
	<body>

		<nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark pl10 pr10">
            <a class="navbar-brand" href="#">MyEscapeGame</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <ul class="navbar-nav ml-auto text-center">
                    <li class="nav-item active"><a class="nav-link before after" href="#">Accueil<span class="sr-only">(current)</span></a></li>
                    <li class="nav-item"><a class="nav-link before after" href="#">Contact</a></li>
                    <li class="nav-item"><a class="nav-link before after" href="#">Connexion</a></li>
                    <li class="nav-item"><a class="nav-link before after" href="#">Inscription</a></li>
                </ul>
            </div>
        </nav>

        <main class="container">
        	
        	<section class="row sEntreprise">

                <div class="col-md-2 text-center">
                    <!-- <p>Ici se trouve le LOGO de l'entreprise</p> -->
                    <img src="./imgs/chat_2.jpg" class="chat2" alt="Une photo de chat">
                </div><!-- .div -->
        		<div class="col-md-10 text-center">
                    <h2>Nom de l'entreprise</h2>
                    <p>Adresse de l'entreprise</p>
                </div><!-- .div -->
            </section>

            <section class="row sSalle">

                <div class="col-12 text-center">
                    <h2>Nom de la salle de l'Escape Game N°1</h2>                    
                </div><!-- .div -->
                <div class="col-md-6 divImg">
                        <img src="./imgs/chat_1.jpg" class="chat1" alt="Une photo de chat">
                </div><!-- .div imgs -->
                    <article class="col-md-6 divArticle">
                        <h3>Description courte de la salle N°1 :</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                        tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.</p>

                        
                    </article><!-- .artcicle -->
                <div class="col-12 divEnSavoirPlus text-center">
                    <a href="page_salle.php">En savoir plus...</a>
                </div>

        	</section><!-- .section1 row -->

        </main><!-- .main -->

	</body>
</html>
