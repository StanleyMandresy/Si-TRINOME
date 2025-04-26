<?php
class DonneeBudget
{
    public $id;
    public $id_periode;
    public $id_categorie;
    public $prevision;
    public $realisation;
    
    public function __construct($data)
    {
        $this->id = $data['id'] ?? null;
        $this->id_periode = $data['id_periode'];
        $this->id_categorie = $data['id_categorie'];
        $this->prevision = $data['prevision'];
        $this->realisation = $data['realisation'];
    }
    
    public function getEcart()
    {
        return $this->realisation - $this->prevision;
    }
}