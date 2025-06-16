<?php
namespace app\models;
use BudgetPDF;

use PDO;
use Exception;

class Budget {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }


    public function getAllBudgets() {
        $sql = "SELECT b.*, 
                       d.nomDepartement, 
                       c.nomCategorie, 
                       p.periode_id
                FROM budget b
                JOIN Departement d ON b.idDepartement = d.idDepartement
                JOIN Categorie c ON b.idCategorie = c.idCategorie
                JOIN periodes p ON b.periode_id = p.periode_id
                where b.isApproved=false
                 ORDER BY b.DateBudget DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllBudgetsByDept($id) {
        $sql = "SELECT b.*, 
                       d.nomDepartement, 
                       c.nomCategorie, 
                       p.periode_id
                FROM budget b
                JOIN Departement d ON b.idDepartement = d.idDepartement
                JOIN Categorie c ON b.idCategorie = c.idCategorie
                JOIN periodes p ON b.periode_id = p.periode_id
                where b.idDepartement='$id'
                AND b.isApproved=false
                ORDER BY b.DateBudget DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    
    public function AjoutBudget($idDepartement, $idCategorie, $idPeriode, $prevision, $realisation, $dateBudget) {
        $sql = "INSERT INTO budget (idDepartement, idCategorie, periode_id, prevision, realisation, DateBudget) 
                VALUES (:idDepartement, :idCategorie, :idPeriode, :prevision, :realisation, :dateBudget)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':idDepartement' => $idDepartement,
            ':idCategorie' => $idCategorie,
            ':idPeriode' => $idPeriode,
            ':prevision' => $prevision,
            ':realisation' => $realisation,
            ':dateBudget' => $dateBudget  // Corrigé pour correspondre au nom du champ du formulaire
        ]);
        
        return $stmt->rowCount();
    }
    // Récupérer un budget par ID
    public function getBudgetByID($id) {
        try {
            $sql = "SELECT * FROM budget WHERE idBudget = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
            return [];
        }
    }
    
    
    // Supprimer un budget
    public function removeBudget($id) {
        try {
            $sql = "DELETE FROM budget WHERE idBudget = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return "Budget supprimé avec succès.";
        } catch (Exception $e) {
            return "Erreur : " . $e->getMessage();
        }
    }

    public function valider($id) {
        try {
            $sql = "update budget set isApproved=true WHERE idBudget = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
         
          
            return 1;
        } catch (Exception $e) {
            return "Erreur : " . $e->getMessage();
        }
    }

    public function exportCSV($id) {
        // Crée une requête pour obtenir les données à exporter
        $budgets = $this->getAllBudgetsByDept($id);  // Appel à getAllBudgetsByDept
        
        // Définir le nom du fichier CSV
        $filename = "budget_export_" . date('Y-m-d') . ".csv";
        
        // Ouvrir un fichier pour l'écriture dans le flux de sortie PHP
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Ouvrir le flux de sortie PHP
        $output = fopen('php://output', 'w');
        
        // Ajouter les en-têtes de colonnes avec des noms compréhensibles
        fputcsv($output, array('idBudget', 'Departement', 'Categorie', 'Prevision', 'Realisation', 'Ecart', 'DateBudget', 'Periode', 'isApproved'));
        
        // Ajouter les données ligne par ligne
        foreach ($budgets as $row) {
            fputcsv($output, array(
                $row['idBudget'],            // idBudget
                $row['nomDepartement'],      // nomDepartement
                $row['nomCategorie'],        // nomCategorie
                $row['Prevision'],           // Prevision
                $row['Realisation'],         // Realisation
                $row['Ecart'],               // Ecart
                $row['DateBudget'],          // DateBudget
                $row['nom_periode'],         // nom_periode
                $row['isApproved']           // isApproved
            ));
        }
        
        // Fermer le fichier
        fclose($output);
    }
    

    public function importCSV($idDepartement) {
        if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
            $file = $_FILES['csv_file']['tmp_name'];
            $handle = fopen($file, 'r');
            
            // Ignorer la première ligne (en-têtes)
            fgetcsv($handle, 1000, ','); 
            
            // Lire le fichier ligne par ligne
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
    
                // Déboguer le contenu de la ligne lue
                var_dump($data); // Afficher le contenu de la ligne CSV
                
                // Vérifier si la ligne contient bien les 5 colonnes attendues (idCategorie, Prevision, Realisation, DateBudget, periode)
                if (count($data) < 5) {
                    // Si la ligne ne contient pas suffisamment de données, on l'ignore
                    continue;
                }
    
                // Vérification du format de la date
                $date = $data[3];  // Supposons que la date est à l'index 3 (DateBudget)
                $dateFormatValid = preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $date);
    
                if (!$dateFormatValid) {
                    // Si la date est invalide, tenter de la corriger (par exemple ajouter l'heure)
                    // On assume que la date est au format 'YYYY-MM-DD', on ajoute donc '00:00:00' pour l'heure
                    $date = $date . ' 00:00:00';
                }
    
                // Vérification du format après correction
                $date = date('Y-m-d H:i:s', strtotime($date));  // Convertir la date au bon format
    
                // Insérer les données dans la base avec isApproved = 0 par défaut
                $sql = "INSERT INTO budget (idDepartement, idCategorie, Prevision, Realisation, DateBudget, periode_id, isApproved) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
        
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $idDepartement,           // idDepartement (passé comme paramètre à la fonction)
                    $data[0],                 // idCategorie
                    $data[1],                 // Prevision
                    $data[2],                 // Realisation
                    $date,                    // DateBudget (formatée)
                    $data[4],                 // periode_id
                    0                         // isApproved (0 par défaut pour 'false')
                ]);
            }
            
            fclose($handle);
            
            // Message de succès
            echo "Importation réussie!";
        } else {
            echo "Erreur lors de l'importation du fichier.";
        }
    }
    
    // Somme des prévisions par département, nature et période
    public function sumPrevisionByDept($idDepartement, $idnature, $periode_id) {
        try {
            // Si idnature = 1, on calcule la somme des recettes
            if ($idnature == 1) {
                $sql = "SELECT SUM(prevision) 
                        FROM budget 
                        JOIN Categorie ON budget.idCategorie = Categorie.idCategorie 
                        WHERE idNature = 1 
                        AND budget.idDepartement= '$idDepartement' 
                        AND periode_id = '$periode_id' 
                        AND isApproved = TRUE"; // Assurer que la prévision est validée
            }
            // Si idnature = 2, on calcule la somme des dépenses
            else if ($idnature == 2) {
                $sql = "SELECT SUM(prevision) 
                        FROM budget 
                        JOIN Categorie ON budget.idCategorie = Categorie.idCategorie 
                        WHERE idNature = 2 
                        AND budget.idDepartement= '$idDepartement' 
                        AND periode_id = '$periode_id' 
                        AND isApproved = TRUE"; // Assurer que la prévision est validée
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            // Récupérer la somme des prévisions
            $sumPrevision = $stmt->fetchColumn();
            
            if ($sumPrevision !== false) {
                // Maintenant, mettez à jour la table SoldePrevision pour la période et le département spécifiés
                if ($idnature == 1) {
                    // Mettre à jour la recette dans SoldePrevision
                    $updateSql = "UPDATE SoldePrevision 
                                  SET Recette = :recette 
                                  WHERE periode_id = :periode_id 
                                  AND idDepartement= :idDepartement";
                    $stmt = $this->db->prepare($updateSql);
                    $stmt->execute([
                        ':recette' => $sumPrevision,
                        ':periode_id' => $periode_id,
                        ':idDepartement' => $idDepartement
                    ]);
                } else if ($idnature == 2) {
                    // Mettre à jour la dépense dans SoldePrevision
                    $updateSql = "UPDATE SoldePrevision 
                                  SET Depense = :depense 
                                  WHERE periode_id = :periode_id 
                                  AND idDepartement= :idDepartement";
                    $stmt = $this->db->prepare($updateSql);
                    $stmt->execute([
                        ':depense' => $sumPrevision,
                        ':periode_id' => $periode_id,
                        ':idDepartement' => $idDepartement
                    ]);
                }
                
                return $sumPrevision; // Retourner la somme des prévisions calculées
            }
            
            return 0; // Retourne 0 si aucune somme n'a été trouvée
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }
    public function sumRealisationByDept($idDepartement, $idnature, $periode_id) {
        try {
            // Si idnature = 1, on calcule la somme des recettes
            if ($idnature == 1) {
                $sql = "SELECT SUM(realisation) 
                        FROM budget 
                        JOIN Categorie ON budget.idCategorie = Categorie.idCategorie 
                        WHERE idNature = 1 
                        AND budget.idDepartement= '$idDepartement' 
                        AND periode_id = '$periode_id' 
                        AND isApproved = TRUE"; // Assurer que la prévision est validée
            }
            // Si idnature = 2, on calcule la somme des dépenses
            else if ($idnature == 2) {
                $sql = "SELECT SUM(realisation) 
                        FROM budget 
                        JOIN Categorie ON budget.idCategorie = Categorie.idCategorie 
                        WHERE idNature = 2 
                        AND budget.idDepartement= '$idDepartement' 
                        AND periode_id = '$periode_id' 
                        AND isApproved = TRUE"; // Assurer que la prévision est validée
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            // Récupérer la somme des prévisions
            $sumrealisation = $stmt->fetchColumn();
            
            if ($sumrealisation !== false) {
                // Maintenant, mettez à jour la table Solderealisation pour la période et le département spécifiés
                if ($idnature == 1) {
                    // Mettre à jour la recette dans Solderealisation
                    $updateSql = "UPDATE SoldeRealisation 
                                  SET Recette = :recette 
                                  WHERE periode_id = :periode_id 
                                  AND idDepartement= :idDepartement";
                    $stmt = $this->db->prepare($updateSql);
                    $stmt->execute([
                        ':recette' => $sumrealisation,
                        ':periode_id' => $periode_id,
                        ':idDepartement' => $idDepartement
                    ]);
                } else if ($idnature == 2) {
                    // Mettre à jour la dépense dans Solderealisation
                    $updateSql = "UPDATE SoldeRealisation 
                                  SET Depense = :depense 
                                  WHERE periode_id = :periode_id 
                                  AND idDepartement= :idDepartement";
                    $stmt = $this->db->prepare($updateSql);
                    $stmt->execute([
                        ':depense' => $sumrealisation,
                        ':periode_id' => $periode_id,
                        ':idDepartement' => $idDepartement
                    ]);
                }
                
                return $sumrealisation; // Retourner la somme des prévisions calculées
            }
            
            return 0; // Retourne 0 si aucune somme n'a été trouvée
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }
       
    public function solde_debutdePn($periode_id_avant) {
        try {
            // Calcul de la période suivante
            $periode_id_suivante = $periode_id_avant + 1;
    
            // Vérifier si une ligne existe déjà dans SoldePrevision pour la période suivante
            $sqlCheckSolde = "SELECT COUNT(*) FROM SoldePrevision WHERE periode_id = :periode_id_suivante";
            $stmt = $this->db->prepare($sqlCheckSolde);
            $stmt->execute([':periode_id_suivante' => $periode_id_suivante]);
            $existSolde = $stmt->fetchColumn();
    
            // Si la ligne n'existe pas, l'insérer
            if ($existSolde == 0) {
                $insertSoldeSql = "INSERT INTO SoldePrevision (periode_id, idDepartement, solde_debut) VALUES (:periode_id_suivante, :idDepartement, 0)";
                $stmt = $this->db->prepare($insertSoldeSql);
                $stmt->execute([
                    ':periode_id_suivante' => $periode_id_suivante,
                    ':idDepartement' => $_SESSION['idDepartement'],
                ]);
    
                // Insérer dans SoldeRealisation aussi
                $insertRealisationSql = "INSERT INTO SoldeRealisation (periode_id, idDepartement, solde_debut) VALUES (:periode_id_suivante, :idDepartement, 0)";
                $stmt = $this->db->prepare($insertRealisationSql);
                $stmt->execute([
                    ':periode_id_suivante' => $periode_id_suivante,
                    ':idDepartement' => $_SESSION['idDepartement'],
                ]);
            }
    
            // Récupérer le solde_fin des réalisations de la période précédente
            $sql = "SELECT solde_fin FROM SoldeRealisation WHERE periode_id = :periode_id_avant";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':periode_id_avant' => $periode_id_avant]);
    
            $solde_fin = $stmt->fetchColumn();
    
            if ($solde_fin !== false) {
                // Mettre à jour le solde_debut de la période suivante dans SoldePrevision
                $updateSoldePrevision = "UPDATE SoldePrevision SET solde_debut = :solde_debut WHERE periode_id = :periode_id_suivante AND idDepartement = :idDepartement";
                $stmt = $this->db->prepare($updateSoldePrevision);
                $stmt->execute([
                    ':solde_debut' => $solde_fin,
                    ':periode_id_suivante' => $periode_id_suivante,
                    ':idDepartement' => $_SESSION['idDepartement']
                ]);
    
                return $solde_fin;
            }
    
            return 0; // Si aucun solde_fin n'est trouvé pour la période précédente
    
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return null; // Retourne null en cas d'erreur
        }
    }
    
    public function sumSoldeDebut( $endPeriod) {
        try {
            // Récupérer tous les départements
            $sqlDepts = "SELECT idDepartement FROM Departement";
            $stmt = $this->db->prepare($sqlDepts);
            $stmt->execute();
            $departements = $stmt->fetchAll();
            
            $totalSoldeDebut = 0; // Variable pour accumuler la somme des soldes de début
    
            // Boucle pour chaque département
            foreach ($departements as $departement) {
                $idDepartement = $departement['idDepartement'];
    
                // Boucle pour chaque période entre startPeriod et endPeriod
                for ($periode_id = 1; $periode_id <= $endPeriod; $periode_id++) {
                    
                    // Vérifier si une ligne existe dans SoldePrevision pour cette période et ce département
                    $sqlCheckSolde = "SELECT solde_debut FROM SoldePrevision 
                                      WHERE periode_id = :periode_id 
                                      AND idDepartement = :idDepartement";
                    $stmt = $this->db->prepare($sqlCheckSolde);
                    $stmt->execute([':periode_id' => $periode_id, ':idDepartement' => $idDepartement]);
                    
                    // Récupérer le solde_debut pour la période et le département donné
                    $solde_debut = $stmt->fetchColumn();
                    
                    // Si un solde_debut existe pour cette période et ce département, l'ajouter au total
                    if ($solde_debut !== false) {
                        $totalSoldeDebut += $solde_debut;
                    }
                }
            }
    
            return $totalSoldeDebut; // Retourner la somme totale des soldes de début
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return 0; // Retourner 0 en cas d'erreur
        }
    }
    

    
    public function generateBudgetTable($idDepartement, $startPeriod, $endPeriod) {
        $budgetData = [];
        
        // Boucle pour chaque période entre startPeriod et endPeriod
        for ($periode_id = $startPeriod; $periode_id <= $endPeriod; $periode_id++) {
            
            // Vérifier si une ligne pour la période existe dans SoldePrevision et SoldeRealisation
            // Si ce n'est pas la première période, on récupère le solde de fin de la période précédente
            if ($periode_id > $startPeriod) {
                $solde_debut = $this->solde_debutdePn($periode_id - 1);  // solde_fin de la période précédente
            } else {
                $solde_debut = 0;  // Pour la première période, le soldeDébut est généralement 0
                $this->checkAndInsertSolde($idDepartement, $periode_id);  // Vérifier et insérer pour la première période
            }
            
            // Calculer la somme des prévisions de recettes et de dépenses
            $recettePrevision = $this->sumPrevisionByDept($idDepartement, 1, $periode_id);  // idnature 1 pour recette
            $depensePrevision = $this->sumPrevisionByDept($idDepartement, 2, $periode_id);  // idnature 2 pour depense
            
            // Calculer la somme des réalisations de recettes et de dépenses
            $recetteRealisation = $this->sumRealisationByDept($idDepartement, 1, $periode_id);  // idnature 1 pour recette
            $depenseRealisation = $this->sumRealisationByDept($idDepartement, 2, $periode_id);  // idnature 2 pour depense
            
            // Calcul du solde de fin pour la période actuelle
            $solde_finPrevision = ($solde_debut + $recettePrevision) - $depensePrevision;
            $solde_finRealisation= ($solde_debut + $recetteRealisation) - $depenseRealisation;
            
         
            $ecartRecette = $recetteRealisation - $recettePrevision;
            $ecartDepense = $depenseRealisation - $depensePrevision;
            
         
            $budgetData[] = [
                'periode' => $periode_id,
                'solde_debut' => $solde_debut,
                'recette_prevision' => $recettePrevision,
                'depense_prevision' => $depensePrevision,
                'solde_fin_realisation' => $solde_finRealisation,
                'solde_fin_prevision' => $solde_finPrevision,
                'recette_realisation' => $recetteRealisation,
                'depense_realisation' => $depenseRealisation,
                'ecart_recette' => $ecartRecette,
                'ecart_depense' => $ecartDepense,
            ];
            
            // Mettre à jour les tables SoldePrevision et SoldeRealisation avec le solde de fin calculé
            $this->updateSolde($idDepartement, $periode_id, $solde_finPrevision, $solde_finRealisation);

        }
        
        return $budgetData;
    }
    
    // Nouvelle méthode pour vérifier et insérer des lignes dans SoldePrevision et SoldeRealisation
    private function checkAndInsertSolde($idDepartement, $periode_id) {
        try {
            // Vérifier si les lignes pour cette période existent dans SoldePrevision et SoldeRealisation
            $sqlCheckSoldePrevision = "SELECT COUNT(*) FROM SoldePrevision WHERE periode_id = :periode_id AND idDepartement= :idDepartement";
            $stmt = $this->db->prepare($sqlCheckSoldePrevision);
            $stmt->execute([
                ':periode_id' => $periode_id,
                ':idDepartement' => $idDepartement
            ]);
            $existsPrevision = $stmt->fetchColumn();
    
            // Insérer si la ligne n'existe pas
            if ($existsPrevision == 0) {
                $insertSoldePrevision = "INSERT INTO SoldePrevision (periode_id, idDepartement, solde_debut) 
                                         VALUES (:periode_id, :idDepartement, 0)";
                $stmt = $this->db->prepare($insertSoldePrevision);
                $stmt->execute([
                    ':periode_id' => $periode_id,
                    ':idDepartement' => $idDepartement
                ]);
            }
    
            // Vérifier et insérer dans SoldeRealisation
            $sqlCheckSoldeRealisation = "SELECT COUNT(*) FROM SoldeRealisation WHERE periode_id = :periode_id AND idDepartement= :idDepartement";
            $stmt = $this->db->prepare($sqlCheckSoldeRealisation);
            $stmt->execute([
                ':periode_id' => $periode_id,
                ':idDepartement' => $idDepartement
            ]);
            $existsRealisation = $stmt->fetchColumn();
    
            if ($existsRealisation == 0) {
                $insertSoldeRealisation = "INSERT INTO SoldeRealisation (periode_id, idDepartement, solde_debut) 
                                           VALUES (:periode_id, :idDepartement, 0)";
                $stmt = $this->db->prepare($insertSoldeRealisation);
                $stmt->execute([
                    ':periode_id' => $periode_id,
                    ':idDepartement' => $idDepartement
                ]);
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    
    // Nouvelle méthode pour mettre à jour les soldes de fin dans SoldePrevision et SoldeRealisation
    private function updateSolde($idDepartement, $periode_id, $solde_finPrevision, $solde_finRealisation) {
        try {
            // Mettre à jour le solde de fin dans SoldePrevision
            $updateSoldePrevision = "UPDATE SoldePrevision SET solde_fin = :solde_fin 
                                     WHERE periode_id = :periode_id AND idDepartement= :idDepartement";
            $stmt = $this->db->prepare($updateSoldePrevision);
            $stmt->execute([
                ':solde_fin' => $solde_finPrevision,
                ':periode_id' => $periode_id,
                ':idDepartement' => $idDepartement
            ]);
    
            // Mettre à jour le solde de fin dans SoldeRealisation
            $updateSoldeRealisation = "UPDATE SoldeRealisation SET solde_fin = :solde_fin 
                                       WHERE periode_id = :periode_id AND idDepartement= :idDepartement";
            $stmt = $this->db->prepare($updateSoldeRealisation);
            $stmt->execute([
                ':solde_fin' => $solde_finRealisation,
                ':periode_id' => $periode_id,
                ':idDepartement' => $idDepartement
            ]);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function sumPrevision($idnature, $periode_id) {
        try {
            // Si idnature = 1, on calcule la somme des recettes pour tous les départements
            if ($idnature == 1) {
                $sql = "SELECT SUM(prevision) 
                        FROM budget 
                        JOIN Categorie ON budget.idCategorie = Categorie.idCategorie 
                        WHERE idNature = 1 
                        AND periode_id = '$periode_id' 
                        AND isApproved = TRUE"; // Assurer que la prévision est validée
            }
            // Si idnature = 2, on calcule la somme des dépenses pour tous les départements
            else if ($idnature == 2) {
                $sql = "SELECT SUM(prevision) 
                        FROM budget 
                        JOIN Categorie ON budget.idCategorie = Categorie.idCategorie 
                        WHERE idNature = 2 
                        AND periode_id = '$periode_id' 
                        AND isApproved = TRUE"; // Assurer que la prévision est validée
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
    
            // Récupérer la somme des prévisions
            $sumPrevision = $stmt->fetchColumn();
    
            return $sumPrevision !== false ? $sumPrevision : 0;
    
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return 0;
        }
    }
    public function sumRealisation($idnature, $periode_id) {
        try {
            // Si idnature = 1, on calcule la somme des réalisations pour tous les départements
            if ($idnature == 1) {
                $sql = "SELECT SUM(realisation) 
                        FROM budget 
                        JOIN Categorie ON budget.idCategorie = Categorie.idCategorie 
                        WHERE idNature = 1 
                        AND periode_id = '$periode_id' 
                        AND isApproved = TRUE"; // Assurer que la prévision est validée
            }
            // Si idnature = 2, on calcule la somme des dépenses pour tous les départements
            else if ($idnature == 2) {
                $sql = "SELECT SUM(realisation) 
                        FROM budget 
                        JOIN Categorie ON budget.idCategorie = Categorie.idCategorie 
                        WHERE idNature = 2 
                        AND periode_id = '$periode_id' 
                        AND isApproved = TRUE"; // Assurer que la prévision est validée
            }
    
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
    
            // Récupérer la somme des réalisations
            $sumRealisation = $stmt->fetchColumn();
    
            return $sumRealisation !== false ? $sumRealisation : 0;
    
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return 0;
        }
    }
    public function generateBudgetTableTous($startPeriod, $endPeriod) {
        $budgetData = [];
    
        // Initialisation des totaux globaux
        $totalRecettePrevision = 0;
        $totalDepensePrevision = 0;
        $totalRecetteRealisation = 0;
        $totalDepenseRealisation = 0;
        $totalSoldeFinPrevision = 0;
        $totalSoldeFinRealisation = 0;
        $totalEcartRecette = 0;
        $totalEcartDepense = 0;
        $totalSoldeDebut = 0;
    
        // Variable pour stocker le solde de fin de la période précédente (initialement 0)
        $previousSoldeFin = 0;
    
        // Boucle pour chaque période entre startPeriod et endPeriod
        for ($periode_id = $startPeriod; $periode_id <= $endPeriod; $periode_id++) {
    
            // Récupérer le solde de début pour cette période (solde de fin de la période précédente)
            $solde_debut = $previousSoldeFin;
    
            // Récupérer la somme globale des prévisions de recettes et de dépenses pour la période
            $recettePrevision = $this->sumPrevision(1, $periode_id);  // Recettes
            $depensePrevision = $this->sumPrevision(2, $periode_id);  // Dépenses
    
            // Récupérer la somme globale des réalisations de recettes et de dépenses pour la période
            $recetteRealisation = $this->sumRealisation(1, $periode_id);  // Recettes
            $depenseRealisation = $this->sumRealisation(2, $periode_id);  // Dépenses
    
            // Calcul du solde de fin global pour la période
            $solde_finPrevision = ($solde_debut + $recettePrevision) - $depensePrevision;
            $solde_finRealisation = ($solde_debut + $recetteRealisation) - $depenseRealisation;
    
            // Calcul de l'écart
            $ecartRecette = $recetteRealisation - $recettePrevision;
            $ecartDepense = $depenseRealisation - $depensePrevision;
    
            // Ajouter les résultats de la période aux données
            $budgetData[] = [
                'periode' => $periode_id,
                'solde_debut' => $solde_debut,
                'recette_prevision' => $recettePrevision,
                'depense_prevision' => $depensePrevision,
                'solde_fin_realisation' => $solde_finRealisation,
                'solde_fin_prevision' => $solde_finPrevision,
                'recette_realisation' => $recetteRealisation,
                'depense_realisation' => $depenseRealisation,
                'ecart_recette' => $ecartRecette,
                'ecart_depense' => $ecartDepense,
            ];
    
            // Accumuler les totaux pour toutes les périodes
            $totalRecettePrevision += $recettePrevision;
            $totalDepensePrevision += $depensePrevision;
            $totalRecetteRealisation += $recetteRealisation;
            $totalDepenseRealisation += $depenseRealisation;
            $totalSoldeFinPrevision += $solde_finPrevision;
            $totalSoldeFinRealisation += $solde_finRealisation;
            $totalEcartRecette += $ecartRecette;
            $totalEcartDepense += $ecartDepense;
            $totalSoldeDebut += $solde_debut;
    
            // Le solde de fin de cette période devient le solde de début de la prochaine période
            $previousSoldeFin = $solde_finRealisation; // Vous pouvez aussi utiliser solde_finPrevision ici selon la logique métier
        }
    
        // Ajouter les totaux globaux pour toutes les périodes
        $budgetData[] = [
            'periode' => 'Total',
            'solde_debut' => $totalSoldeDebut,
            'recette_prevision' => $totalRecettePrevision,
            'depense_prevision' => $totalDepensePrevision,
            'solde_fin_realisation' => $totalSoldeFinRealisation,
            'solde_fin_prevision' => $totalSoldeFinPrevision,
            'recette_realisation' => $totalRecetteRealisation,
            'depense_realisation' => $totalDepenseRealisation,
            'ecart_recette' => $totalEcartRecette,
            'ecart_depense' => $totalEcartDepense,
        ];
    
        return $budgetData;
    }
// Dans BudgetController.php


    
    
}

