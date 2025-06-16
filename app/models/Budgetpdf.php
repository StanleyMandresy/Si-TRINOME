?php
require_once __DIR__.'/../../pdf/fpdf.php';
class Budgetpdf extends FPDF {
    private $title;
    private $periods;
    private $data;
    private $isTotalTable;

    public function __construct($title, $periods, $data, $isTotalTable = false) {
        parent::__construct('L'); // Orientation paysage
        $this->title = $title;
        $this->periods = $periods;
        $this->data = $data;
        $this->isTotalTable = $isTotalTable;
    }

    function Header() {
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,$this->title,0,1,'C');
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }

    function GenerateTable() {
        $this->AddPage();
        $this->SetFont('Arial','',10);
        
        // Largeurs des colonnes
        $wRubrique = 40;
        $wCol = ($this->GetPageWidth() - $wRubrique - 20) / (count($this->periods) * 3);
        
        // En-tête des périodes
        $this->SetFont('Arial','B',10);
        $this->Cell($wRubrique, 10, 'Rubrique', 1, 0, 'C');
        foreach($this->periods as $period) {
            $this->Cell($wCol * 3, 10, 'Période '.$period, 1, 0, 'C');
        }
        $this->Ln();
        
        // Sous-en-tête (Prévision/Réalisation/Écart)
        $this->Cell($wRubrique, 10, '', 1, 0, 'C');
        foreach($this->periods as $period) {
            $this->Cell($wCol, 10, 'Prévision', 1, 0, 'C');
            $this->Cell($wCol, 10, 'Réalisation', 1, 0, 'C');
            $this->Cell($wCol, 10, 'Écart', 1, 0, 'C');
        }
        $this->Ln();
        
        // Données
        $rubriques = ['Solde Début', 'Recette', 'Dépense', 'Solde Fin'];
        
        foreach($rubriques as $rubrique) {
            $this->SetFont('Arial','B',10);
            $this->Cell($wRubrique, 8, $rubrique, 1);
            
            foreach($this->periods as $period) {
                $row = $this->findPeriodData($period);
                
                $this->SetFont('Arial','',9);
                switch($rubrique) {
                    case 'Solde Début':
                        $this->Cell($wCol, 8, $this->formatNumber($row['solde_debut']), 1);
                        $this->Cell($wCol, 8, $this->formatNumber($row['solde_debut']), 1);
                        $this->Cell($wCol, 8, '0', 1);
                        break;
                    case 'Recette':
                        $this->Cell($wCol, 8, $this->formatNumber($row['recette_prevision']), 1);
                        $this->Cell($wCol, 8, $this->formatNumber($row['recette_realisation']), 1);
                        $this->Cell($wCol, 8, $this->formatNumber($row['ecart_recette']), 1);
                        break;
                    case 'Dépense':
                        $this->Cell($wCol, 8, $this->formatNumber($row['depense_prevision']), 1);
                        $this->Cell($wCol, 8, $this->formatNumber($row['depense_realisation']), 1);
                        $this->Cell($wCol, 8, $this->formatNumber($row['ecart_depense']), 1);
                        break;
                    case 'Solde Fin':
                        $this->Cell($wCol, 8, $this->formatNumber($row['solde_fin_prevision']), 1);
                        $this->Cell($wCol, 8, $this->formatNumber($row['solde_fin_realisation']), 1);
                        $this->Cell($wCol, 8, '', 1);
                        break;
                }
            }
            $this->Ln();
        }
    }
    
    private function findPeriodData($period) {
        foreach($this->data as $row) {
            if($row['periode'] == $period) {
                return $row;
            }
        }
        return null;
    }
    
    private function formatNumber($value) {
        return number_format($value, 2, ',', ' ');
    }
}
?>