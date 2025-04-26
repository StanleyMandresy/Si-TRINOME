<?php
class Periode
{
    public $id;
    public $mois;
    public $nom;
    
    public function __construct($id, $mois, $nom)
    {
        $this->id = $id;
        $this->mois = new DateTime($mois);
        $this->nom = $nom;
    }
}