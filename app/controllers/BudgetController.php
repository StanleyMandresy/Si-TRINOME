<?php

namespace app\controllers;

use app\models\Budget;
use app\models\Departement;
use app\models\Categorie;
use app\models\CRMmodel;

use Flight;

class BudgetController{

	public function __construct() {

	}
    public function accueil() {

		$budgets=new Budget(Flight::db());
		$budgets = $budgets->getAllBudgetsByDept($_SESSION['idDepartement']);


        Flight::render('budget',['budgets'=>$budgets]);
    
    }

    public function addpage() {
        $departementModel = new Departement(Flight::db());
        $categorieModel = new Categorie(Flight::db());
        
    
        // Récupérer tous les départements
        $departements = $departementModel->getAllDepartement();
        $categories = $categorieModel->getAllCategorieByDept($_SESSION['idDepartement']);
    
        // Vérification si les listes sont vides
        $departements = !empty($departements) ? $departements : [];
        $categories = !empty($categories) ? $categories : [];
    
        // Envoyer toutes les données à la vue
        Flight::render('addbudget', [
            'data' => [
           
                'categories' => $categories
            ]
        ]);


    }
    public function addpage2() {
        $departementModel = new Departement(Flight::db());
        $categorieModel = new Categorie(Flight::db());

    
        // Récupérer tous les départements
        $departements = $departementModel->getAllDepartement();
        $categories = $categorieModel->getAllCategorieByDept($_SESSION['idDepartement']);
    
        // Vérification si les listes sont vides
        $departements = !empty($departements) ? $departements : [];
        $categories = !empty($categories) ? $categories : [];
    
        // Envoyer toutes les données à la vue
        Flight::render('addmodifier', [
            'data' => [
          
                'categories' => $categories
            ]
        ]);


    }
    public function add() {
        $budgetModel = new Budget(Flight::db());
        
        // Récupération des données du formulaire
        $idDepartement = $_SESSION['idDepartement'];
        $idCategorie = $_POST['idCategorie'];
        $idPeriode = $_POST['idPeriode']; // Notez que c'est 'idPeriode' et non 'idPeriode'
        $prevision = $_POST['prevision'];
        $realisation = $_POST['realisation'] ?? null; // Champ optionnel
        $dateBudget = $_POST['dateBudget']; // Notez que c'est 'dateBudget' et non 'datebudget'
    
        $budgetModel->AjoutBudget(
            $idDepartement,
            $idCategorie,
            $idPeriode,
            $prevision,
            $realisation,
            $dateBudget
        );
        
        Flight::redirect('budgetList');
    }
    
  
    public function deleteBudget(){
        $db = Flight::db();
        $budgetModel = new Budget($db);
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $id = $_GET['idBudget'];
            print($id);
            $success = $budgetModel->removeBudget($id);
            if ($success) {
                Flight::redirect('budgetList');            
                }
            }
    }

    public function update(){
        $db = Flight::db();
        $budgetModel = new Budget($db);
        $id = $_POST['save'];
        // Récupération des données du formulaire
        $idDepartement = $_SESSION['idDepartement'];
        $idCategorie = $_POST['idCategorie'];
        $idPeriode = $_POST['idPeriode']; // Notez que c'est 'idPeriode' et non 'idPeriode'
        $prevision = $_POST['prevision'];
        $realisation = $_POST['realisation'] ?? null; // Champ optionnel
        $dateBudget = $_POST['dateBudget']; 

     echo   $budgetModel->update(
            $id,
            $idDepartement,
            $idCategorie,
            $idPeriode,
            $prevision,
            $realisation,
            $dateBudget
        );
        
        Flight::redirect('budgetList');

    }





    public function pagevalidation(){
        $db = Flight::db();
        $budgetModel = new Budget($db);
        $dept=new Departement(($db));

        
        $crmModel = new CRMModel($db); // On passe la connexion DB au modèle
        
        // Récupérer toutes les périodes
        $periodes = $crmModel->getAllPeriodes();


        if ($dept->isFinance($_SESSION['idDepartement'])==false) {
        
                echo "Seul les membres du finance on accees";            
            }else{
                $budgets = $budgetModel->getAllBudgets();


                Flight::render('Validation',['budgets'=>$budgets,'periodes'=>$periodes]);  
            }

            }
            public function validation(){
                $db = Flight::db();
                $budgetModel = new Budget($db);
                $dept=new Departement(($db));
        
               
                $crmModel = new CRMModel($db);

                if (isset($_POST['valide']) && $dept->isFinance($_SESSION['idDepartement'])==false) {
                
                        echo "Seul les membres du finance on accees";            
                    }else{
                        $budgets = $budgetModel->valider($_POST['valide']);
        
                         header('Location: validation');
                         exit;
                       
                    }
                    if(isset(($_POST['crmValide']))){
               $crmModel->validerCRMPeriod($_POST['crmValide']);

                    }
        
                    }
                public function csv(){
                        $db = Flight::db();
                        $budgetModel = new Budget($db);
                       
                
                
                        if (isset($_POST['csv'])) {
                            if($_POST['csv']=="import"){
                                $budgetModel->importCSV($_SESSION['idDepartement']);

                                header('Location: budgetList');
                                exit;
                            }
                            if($_POST['csv']=="export"){

                                echo $budgetModel->exportCSV($_SESSION['idDepartement']);      
                                
                            }
                        
                         
                           
                               
                            }
                
                            }
            
        



    public function welcome(){

        Flight::render('PageAcceuil',[]);



    }

    public function PageBudget() {
        // Récupérer l'ID du département à partir de la session ou d'autres sources
        $idDepartement = $_SESSION['idDepartement'] ?? null;

        // Si les paramètres de début et de fin de période sont envoyés via POST
        $moisDebut = isset($_POST['debut']) ? $_POST['debut'] : 1;
        $moisFin = isset($_POST['fin']) ? $_POST['fin'] : 1;

      
        $budget = new Budget(Flight::db());
        $dept=new Departement(Flight::db());



        
        $tableau = $budget->generateBudgetTable($_SESSION['idDepartement'],$moisDebut , $moisFin);


   if($dept->isFinance($_SESSION['idDepartement'])==false){

    Flight::render('PageBudget', ['tableau' => $tableau,'debut'=>$moisDebut,'fin'=>$moisFin]);


   // Flight::render('PageBudget', ['tableau' => $tableau,'tableauTotal'=>$tableauTotal,'debut'=>$moisDebut,'fin'=>$moisFin]);
   }else{
    $tableauTotal=$budget->generateBudgetTableTous($moisDebut,$moisFin);
     Flight::render('PageBudget', ['tableau' => $tableau,'tableauTotal'=>$tableauTotal,'debut'=>$moisDebut,'fin'=>$moisFin]);

   }
        



        
    }
    public function exportPDF()
		{
			// 1. Récupérer les paramètres
			$idDepartement = $_SESSION['idDepartement'];
			$startPeriod = $_GET['debut'] ?? 1;
			$endPeriod = $_GET['fin'] ?? 1;
			$isTotal = isset($_GET['total']);
		
			// 2. Charger le modèle
			$budget=new Budget(Flight::db());
			
			// 3. Générer les données
			if ($isTotal) {
				$data = $budget->generateTotalBudgetTable($startPeriod, $endPeriod);
				$title = "Tableau Budgétaire Consolidé";
			} else {
				$data = $budget->generateBudgetTable($idDepartement, $startPeriod, $endPeriod);
				$title = "Tableau Budgétaire - Département " . $idDepartement;
			}
		
			// 4. Extraire les périodes
			$periods = array_column($data, 'periode');
		
			// 5. Générer le PDF
            dump(__DIR__.'/../models/Budgetpdf.php') ;
			require_once(__DIR__.'/../models/Budgetpdf.php');
			$pdf = new Budgetpdf($title, $periods, $data, $isTotal);
			$pdf->AliasNbPages();
			$pdf->GenerateTable();
		
			// 6. Envoyer le PDF
			$pdf->Output('D', 'budget_'.date('Y-m-d').'.pdf');
			exit; // Important pour arrêter l'exécution après l'envoi du PDF
		}	
		

   
}




