<?php

use Doctrine\DBAL\Connection;

class Models {


	public function selectCategorie($app){
	$sql=" SELECT * FROM categorie";
	return $app['db'] -> fetchAll($sql);
	}

	public function recherchePredictive($app, $recherche){
		$query = $app['db'] -> prepare("SELECT * FROM entreprise  WHERE ville LIKE :saisie");
		$query->bindValue(':saisie',$recherche.'%', PDO::PARAM_STR);
		$query->execute();
		$result = $query->fetchAll();
		return $result; // enfin on retourne les messages à notre script JS
	}



public function checkEmail($app, $email){
		$query = $app['db'] -> prepare('SELECT id FROM utilisateur WHERE email = :email');
		$query -> bindValue(':email', $email, PDO::PARAM_STR);
		$query -> execute();

		return $query->fetch();
	}
	

	public function ajoutInscription($app, $saisie){

		$nom = strip_tags($saisie['nom']);
		$prenom = strip_tags($saisie['prenom']);
		$pseudo = strip_tags($saisie['pseudo']);
		$email = strip_tags($saisie['email']);
		$ville = strip_tags($saisie['ville']);
		$code_postal = strip_tags($saisie['code_postal']);
		$pass1 = password_hash(strip_tags($saisie['pass1']),PASSWORD_DEFAULT);

		$sql = "INSERT INTO utilisateur (`nom`,`prenom`,`pseudo`,`email`,`ville`,`code_postal`,`mot_de_passe` ) VALUES (:nom, :prenom, :pseudo, :email, :ville, :code_postal, :mot_de_passe)";
		$query = $app['db'] -> prepare($sql);
        $query -> bindValue(':nom', $nom, PDO::PARAM_STR);
        $query -> bindValue(':prenom', $prenom, PDO::PARAM_STR);
        $query -> bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
        $query -> bindValue(':email', $email, PDO::PARAM_STR);
        $query -> bindValue(':ville', $ville, PDO::PARAM_STR);
        $query -> bindValue(':code_postal', $code_postal, PDO::PARAM_STR);
        $query -> bindValue(':mot_de_passe', $pass1, PDO::PARAM_STR);
        $query -> execute();


        return true;
	}




	public function repConnexion($app, $pseudo){
		$query =  $app['db'] -> prepare("SELECT * FROM utilisateur WHERE pseudo = :pseudo");
		$query -> bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
		$query -> execute();

		$result = $query -> fetch();

		return $result;
	}

	public function repContact ($app, $saisie){

		$nom = htmlentities($saisie['nom']);
		$email = htmlentities($saisie['email']);
		$message = htmlentities($saisie['message']);

		$query = $app['db'] -> prepare("INSERT INTO contact (nom, email, message) VALUES(:nom, :email, :message)");
		$query ->bindValue(':nom', $nom, PDO::PARAM_STR);
		$query ->bindValue(':email', $email, PDO::PARAM_STR);
		$query ->bindValue(':message', $message, PDO::PARAM_STR);
		$query -> execute();

		return true;

	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	//recherche dans utilisateurs par l'email
	public function recupUserParMail($app, $email){
		$query = $app['db'] -> prepare("SELECT * FROM utilisateur WHERE email = :email");
		$query -> bindValue(':email', $email, PDO::PARAM_STR);
		$query -> execute();

		$result = $query -> fetch();
		
		return $result;
	}

	//récupération du token par l'id
	public function recupTokenParMail($app, $id){

		$query = $app['db'] -> prepare("SELECT * FROM token WHERE id = :id");
		$query -> bindValue(':id', $id, PDO::PARAM_STR);
		$query -> execute();

		$result2 = $query -> fetch();
		
		return $result2;

	}
	//modification du token si token existant pour ce même utilisateur
	public function modifToken($app, $post, $id, $token){
		$query = $app['db'] -> prepare("UPDATE token SET token = :token WHERE id=:id");
		$query -> bindValue(':id', $id, PDO::PARAM_INT);
		$query -> bindValue(':token', $token, PDO::PARAM_STR);
		$query -> execute();
		return true;
	}

	//création du token
	public function creationToken($app, $post, $id, $token){
		$query = $app['db'] -> prepare("INSERT INTO token VALUES (:id, :token)");
		$query -> bindValue(':id', $id, PDO::PARAM_INT);
		$query -> bindValue(':token', $token, PDO::PARAM_STR);
		$query -> execute();
		return true;
	}

	//récupération du token par l'id et le numéro de token pour vérification avec le token indiqué dans l'url (obtenu par le lien envoyé par mail)
	public function recupTokenPourRedefinirMdp ($app, $id, $token){

		$query = $app['db'] -> prepare("SELECT * FROM token WHERE id = :id AND token = :token");
		$query -> bindValue(':id', $id, PDO::PARAM_STR);
		$query -> bindValue(':token', $token, PDO::PARAM_STR);
		$query -> execute();

		$result2 = $query -> fetch();
		
		return $result2;

	}

	//recherche dans utilisateurs par l'id
	public function recupUserParId($app, $id){
		$query = $app['db'] -> prepare("SELECT * FROM utilisateur WHERE id = :id");
		$query -> bindValue(':id', $id, PDO::PARAM_INT);
		$query -> execute();

		$result = $query -> fetch();
		
		return $result;

	}

	// modification du mot de passe pour un utilisateur
	public function modifMdp($app, $id, $mdpChiffre){
	
		$query = $app['db'] -> prepare("UPDATE utilisateur SET mot_de_passe = :mot_de_passe WHERE id=:id");

		$query -> bindValue(':id', $id, PDO::PARAM_INT);
		
		$query -> bindValue(':mot_de_passe', $mdpChiffre, PDO::PARAM_STR);

		$query -> execute();

		return true;
	}
		
	// suppression du token
	public function supprimeToken($app, $id){
		$query = $app['db'] -> prepare("DELETE FROM token WHERE id=:id");
		$query -> bindValue(':id', $id, PDO::PARAM_INT);
		$query -> execute();
		return true;
	}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function recupId($app, $id){
		$query =  $app['db'] -> prepare("SELECT id, pseudo FROM utilisateur WHERE id = :id");
		$query -> bindValue(':id', $id, PDO::PARAM_STR);
		$query -> execute();

		$result = $query -> fetch();

		return $result;
	}

	public function modifProfil ($app, $modif, $id){


		$pseudo = strip_tags($modif['pseudo']);
		$email = strip_tags($modif['email']);
		$ville = strip_tags($modif['ville']);
		$code_postal = strip_tags($modif['code_postal']);

		$sql = "UPDATE utilisateur SET `pseudo`=:pseudo,`email`=:email,`ville`=:ville,`code_postal`=:code_postal WHERE id = :id" ;
		$query = $app['db'] -> prepare($sql);
		$query -> bindValue(':id', $id, PDO::PARAM_INT);
        $query -> bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
        $query -> bindValue(':email', $email, PDO::PARAM_STR);
        $query -> bindValue(':ville', $ville, PDO::PARAM_STR);
        $query -> bindValue(':code_postal', $code_postal, PDO::PARAM_STR);
		$query -> execute();

		//Retourner la ligne modifié
		$sql = 'SELECT * FROM utilisateur WHERE id = :id';
		$query = $app['db'] -> prepare($sql);
		$query -> bindValue(':id', $id, PDO::PARAM_INT);
		$query->execute();
		$result = $query -> fetch();

		return $result;
	}

	public function verifMdp ($app, $id){

		$query = $app['db'] -> prepare("SELECT id, mot_de_passe FROM utilisateur  WHERE id = :id");
		$query -> bindValue(':id',$id, PDO::PARAM_STR);
        $query->execute();
		$result = $query -> fetch();

		return $result;
	}
	
	public function modifProfilMdp ($app, $redef, $id){
		

		$mot_de_passe = strip_tags($redef['mdp2']);

		$sql = "UPDATE utilisateur SET `mot_de_passe`= :mot_de_passe WHERE id = :id" ;
		$query = $app['db'] -> prepare($sql);
		$query -> bindValue(':id', $id, PDO::PARAM_STR);
		$query -> bindValue(':mot_de_passe',  password_hash(strip_tags($mot_de_passe),PASSWORD_DEFAULT), PDO::PARAM_STR);
		$query -> execute();	
	}
}