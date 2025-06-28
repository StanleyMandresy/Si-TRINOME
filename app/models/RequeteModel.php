<?php
namespace app\models;

use PDO;

class RequeteModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }


public function getRequetesByClient($idClient) {
    $sql = "SELECT r.id, r.sujet,r.classified,p.nomProduit
            FROM Requete_client r
            JOIN Produit p ON r.idproduit_concerne = p.idProduit
            WHERE r.idclient = ?";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$idClient]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function insertRequete($idClient, $idProduit, $sujet) {
        $sql = "INSERT INTO Requete_client (idclient, idproduit_concerne, sujet)
                VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idClient, $idProduit, $sujet]);
    }

    public function getAllRequetes() {
        $sql = "SELECT r.*, c.nom, c.prenom, p.nomProduit 
                FROM Requete_client r
                JOIN Client c ON c.idClient = r.idclient
                JOIN Produit p ON p.idProduit = r.idproduit_concerne
             WHERE r.classified = FALSE";
                
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getRequeteById($id) {
        $sql = "SELECT * FROM Requete_client WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
