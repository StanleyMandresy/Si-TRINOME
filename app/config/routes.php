<?php


use app\controllers\WelcomeController;
use app\controllers\CategorieController;
use app\controllers\DepartementController;
use app\controllers\UserController;
use app\controllers\BudgetController;
use app\controllers\StatController;
use app\controllers\ClientController;
use app\controllers\RetourController;
use app\controllers\CRMcontroller;
use app\controllers\VenteController;
use flight\Engine;
use flight\net\Router;
//use Flight;

/** 
 * @var Router $router 
 * @var Engine $app
 */
/*$router->get('/', function() use ($app) {
	$Welcome_Controller = new WelcomeController($app);
	$app->render('welcome', [ 'message' => 'It works!!' ]);

});*/

$venteController = new VenteController();
$router->get('/vente', [$venteController, 'showForm']); // pour afficher le formulaire
$router->post('/vente', [$venteController, 'showForm']);   // pour traiter la soumission
$router->post('/ventes/import', [$venteController, 'handleImport']);   // pour traiter la soumission


$crmController = new CRMcontroller();
Flight::route('GET /retours-clients', [$crmController, 'AfficherRetoursClients']);
Flight::route('POST /associate-crm', [$crmController, 'associateCRM']);
Flight::route('GET /list_crm', [$crmController, 'showCRMretour']);
Flight::route('POST /valider-crm', [$crmController, 'validateCRM']);
Flight::route('POST /save-prevision', [$crmController, 'savePrevision']);



$RetourController = new RetourController();

$router->get('/retour', [$RetourController, 'formulaireRetour']); // pour afficher le formulaire
$router->post('/retour', [$RetourController, 'envoyerRetour']);   // pour traiter la soumission

$ClientController=new ClientController();
$router->post('/login', [$ClientController, 'accueil']);
$router->get('/home', [ $ClientController, 'home' ]);


$Welcome_Controller = new WelcomeController();
$router->get('/welcome', [ $Welcome_Controller, 'home' ]);
$router->get('/homedb', [ $Welcome_Controller, 'homedb' ]); 
$router->get('/testdb', [ $Welcome_Controller, 'testdb' ]); 
$router->get('/home-template', [ $Welcome_Controller, 'homeTemplate' ]); 
$router->get('/crud', [ $Welcome_Controller, 'crud' ]); 



$user_Controller=new UserController();
$router->get('/', [$user_Controller, 'loginPage']);
$router->get('/addUser', [$user_Controller, 'insertPage']);
$router->get('/listUser', [$user_Controller, 'userPage']);

$router->post('/', [$user_Controller, 'login']);
$router->post('/addUser', [$user_Controller, 'insertUser']);





$categorie_Controller = new CategorieController();

$router->get('/categorie', [$categorie_Controller, 'acceuil']);
$router->post('/categorie', [$categorie_Controller, 'save']);
$router->post('/addcategorie', [$categorie_Controller, 'addpage']);

$dept_Controller = new DepartementController();

$router->get('/dept', [$dept_Controller, 'acceuil']);
$router->post('/dept', [$dept_Controller, 'save']);
$router->post('/adddept', [$dept_Controller, 'addpage']);


$budget_controller=new BudgetController();
$router->get('/acceuil' ,[$budget_controller, 'welcome'] );
$router->get('/budget' ,[$budget_controller, 'PageBudget'] );
$router->post('/budget' ,[$budget_controller, 'PageBudget'] );



$router->get('/budgetList',[$budget_controller,'accueil']);
$router->get('/addbudget',[$budget_controller,'addpage']);
$router->post('/addbudget',[$budget_controller,'add']);

$router->get('/addmodifier',[$budget_controller,'addpage2']);
$router->post('/addmodifier',[$budget_controller,'update']);
$router->get('/deletebudget',[$budget_controller,'deleteBudget']);


$router->post('/validation',[$budget_controller,'validation']);
$router->get('/validation',[$budget_controller,'pagevalidation']);

$router->post('/csv',[$budget_controller,'csv']);

$router->get('/exportpdf',[$budget_controller,'exportPDF']);


$StatistiquesController = new StatController();

// Définition de la route pour la page de statistiques
$router->get('/statistiques', [ $StatistiquesController, 'home' ]);
$router->post('/statistiques', [ $StatistiquesController, 'home' ]);



//$router->get('/', \app\controllers\WelcomeController::class.'->home'); 

$router->get('/hello-world/@name', function($name) {
	echo '<h1>Hello world! Oh hey '.$name.'!</h1>';
});
