<?php

namespace app\controllers;
session_start();
use app\models\Departement;
use app\models\User;
use Flight;

class UserController{

	public function __construct() {

	}

	public function loginPage() {

		 Flight::render('PageLogin',[]);
     
    }

    public function insertPage() {
        $dept=new Departement(Flight::db());
		$d = $dept->getAllDepartement();

        Flight::render('Backoffice/PageInsertUser',['dept'=>$d]);

    
   }


   public function userPage() {
    $user=new User(Flight::db());
    $users = $user->findAllUser();

    Flight::render('Backoffice/user',['User'=>$users]);


}


	public function login() {
        if(isset($_POST['Nom']) && isset($_POST['MotDepasse'])  ){

			$user=new User(Flight::db());

            $reponse=$user->findUser($_POST['Nom'],$_POST['MotDepasse']);

            if($reponse!=null){

               $_SESSION['idUser']=$reponse['idUser'];
                 $_SESSION['idDepartement']=$reponse['IdDepartement'];
                $_SESSION['Position']=$reponse['Position'];


                header('Location: acceuil');
                exit;
            




            }else{
                Flight::render('PageLogin',['message'=>"Reverifiez votre authentification"]); 
            }
		
	
            

       
        
    }

}

public function insertUser() {
    if(isset($_POST['Nom']) && isset($_POST['MotDepasse']) && isset($_POST['Genre']) &&
   isset($_POST['idDepartement'])  ){

        $user=new User(Flight::db());

    $reponse=$user->InsertUser($_POST['Nom'],$_POST['MotDepasse'],$_POST['Genre'] ,
        $_POST['idDepartement']);

        if($reponse!=0){

     

            header('location: listUser');
            exit;




        }else{
            header('location: addUser');
        exit;
        }
    

        

   
    
}

}

        


	
}