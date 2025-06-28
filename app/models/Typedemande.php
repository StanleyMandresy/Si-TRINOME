<?php
namespace app\models;

use PDO;
use Exception;

class Typedemande {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // 🔹 Récupérer tous les types de demande
    public function getAllTypes() {
        $sql = "SELECT * FROM Type_demande ORDER BY nom_type ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Récupérer un type par ID
    public function getTypeById($id) {
        $sql = "SELECT * FROM Type_demande WHERE id_type = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 Ajouter un nouveau type
    public function addType($nom_type) {
        $sql = "INSERT INTO Type_demande (nom_type) VALUES (?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nom_type]);
    }

    // 🔹 Mettre à jour un type existant
    public function updateType($id_type, $nom_type) {
        $sql = "UPDATE Type_demande SET nom_type = ? WHERE id_type = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nom_type, $id_type]);
    }

    // 🔹 Supprimer un type
    public function deleteType($id_type) {
        $sql = "DELETE FROM Type_demande WHERE id_type = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id_type]);
    }
}
