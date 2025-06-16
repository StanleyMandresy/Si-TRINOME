<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Retours CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .summary-card {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .badge-pending {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-approved {
            background-color: #198754;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <h1 class="mb-4">Gestion des Retours CRM</h1>

        <!-- Sélecteur de période -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="get" action="/list_crm" class="row g-3">
                    <div class="col-md-6">
                        <label for="periode-select" class="form-label">Période :</label>
                        <select id="periode-select" name="periode_id" class="form-select">
                            <?php foreach ($periodes as $periode): ?>
                                <option value="<?= $periode['periode_id'] ?>" 
                                    <?= ($periode['periode_id'] == $selectedPeriode) ? 'selected' : '' ?>>
                                    Période <?= $periode['periode_id'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary mt-4">
                            Afficher
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tableau des retours CRM -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Liste des Retours CRM
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nom CRM</th>
                                <th>Produit</th>
                                <th>Coût</th>
                                <th>% CA</th>
                               
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($crmRetours)): ?>
                                <?php foreach ($crmRetours as $crm): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($crm['NomCRM']) ?></td>
                                        <td><?= htmlspecialchars($crm['nomProduit']) ?></td>
                                        <td><?= number_format($crm['cout'], 2, ',', ' ') ?> €</td>
                                        <td><?= $crm['pourcentChiffreAffaire'] ?>%</td>
                                      
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        Aucun retour CRM pour cette période
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Résumé et validation -->
        <div class="row">
            <div class="col-md-8">
                <div class="summary-card">
                    <h5 class="mb-4">Récapitulatif</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Prévision CRM :</strong><br>
                            <span class="h4"><?= number_format($prevision, 2, ',', ' ') ?> €</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total des coûts :</strong><br>
                            <span class="h4 <?= ($totalCout > $prevision) ? 'text-danger' : 'text-success' ?>">
                                <?= number_format($totalCout, 2, ',', ' ') ?> €
                            </span></p>
                        </div>
                    </div>
                    <div class="alert <?= ($prevision > $totalCout) ? 'alert-success' : 'alert-danger' ?> mt-3">
                        <?= ($prevision > $totalCout) 
                            ? 'La prévision couvre les coûts (Ecart: '.number_format($prevision - $totalCout, 2, ',', ' ').' €)'
                            : 'Attention : Les coûts dépassent la prévision (Ecart: '.number_format($totalCout - $prevision, 2, ',', ' ').' €)' ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="mb-4">Validation</h5>
                        <?php if ($selectedPeriode): ?>
                            <form method="post" action="/valider-crm">
                                <input type="hidden" name="periode_id" value="<?= $selectedPeriode ?>">
                                <input type="hidden" name="montant" value="<?= $totalCout ?>">
                                <input type="hidden" name="ok" value="<?= $prevision > $totalCout ? '1' : '0' ?>">
                                <button type="submit" class="btn btn-success w-100">
                                    Valider les retours
                                </button>
                                <p class="small text-muted mt-2">
                                    Cette action validera tous les retours pour cette période.
                                </p>
                            </form>
                     
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>