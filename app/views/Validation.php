<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Budgets</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .positive {
            color: green;
        }
        .negative {
            color: red;
        }
    </style>
</head>
<body>

    <h2>Liste des Budgets</h2>
    <form action="addbudget" method="get">
        <button name="button" value="ajouter">Ajouter un budget</button>
    </form>

    <?php if (!empty($budgets)) : ?>
        <table>
            <thead>
                <tr>
                    <th>Département</th>
                    <th>Catégorie</th>
                    <th>Prévision</th>
                    <th>Réalisation</th>
                    <th>Écart</th>
                    <th>Date</th>
                    <th>Valider</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($budgets as $budget) : ?>
                    <?php if (strtoupper($budget['nomCategorie']) != 'CRM' && $budget['nomCategorie'] != 'Ticket') : ?>
                        <tr>
                            <td><?= htmlspecialchars($budget['nomDepartement']) ?></td>
                            <td><?= htmlspecialchars($budget['nomCategorie']) ?></td>
                            <td><?= number_format($budget['Prevision'], 2, ',', ' ') ?></td>
                            <td><?= number_format($budget['Realisation'], 2, ',', ' ') ?></td>
                            <td class="<?= ($budget['Ecart'] >= 0) ? 'positive' : 'negative' ?>">
                                <?= number_format($budget['Ecart'], 2, ',', ' ') ?>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($budget['DateBudget'])) ?></td>
                            <td>
                                <form action="validation" method="post">
                                    <input type="hidden" name="valide" value="<?= $budget['idBudget'] ?>">
                                    <button type="submit">Valider</button>
                                </form>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>Aucun budget trouvé.</p>
    <?php endif; ?>

    <h2>Validation des CRM</h2>

    <?php if (!empty($budgets)) : ?>
        <table>
            <thead>
                <tr>
                    <th>Département</th>
                    <th>Catégorie</th>
                    <th>Prévision</th>
                    <th>Réalisation</th>
                    <th>Écart</th>
                    <th>Date</th>
                    <th>Valider</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($budgets as $budget) : ?>
                    <?php if (strtoupper($budget['nomCategorie']) == 'CRM') : ?>
                        <tr>
                            <td><?= htmlspecialchars($budget['nomDepartement']) ?></td>
                            <td><?= htmlspecialchars($budget['nomCategorie']) ?></td>
                            <td><?= number_format($budget['Prevision'], 2, ',', ' ') ?></td>
                            <td><?= number_format($budget['Realisation'], 2, ',', ' ') ?></td>
                            <td class="<?= ($budget['Ecart'] >= 0) ? 'positive' : 'negative' ?>">
                                <?= number_format($budget['Ecart'], 2, ',', ' ') ?>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($budget['DateBudget'])) ?></td>
                            <td>
                                <form action="validation" method="post">
                                    <input type="hidden" name="valide" value="<?= $budget['idBudget'] ?>">
                                    <input type="hidden" name="crmValide" value="<?= $budget['periode_id'] ?>">
                                    <button type="submit">Valider CRM</button>
                                </form>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>Aucun CRM trouvé.</p>
    <?php endif; ?>
    
    <h2>Validation des Ticket</h2>

    <?php if (!empty($budgets)) : ?>
        <table>
            <thead>
                <tr>
                    <th>Département</th>
                    <th>Catégorie</th>
                    <th>Prévision</th>
                    <th>Réalisation</th>
                    <th>Écart</th>
                    <th>Date</th>
                    <th>Valider</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($budgets as $budget) : ?>
                    <?php if (strtoupper($budget['nomCategorie'] == 'Ticket')) : ?>
                        <tr>
                            <td><?= htmlspecialchars($budget['nomDepartement']) ?></td>
                            <td><?= htmlspecialchars($budget['nomCategorie']) ?></td>
                            <td><?= number_format($budget['Prevision'], 2, ',', ' ') ?></td>
                            <td><?= number_format($budget['Realisation'], 2, ',', ' ') ?></td>
                            <td class="<?= ($budget['Ecart'] >= 0) ? 'positive' : 'negative' ?>">
                                <?= number_format($budget['Ecart'], 2, ',', ' ') ?>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($budget['DateBudget'])) ?></td>
                            <td>
                                <form action="validation" method="post">
                                    <input type="hidden" name="valide" value="<?= $budget['idBudget'] ?>">
                                    <input type="hidden" name="TicketValide" value="<?= $budget['periode_id'] ?>">
                                    <button type="submit">Valider Ticket</button>
                                </form>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>Aucun Ticket trouvé.</p>
    <?php endif; ?>
    <div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Ajouter une prévision CRM</h5>
    </div>
    <div class="card-body">
        <form method="post" action="/save-prevision" class="row g-3">
            <div class="col-md-6">
                <label for="periode_id" class="form-label">Période</label>
                <select class="form-select" id="periode_id" name="periode_id" required>
                    <option value="">Sélectionner...</option>
                    <?php foreach ($periodes as $periode): ?>
                        <option value="<?= $periode['periode_id'] ?>">
                            <?= htmlspecialchars($periode['nom_periode'] ?? 'Période '.$periode['periode_id']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-4">
                <label for="montant" class="form-label">Montant (€)</label>
                <input type="number" step="0.01" min="0" class="form-control" 
                       id="montant" name="montant" required>
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    Enregistrer
                </button>
            </div>
        </form>
        
        <?php if (isset($prevision_message)): ?>
            <div class="alert alert-<?= $prevision_success ? 'success' : 'danger' ?> mt-3">
                <?= htmlspecialchars($prevision_message) ?>
            </div>
        <?php endif; ?>
    </div>
      <div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Ajouter une prévision Ticket</h5>
    </div>
    <div class="card-body">
        <form method="post" action="/save-Ticket" class="row g-3">
            <div class="col-md-6">
                <label for="periode_id" class="form-label">Période</label>
                <select class="form-select" id="periode_id" name="periode_id" required>
                    <option value="">Sélectionner...</option>
                    <?php foreach ($periodes as $periode): ?>
                        <option value="<?= $periode['periode_id'] ?>">
                            <?= htmlspecialchars($periode['nom_periode'] ?? 'Période '.$periode['periode_id']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-4">
                <label for="montant" class="form-label">Montant (€)</label>
                <input type="number" step="0.01" min="0" class="form-control" 
                       id="montant" name="montant" required>
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    Enregistrer
                </button>
            </div>
        </form>
        
       
    </div>
</div>   

</body>
</html>
