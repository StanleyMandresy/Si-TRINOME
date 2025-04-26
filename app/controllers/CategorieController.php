<?php

namespace app\controllers;

use app\models\Categorie;
use app\models\Departement;


use Flight;

class CategorieController{

	public function __construct() {

	}

	public function acceuil() {

		$categorie=new Categorie(Flight::db());
		$cat = $categorie->getAllCategorie();


        Flight::render('Backoffice/categorie',['categorie'=>$cat]);
     
    }

	public function addpage() {
        if(isset($_POST['button']) ){

			$categorie=new Categorie(Flight::db());
			$cat = $categorie->getAllnature();

			$dept=new Departement(Flight::db());
		$d = $dept->getAllDepartement();
	
            $button=$_POST['button'];

        Flight::render('Backoffice/addcategorie',['button'=>$button,
	'nature'=>$cat,'dept'=>$d]);
        
    }

}


        


	public function save() {
		if(isset($_POST['save']) && $_POST['save']=="ajouter"){
			if(isset($_POST['nom']) && isset($_POST['nature'])  && isset($_POST['idDepartement'])  ) {
				$categorie=new Categorie(Flight::db());
		 
				$retour=$categorie->addCategorie($_POST['nom'],$_POST['nature'],$_POST['idDepartement']);
		 
				if($retour==1){
				 header('Location: categorie');
				 exit;
				}else{
				 echo "erreur lors de l'ajout , reverifiez les données";
				}
				 
			 }
		}
		if(isset($_POST['save']) && $_POST['save']!="ajouter"){
			if(isset($_POST['nom']) && isset($_POST['nature']) && isset($_POST['idDepartement']) ) {
				$categorie=new Categorie(Flight::db());
		 
				$retour=$categorie->changeCategorie($_POST['save'],$_POST['nom'],$_POST['nature'],$_POST['idDepartement']);
		 
				if($retour==1){
				 header('Location: categorie');
				 exit;
				}else{
				 echo "erreur lors du modification , reverifiez les données";
				}
		}

	}
		if(isset($_POST["delete"])){
			$categorie=new Categorie(Flight::db());
		 
			$retour=$categorie->removeCategorie($_POST['delete']);   
		
		
			header('Location: categorie');
			exit;
		
		}
		
		
		
		
}

}

