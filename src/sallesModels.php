<?php


use Doctrine\DBAL\Connection;


class sallesModels {

	//VERIF SAISIE FORMULAIRE

	public function verifVote($app, $saisie)
	{

		$util = $app['session']->get('user');
		$id_salle = strip_tags($saisie['id_salle']);
			
		$sql = "SELECT id_util, id_salle FROM commentaires WHERE id_util = :id_util AND id_salle = :id_salle";
		$query = $app['db']-> prepare($sql);
		$query -> bindValue(':id_util', $util['id'], PDO::PARAM_INT);
		$query -> bindValue(':id_salle', $id_salle, PDO::PARAM_INT);
		$query -> execute();

		return $query -> fetch();

	}

	// SAISIE COMMENTAIRES

	public function ajoutCommentaires($app,$saisie)
	{

		$commentaires = strip_tags($saisie['commentaires']);
		$notes = strip_tags($saisie['notes']);
		$util = $app['session']->get('user');
		$id_salle = strip_tags($saisie['id_salle']);

		$sql = "INSERT INTO  commentaires (commentaires, date_commentaires, notes, id_util, id_salle) VALUES (:commentaires, :date_commentaires, :notes, :id_util, :id_salle)";
		$query = $app['db']-> prepare($sql);
		$query -> bindValue(':commentaires',$commentaires, PDO::PARAM_STR);
		$query -> bindValue(':date_commentaires',date('Y-m-d H:i:s'), PDO::PARAM_STR);
		$query -> bindValue(':notes',$notes, PDO::PARAM_INT);
		$query -> bindValue(':id_util',$util['id'], PDO::PARAM_INT);
		$query -> bindValue(':id_salle',$id_salle, PDO::PARAM_INT);
		$query -> execute();

		return true;

	}

	//AFFICHAGE INFO SALLES

	public function getInfosSalles($app,$id)
	{
		$sql = "SELECT * FROM salles WHERE id = :id";
		$query = $app['db']-> prepare($sql);
		$query -> bindValue(':id', $id, PDO::PARAM_INT);
		$query -> execute();
		return $query->fetch(); 
	}
	//AFFICHAGE INFO ENTREPRISES

	public function getInfosEntreprise($app,$idSalles)
	{
		$sql = "SELECT entreprise.* FROM entreprise
		INNER JOIN salles ON  entreprise.id = salles.id_entreprise
		WHERE salles.id = :id";
		$query = $app['db']-> prepare($sql);
		$query -> bindValue(':id', $idSalles, PDO::PARAM_INT);
		$query -> execute();
		return $query->fetch(); 
	}


	//AFFICHAGE COMMENTAIRES

	public function getCommentaires($app,$id)
	{

		$sql = "SELECT commentaires, date_commentaires, pseudo FROM  commentaires 
				INNER JOIN utilisateur ON commentaires.id_util = utilisateur.id
				WHERE commentaires.id_salle = :id_salle ORDER BY date_commentaires DESC LIMIT 3";
		$query = $app['db']->prepare($sql);
		$query -> bindValue(':id_salle',$id, PDO::PARAM_INT); 
		$query-> execute();
		return $query -> fetchAll();

	}

	//AFFICHAGE NOTE MOYENNE
	public function getNotes($app,$id)
	{

		$sql = "SELECT AVG(notes) as moyenne FROM commentaires WHERE commentaires.id_salle = :id_salle";
		$query = $app['db']->prepare($sql);
		$query -> bindValue(':id_salle',$id, PDO::PARAM_INT);
		$query-> execute();

		$result = $query -> fetch();

		$moy = round($result['moyenne']);

		return $moy;
	}

	//AFFICHAGE IMAGES

	public function getImagesSalles($app,$id)
	{

		$sql = "SELECT image_salle FROM  salles 
				WHERE id = :id";
		$query = $app['db']->prepare($sql);
		$query -> bindValue(':id',$id, PDO::PARAM_INT); 
		$query-> execute();
		return $query -> fetch();

	}

	//AFFICHAGE MEILLEURES MOYENNES SALLES
	public function getMeilleuresSalles($app)
	{
		$sql = "SELECT DISTINCT id_entreprise, image_salle, nom FROM salles INNER JOIN commentaires WHERE salles.id = commentaires.id_salle ORDER BY notes DESC LIMIT 5";
		return $app['db'] -> fetchAll($sql);
	}
	

}