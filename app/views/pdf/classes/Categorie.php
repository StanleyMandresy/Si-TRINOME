<?php
class Categorie
{
    public $id;
    public $nom;
    public $type;
    
    public function __construct($id, $nom, $type)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->type = $type;
    }
}