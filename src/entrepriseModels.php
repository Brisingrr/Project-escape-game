<?php


use Doctrine\DBAL\Connection;


class entrepriseModels {



	//AFFICHAGE  ENTREPRISES


	public function afficheEntreprise($app,$id){
		$sql = "SELECT * FROM entreprise WHERE id = :id";
		$query = $app['db']-> prepare($sql);
		$query -> bindValue(':id', $id, PDO::PARAM_INT);
		$query -> execute();
		return $query->fetch(); 
	}

	public function getSalles($app,$idEnt){
		$sql = "SELECT salles.*, entreprise.id AS proprietaire FROM salles LEFT JOIN entreprise ON salles.id_entreprise = entreprise.id WHERE entreprise.id = :idEnt";
		$query = $app['db']-> prepare($sql);
		$query -> bindValue(':idEnt', $idEnt, PDO::PARAM_INT);
		$query -> execute();
		return $query->fetchAll();   
	}

	public function getImagesEntreprise($app,$id){

		$sql = "SELECT image FROM  entreprise 
				WHERE id = :id";
		$query = $app['db']->prepare($sql);
		$query -> bindValue(':id',$id, PDO::PARAM_INT); 
		$query-> execute();
		return $query -> fetch();
	}	

	//AFFICHAGE IMAGES DANS PAGE ENTREPRISE

	public function getImagesSalles($app,$id){

		$sql = "SELECT image_salle FROM  salles 
				WHERE id = :id";
		$query = $app['db']->prepare($sql);
		$query -> bindValue(':id',$id, PDO::PARAM_INT); 
		$query-> execute();
		return $query -> fetchAll();
	}
}