<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Ventes par Période</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Liste des Ventes par Période</h1>

        <!-- Formulaire de sélection de période -->
        <form method="get" action="">
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="periode_id" class="form-label">Sélectionner une période :</label>
                    <select name="periode_id" id="periode_id" class="form-select">
                        <option value="">Toutes les périodes</option>
                        <?php foreach ($periodes as $periode): ?>
                            <option value="<?= $periode['periode_id'] ?>" 
                                <?= ($selectedPeriode == $periode['periode_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($periode['nom_periode'] ?? 'Période '.$periode['periode_id']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filtrer</button>
                    <a href="?" class="btn btn-secondary">Réinitialiser</a>
                </div>
            </div>
        </form>

        <!-- Formulaire d'import CSV -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Importer des données</h5>
            </div>
            <div class="card-body">
                <form method="post" action="/ventes/import" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="csv_type" class="form-label">Type de données :</label>
                        <select class="form-select" name="csv_type" id="csv_type" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="produits">Produits</option>
                            <option value="ventes">Ventes</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">Fichier CSV :</label>
                        <input class="form-control" type="file" name="csv_file" id="csv_file" accept=".csv" required>
                    </div>
                    <button type="submit" class="btn btn-success">Importer</button>
                </form>
            </div>
        </div>

        <!-- Tableau des ventes -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Période</th>
                        <th>NomProduit</th>
                        <th>Quantité Totale</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ventes)): ?>
                        <?php foreach ($ventes as $vente): ?>
                            <tr>
                                <td>Période <?= htmlspecialchars($vente['periode_id']) ?></td>
                                <td><?= $vente['nomProduit'] ?></td>
                                <td><?= htmlspecialchars($vente['quantite']) ?></td>
                             
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">Aucune donnée de vente disponible</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>