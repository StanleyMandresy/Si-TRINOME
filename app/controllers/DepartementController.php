<?php

namespace app\controllers;

use app\models\Departement;
use app\models\User;
use Flight;

class DepartementController{

	public function __construct() {

	}

	public function acceuil() {

		$dept=new Departement(Flight::db());
		$d = $dept->getAllDepartement();

		
        Flight::render('Backoffice/dept',['dept'=>$d]);
     
    }

	public function addpage() {
        if(isset($_POST['button']) ){
			if($_POST['button']=="ajouter" ){
				$dept=new Departement(Flight::db());
		
			
				$button=$_POST['button'];
	
			Flight::render('Backoffice/adddept',['button'=>$button] );
				
			}else{
				$dept=new Departement(Flight::db());
		
				$user=new User(Flight::db());
				$users = $user->findAllUserByDepart($_POST['button']);
		
				$button=$_POST['button'];
	
			Flight::render('Backoffice/adddept',['button'=>$button, 'user'=>$users] );


			}
		
        
    }

}


        


	public function save() {
		if(isset($_POST['save']) && $_POST['save']=="ajouter"){
			if(  isset($_POST['nom']) ) {
				
				$dept=new Departement(Flight::db());
		 
				$retour=$dept->addDepartement($_POST['nom']);
                
		 
				if($retour==1){
				 header('Location: dept');
                
				 exit;
				}else{
				 echo "erreur lors de l'ajout , reverifiez les données";
				}
				 
			 }
		}
		if(isset($_POST['save']) && $_POST['save']!="ajouter"){
			if( isset($_POST['nom']) && isset($_POST['responsable'])) {
				$dept=new Departement(Flight::db());
		 
				$retour=$dept->updateDepartement($_POST['save'],$_POST['nom'],$_POST['responsable']);
               
		 
				if($retour==1){
				 header('Location: dept');
				 exit;
				}else{
				 echo "erreur lors du modification , reverifiez les données";
				}
		}

	}
		if(isset($_POST["delete"])){
			$dept=new Departement(Flight::db());
		 
			$retour=$dept->removeDepartement($_POST['delete']);   
		
		
			header('Location: dept');
			exit;
		
		}
		
		
		
		
		
}
}