<?php
namespace app\models;

use PDO;
use Exception;

class EtatModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Récupère le meilleur client pour une période
     * @param int $periode_id
     * @return array
     */
    public function getMeilleurClient($periode_id) {
        $sql = "SELECT c.nom, c.prenom, c.email, SUM(v.quantite) AS total_achete
                FROM vente v
                JOIN Client c ON c.idClient = v.idClient
                WHERE v.periode_id = :periode_id
                GROUP BY v.idClient
                ORDER BY total_achete DESC
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':periode_id' => $periode_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les 5 meilleurs clients pour une période
     * @param int $periode_id
     * @return array
     */
    public function getTop5Clients($periode_id) {
        $sql = "SELECT c.nom, c.prenom, c.email, SUM(v.quantite) AS total_achete
                FROM vente v
                JOIN Client c ON c.idClient = v.idClient
                WHERE v.periode_id = :periode_id
                GROUP BY v.idClient
                ORDER BY total_achete DESC
                LIMIT 5";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':periode_id' => $periode_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les 5 produits les plus vendus pour une période
     * @param int $periode_id
     * @return array
     */
    public function getTop5Produits($periode_id) {
        $sql = "SELECT p.nomProduit, SUM(v.quantite) AS total_vendu
                FROM vente v
                JOIN Produit p ON p.idProduit = v.idProduit
                WHERE v.periode_id = :periode_id
                GROUP BY v.idProduit
                ORDER BY total_vendu DESC
                LIMIT 5";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':periode_id' => $periode_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les 5 produits les moins vendus pour une période
     * @param int $periode_id
     * @return array
     */
    public function getFlop5Produits($periode_id) {
        $sql = "SELECT p.nomProduit, SUM(v.quantite) AS total_vendu
                FROM vente v
                JOIN Produit p ON p.idProduit = v.idProduit
                WHERE v.periode_id = :periode_id
                GROUP BY v.idProduit
                HAVING total_vendu > 0
                ORDER BY total_vendu ASC
                LIMIT 5";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':periode_id' => $periode_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les produits jamais vendus pour une période
     * @param int $periode_id
     * @return array
     */
    public function getProduitsNonVendus($periode_id) {
        $sql = "SELECT p.nomProduit 
                FROM Produit p
                LEFT JOIN vente v ON p.idProduit = v.idProduit AND v.periode_id = :periode_id
                WHERE v.idProduit IS NULL";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':periode_id' => $periode_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

   
}