<?php

namespace app\models;

use PDO;
use Exception;

class RetourModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getTypesCRM() {
        $sql = "SELECT idTypeCRM,nomTypeCRM  FROM TypeCRM";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProduits() {
        $sql = "SELECT idProduit, nomProduit FROM Produit";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insererRetour($idClient, $idTypeCRM, $idProduit, $notes) {
        $sql = "INSERT INTO RetourClient (idClient, idTypeCRM, idProduit, notesSupplementaires) 
                VALUES (:idClient, :idTypeCRM, :idProduit, :notes)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':idClient' => $idClient,
            ':idTypeCRM' => $idTypeCRM,
            ':idProduit' => $idProduit,
            ':notes' => $notes
        ]);
    }
}
