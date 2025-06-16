<?php
// Création d'une table de correspondance
$periodeLabels = [];
foreach ($periodes as $periode) {
    $periodeLabels[$periode['periode_id']] = $periode['nom_periode'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stat</title>
    <style>
        .stats-section {
            margin-top: 40px;
            border-top: 2px solid #ddd;
            padding-top: 20px;
        }
        .stat-block {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-title {
            color: #0d6efd;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .produit-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #eee;
        }
    </style>
</head>
<body>
<h1>Statistiques</h1>

<!-- Formulaire pour chercher chiffre d'affaires d'une période -->
<form method="POST" action="/statistiques">
    <label for="periode_id">Entrez la période :</label>
    <input type="number" name="periode_id" id="periode_id" required>
    <button type="submit">Chercher</button>
</form>

<!-- Afficher le chiffre d'affaires de la période si demandé -->
<?php if (isset($caPeriode)) : ?>
    <h2>Chiffre d'affaires pour la période <?= htmlspecialchars($_POST['periode_id']) ?> : <?= htmlspecialchars($caPeriode) ?> €</h2>
<?php endif; ?>

<hr>

<!-- Graphe chiffre d'affaires par période -->
<h2>Chiffre d'affaires par période</h2>

<?php if (!empty($chiffres)): ?>
    <canvas id="chiffreAffairesChart" width="400" height="200"></canvas>

<?php else: ?>
    <div class="alert alert-warning">Aucune donnée de ventes disponible</div>
<?php endif; ?>

<!-- Graphe ventes par période -->
<h2>Ventes par période</h2>

<?php if (!empty($ventes)): ?>
    <canvas id="ventesChart" width="400" height="200"></canvas>
<?php else: ?>
    <div class="alert alert-warning">Aucune donnée de ventes disponible</div>
<?php endif; ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labelsChiffre = <?= json_encode(array_map(function($c) use ($periodeLabels) {
    return $periodeLabels[$c['periode_id']] ?? $c['periode_id'];
}, $chiffres)) ?>;

const labelsVentes = <?= json_encode(array_map(function($v) use ($periodeLabels) {
    return $periodeLabels[$v['periode_id']] ?? $v['periode_id'];
}, $ventes)) ?>;

// ⚡ AJOUT ICI ⚡
const dataChiffre = <?= json_encode(array_column($chiffres, 'chiffre_affaires')) ?>;
const dataVentes = <?= json_encode(array_column($ventes, 'total_ventes')) ?>;

// Graphe chiffre d'affaires
const ctxChiffre = document.getElementById('chiffreAffairesChart').getContext('2d');
new Chart(ctxChiffre, {
    type: 'line',
    data: {
        labels: labelsChiffre,
        datasets: [{
            label: 'Chiffre d\'Affaires (€)',
            data: dataChiffre,
            borderColor: 'blue',
            backgroundColor: 'lightblue',
            fill: false
        }]
    },
    options: {
        scales: {
            x: { title: { display: true, text: 'Période' } },
            y: { title: { display: true, text: 'Chiffre d\'Affaires (€)' } }
        }
    }
});

// Graphe ventes
const ctxVentes = document.getElementById('ventesChart').getContext('2d');
new Chart(ctxVentes, {
    type: 'line',
    data: {
        labels: labelsVentes,
        datasets: [{
            label: 'Total Ventes (€)',
            data: dataVentes,
            borderColor: 'green',
            backgroundColor: 'lightgreen',
            fill: false
        }]
    },
    options: {
        scales: {
            x: { title: { display: true, text: 'Période' } },
            y: { title: { display: true, text: 'Total Ventes (€)' } }
        }
    }
});
</script> 
<div class="stats-section">
        <h2>Statistiques détaillées</h2>

        <?php if (isset($caPeriode)): ?>
            <!-- Bloc Meilleurs Clients -->
            <div class="stat-block">
                <h3 class="stat-title">Top 5 Clients (Période <?= htmlspecialchars($_POST['periode_id']) ?>)</h3>
                <?php if (!empty($meilleurClient)): ?>
                    <div class="client-list">
                        <?php foreach ($meilleurClient as $client): ?>
                            <div class="produit-item">
                                <span><?= htmlspecialchars($client['nom'] . ' ' . $client['prenom']) ?></span>
                                <strong><?= $client['total_achete'] ?> achats</strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Aucun client trouvé pour cette période.</p>
                <?php endif; ?>
            </div>

            <!-- Bloc Top Produits -->
            <div class="stat-block">
                <h3 class="stat-title">Top 5 Produits</h3>
                <?php if (!empty($topProduits)): ?>
                    <div class="produit-list">
                        <?php foreach ($topProduits as $produit): ?>
                            <div class="produit-item">
                                <span><?= htmlspecialchars($produit['nomProduit']) ?></span>
                                <strong><?= $produit['total_vendu'] ?> ventes</strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Aucun produit trouvé pour cette période.</p>
                <?php endif; ?>
            </div>

            <!-- Bloc Flop Produits -->
            <div class="stat-block">
                <h3 class="stat-title">5 Produits les moins vendus</h3>
                <?php if (!empty($flopProduits)): ?>
                    <div class="produit-list">
                        <?php foreach ($flopProduits as $produit): ?>
                            <div class="produit-item">
                                <span><?= htmlspecialchars($produit['nomProduit']) ?></span>
                                <strong><?= $produit['total_vendu'] ?> ventes</strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Aucun produit trouvé pour cette période.</p>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <div class="alert alert-info">
                Sélectionnez une période pour voir les statistiques détaillées.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

