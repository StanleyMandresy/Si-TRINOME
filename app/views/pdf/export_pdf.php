<?php
require_once('fpdf.php');
require_once 'config/database.php';
require_once 'classes/Database.php';
require_once 'classes/Periode.php';
require_once 'classes/Categorie.php';
require_once 'classes/DonneeBudget.php';
require_once 'classes/Budget.php';

class PDF extends FPDF
{
    public $maxWidth = 190; // Largeur max en mm (A4 landscape - marges)
    
    function Header()
    {
        $this->SetFont('Arial','B',16);
        $this->Cell(0,10,$this->convertText('Tableau Budgétaire Mensuel'),0,1,'C');
        $this->Ln(5);
    }
    
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
    
    function CalculateTableWidth($colCount)
    {
        $firstColWidth = 50;
        $dataColWidth = 25;
        return $firstColWidth + ($colCount * $dataColWidth * 3);
    }
    
    // Méthode publique pour la conversion du texte
    public function convertText($text) {
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);
    }
    
    function DrawTableSection($data, $startCol, $endCol, $isSecondPart = false)
    {
        if($isSecondPart) {
            $this->Ln(10);
            $this->SetFont('Arial','B',12);
            $this->Cell(0,10,$this->convertText('Suite du tableau'),0,1,'C');
            $this->Ln(5);
        }
        
        $colWidth = 25;
        $firstColWidth = 50;
        
        // Couleurs
        $headerR = 211; $headerG = 211; $headerB = 211;
        $positiveR = 220; $positiveG = 255; $positiveB = 220;
        $negativeR = 255; $negativeG = 220; $negativeB = 220;
        
        // En-têtes
        $this->SetFont('Arial','B',10);
        $this->SetFillColor($headerR, $headerG, $headerB);
        
        // Première ligne
        $this->Cell($firstColWidth,8,$this->convertText('Catégories'),1,0,'C',true);
        for ($i = $startCol; $i <= $endCol; $i++) {
            $periode = $data['periodes'][$i];
            $this->Cell($colWidth*3,8,$this->convertText($periode->nom),1,0,'C',true);
        }
        $this->Ln();
        
        // Deuxième ligne
        $this->Cell($firstColWidth,8,'',1,0,'C',true);
        for ($i = $startCol; $i <= $endCol; $i++) {
            $this->Cell($colWidth,8,$this->convertText('Prévision'),1,0,'C',true);
            $this->Cell($colWidth,8,$this->convertText('Réalisation'),1,0,'C',true);
            $this->Cell($colWidth,8,$this->convertText('Écart'),1,0,'C',true);
        }
        $this->Ln();
        
        // Données
        $this->SetFont('Arial','',9);
        $totals = [];
        
        foreach ($data['lignes'] as $ligne) {
            $this->SetFont('',$ligne['categorie']->type === 'solde' ? 'B' : '');
            $this->Cell($firstColWidth,8,$this->convertText($ligne['categorie']->nom),1);
            
            for ($i = $startCol; $i <= $endCol; $i++) {
                $periode = $data['periodes'][$i];
                $item = $ligne['data'][$periode->id];
                
                if ($ligne['categorie']->type !== 'solde') {
                    if (!isset($totals[$i])) {
                        $totals[$i] = ['prevision' => 0, 'realisation' => 0, 'ecart' => 0];
                    }
                    $totals[$i]['prevision'] += $item['prevision'];
                    $totals[$i]['realisation'] += $item['realisation'];
                    $totals[$i]['ecart'] += $item['ecart'];
                }
                
                // Cellules de données
                $this->Cell($colWidth,8,number_format($item['prevision'],2,',',' '),1,0,'R');
                $this->Cell($colWidth,8,number_format($item['realisation'],2,',',' '),1,0,'R');
                
                // Écart coloré
                $ecart = $item['ecart'];
                if ($ecart > 0) {
                    $this->SetFillColor($positiveR, $positiveG, $positiveB);
                } elseif ($ecart < 0) {
                    $this->SetFillColor($negativeR, $negativeG, $negativeB);
                } else {
                    $this->SetFillColor(255, 255, 255);
                }
                $this->Cell($colWidth,8,number_format($ecart,2,',',' '),1,0,'R',true);
                $this->SetFillColor(255, 255, 255);
            }
            $this->Ln();
        }
        
        // Totaux
        $this->SetFont('Arial','B',9);
        $this->SetFillColor($headerR, $headerG, $headerB);
        $this->Cell($firstColWidth,8,$this->convertText('TOTAUX'),1,0,'C',true);
        
        for ($i = $startCol; $i <= $endCol; $i++) {
            $t = $totals[$i] ?? ['prevision'=>0,'realisation'=>0,'ecart'=>0];
            $this->Cell($colWidth,8,number_format($t['prevision'],2,',',' '),1,0,'R',true);
            $this->Cell($colWidth,8,number_format($t['realisation'],2,',',' '),1,0,'R',true);
            
            $ecartTotal = $t['ecart'];
            if ($ecartTotal > 0) {
                $this->SetFillColor($positiveR, $positiveG, $positiveB);
            } elseif ($ecartTotal < 0) {
                $this->SetFillColor($negativeR, $negativeG, $negativeB);
            } else {
                $this->SetFillColor($headerR, $headerG, $headerB);
            }
            $this->Cell($colWidth,8,number_format($ecartTotal,2,',',' '),1,0,'R',true);
            $this->SetFillColor(255, 255, 255);
        }
        $this->Ln();
    }
}

// Initialisation
$config = require 'config/database.php';
$db = Database::getInstance($config)->getConnection();
$manager = new BudgetManager($db);

// Paramètres
$moisDebut = $_GET['mois_debut'] ?? null;
$moisFin = $_GET['mois_fin'] ?? null;

// Données
$data = $manager->prepareBudgetData($moisDebut, $moisFin);
$allPeriodes = $manager->getAllPeriodes();

// Création PDF
$pdf = new PDF('L');
$pdf->AliasNbPages();
$pdf->AddPage();

// Titre période
$pdf->SetFont('Arial','',12);
if ($moisDebut && $moisFin) {
    foreach ($allPeriodes as $periode) {
        if ($periode->id == $moisDebut) $periodeDebut = $periode;
        if ($periode->id == $moisFin) $periodeFin = $periode;
    }
    $titre = $pdf->convertText('Période du '.$periodeDebut->nom.' au '.$periodeFin->nom);
    $pdf->Cell(0,10,$titre,0,1,'C');
}
$pdf->Ln(8);

// Calcul division
$totalCols = count($data['periodes']);
$tableWidth = $pdf->CalculateTableWidth($totalCols);
$needsSplit = ($tableWidth > $pdf->maxWidth);

if ($needsSplit && $totalCols > 1) {
    $splitPoint = ceil($totalCols / 2);
    $pdf->DrawTableSection($data, 0, $splitPoint - 1);
    $pdf->DrawTableSection($data, $splitPoint, $totalCols - 1, true);
} else {
    $pdf->DrawTableSection($data, 0, $totalCols - 1);
}

$pdf->Output('I','tableau_budgetaire.pdf');
?>