<?php
class BudgetManager
{
    private $db;
    
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    public function getAllPeriodes()
    {
        $stmt = $this->db->query("SELECT * FROM periodes ORDER BY mois");
        return $stmt->fetchAll(PDO::FETCH_FUNC, function($id, $mois, $nom) {
            return new Periode($id, $mois, $nom);
        });
    }
    
    public function getCategories()
    {
        $stmt = $this->db->query("
            SELECT * FROM categories 
            ORDER BY 
                CASE type 
                    WHEN 'solde' THEN 1 
                    WHEN 'recette' THEN 2 
                    WHEN 'depense' THEN 3 
                    ELSE 4 
                END, 
                id
        ");
        return $stmt->fetchAll(PDO::FETCH_FUNC, function($id, $nom, $type) {
            return new Categorie($id, $nom, $type);
        });
    }
    
    public function getDonneesForPeriodes($periodeIds)
    {
        if (empty($periodeIds)) return [];
        
        $placeholders = implode(',', array_fill(0, count($periodeIds), '?'));
        $stmt = $this->db->prepare("
            SELECT * FROM donnees_budget
            WHERE id_periode IN ($placeholders)
        ");
        $stmt->execute($periodeIds);
        return $stmt->fetchAll(PDO::FETCH_FUNC, function($id, $id_periode, $id_categorie, $prevision, $realisation) {
            return new DonneeBudget([
                'id' => $id,
                'id_periode' => $id_periode,
                'id_categorie' => $id_categorie,
                'prevision' => $prevision,
                'realisation' => $realisation
            ]);
        });
    }
    
    public function prepareBudgetData($moisDebutId = null, $moisFinId = null)
    {
        // Récupérer les périodes filtrées
        $query = "SELECT * FROM periodes";
        $params = [];
        
        if ($moisDebutId && $moisFinId) {
            $query .= " WHERE id BETWEEN ? AND ?";
            $params = [$moisDebutId, $moisFinId];
        }
        
        $query .= " ORDER BY mois";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $periodes = $stmt->fetchAll(PDO::FETCH_FUNC, function($id, $mois, $nom) {
            return new Periode($id, $mois, $nom);
        });
        
        // Si aucune période sélectionnée, prendre toutes
        $periodeIds = array_map(function($p) { return $p->id; }, $periodes);
        $categories = $this->getCategories();
        $donnees = $this->getDonneesForPeriodes($periodeIds);
        
        // Organiser les données
        $data = [
            'periodes' => $periodes,
            'lignes' => []
        ];
        
        // Indexer les données par période et catégorie
        $donneesIndex = [];
        foreach ($donnees as $donnee) {
            $donneesIndex[$donnee->id_periode][$donnee->id_categorie] = $donnee;
        }
        
        // Remplir les lignes par catégorie
        foreach ($categories as $categorie) {
            $ligne = [
                'categorie' => $categorie,
                'data' => []
            ];
            
            foreach ($periodes as $periode) {
                $donnee = $donneesIndex[$periode->id][$categorie->id] ?? null;
                
                $ligne['data'][$periode->id] = [
                    'prevision' => $donnee ? $donnee->prevision : 0,
                    'realisation' => $donnee ? $donnee->realisation : 0,
                    'ecart' => $donnee ? $donnee->getEcart() : 0
                ];
            }
            
            $data['lignes'][] = $ligne;
        }
        
        return $data;
    }
}