<?php
function formatNumber($value)
{
    return $value === null ? '-' : number_format($value, 2, ',', ' ');
}

function getColorClass($value)
{
    if ($value === null) return '';
    if ($value < 0) return 'negative';
    if ($value > 0) return 'positive';
    return '';
}
?>
<style>
    .budget-table {
        border-collapse: collapse;
        width: 100%;
        margin: 20px 0;
    }

    .budget-table th,
    .budget-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: right;
    }

    .budget-table th {
        background-color: #f2f2f2;
        text-align: center;
    }

    .positive {
        background-color: #ddffdd;
        color: #006400;
    }

    .negative {
        background-color: #ffdddd;
        color: #d80000;
    }

    .solde {
        font-weight: bold;
        border-top: 2px solid #333;
    }

    .total {
        font-weight: bold;
        background-color: #e0e0e0;
    }

    .filter-form {
        margin-bottom: 20px;
        padding: 15px;
        background: #f5f5f5;
        border-radius: 5px;
    }

    .filter-form label {
        margin-right: 10px;
    }

    .filter-form select,
    .filter-form button {
        padding: 5px 10px;
        margin-right: 15px;
    }

    .export-btn {
        display: inline-block;
        padding: 5px 15px;
        background-color: #4CAF50;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        margin-left: 10px;
        font-size: 14px;
    }

    .export-btn:hover {
        background-color: #45a049;
    }
</style>

<div class="filter-form">
    <form method="post">
        <label for="mois_debut">Période du:</label>
        <select name="mois_debut" id="mois_debut" required>
            <?php foreach ($allPeriodes as $periode): ?>
                <option value="<?= $periode->id ?>" <?= $periode->id == $moisDebut ? 'selected' : '' ?>>
                    <?= htmlspecialchars($periode->nom) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="mois_fin">au:</label>
        <select name="mois_fin" id="mois_fin" required>
            <?php foreach ($allPeriodes as $periode): ?>
                <option value="<?= $periode->id ?>" <?= $periode->id == $moisFin ? 'selected' : '' ?>>
                    <?= htmlspecialchars($periode->nom) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Afficher</button>

        <a href="export_pdf.php?mois_debut=<?= $moisDebut ?>&mois_fin=<?= $moisFin ?>"
            class="export-btn"
            target="_blank">
            Exporter en PDF
        </a>
    </form>
</div>

<table class="budget-table">
    <tr>
        <th rowspan="2">Catégories</th>
        <?php foreach ($data['periodes'] as $periode): ?>
            <th colspan="3"><?= htmlspecialchars($periode->nom) ?></th>
        <?php endforeach; ?>
    </tr>
    <tr>
        <?php foreach ($data['periodes'] as $periode): ?>
            <th>Prévision</th>
            <th>Réalisation</th>
            <th>Écart</th>
        <?php endforeach; ?>
    </tr>

    <?php
    $totals = [];
    foreach ($data['lignes'] as $ligne):
        $class = '';
        if ($ligne['categorie']->type === 'solde') $class = 'solde';
    ?>
        <tr class="<?= $class ?>">
            <td><?= htmlspecialchars($ligne['categorie']->nom) ?></td>

            <?php foreach ($data['periodes'] as $periode):
                $item = $ligne['data'][$periode->id];

                // Calcul des totaux (sauf pour les soldes)
                if ($ligne['categorie']->type !== 'solde') {
                    if (!isset($totals[$periode->id])) {
                        $totals[$periode->id] = ['prevision' => 0, 'realisation' => 0, 'ecart' => 0];
                    }
                    $totals[$periode->id]['prevision'] += $item['prevision'];
                    $totals[$periode->id]['realisation'] += $item['realisation'];
                    $totals[$periode->id]['ecart'] += $item['ecart'];
                }
            ?>
                <td><?= formatNumber($item['prevision']) ?></td>
                <td><?= formatNumber($item['realisation']) ?></td>
                <td class="<?= getColorClass($item['ecart']) ?>">
                    <?= formatNumber($item['ecart']) ?>
                </td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>

    <!-- Ligne des totaux -->
    <tr class="total">
        <td>Totaux</td>
        <?php foreach ($data['periodes'] as $periode): ?>
            <td><?= formatNumber($totals[$periode->id]['prevision'] ?? 0) ?></td>
            <td><?= formatNumber($totals[$periode->id]['realisation'] ?? 0) ?></td>
            <td class="<?= getColorClass($totals[$periode->id]['ecart'] ?? 0) ?>">
                <?= formatNumber($totals[$periode->id]['ecart'] ?? 0) ?>
            </td>
        <?php endforeach; ?>
    </tr>
</table>