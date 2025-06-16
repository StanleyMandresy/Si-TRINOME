<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        form { display: inline; }
        select, button { padding: 5px; margin: 2px; }
        .form-group { display: inline-block; margin-right: 10px; }
        label { display: block; font-size: 0.8em; color: #666; }
    </style>
</head>
<body>
    <?php if (!empty($retours)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID Retour</th>
                    <th>Détails Retour</th>
                    <th>CRM Associé</th>
                    <th>Période</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($retours as $retour): ?>
                    <tr>
                        <td><?= htmlspecialchars($retour['idRetour']) ?></td>
                        <td><?= htmlspecialchars($retour['nomTypeCRM'] ?? '') ?></td>
                        <td>
                            <div class="form-group">
                                <label>CRM</label>
                                <select name="idCRM" form="form-<?= $retour['idRetour'] ?>" required>
                                    <option value="">-- Sélectionner --</option>
                                    <?php foreach ($crms as $crm): ?>
                                        <option value="<?= $crm['idCRM'] ?>">
                                            <?= htmlspecialchars($crm['NomCRM']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <label>Mois</label>
                                <select name="periode_id" form="form-<?= $retour['idRetour'] ?>" required>
                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?= $i ?>" <?= (date('n') == $i) ? 'selected' : '' ?>>
                                            Mois <?= $i ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </td>
                        <td>
                            <form method="post" action="/associate-crm" id="form-<?= $retour['idRetour'] ?>">
                                <input type="hidden" name="idRetour" value="<?= $retour['idRetour'] ?>">
                                <button type="submit">Associer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun retour client trouvé.</p>
    <?php endif; ?>

    <script>
        document.querySelectorAll('form[id^="form-"]').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(form);
                
                try {
                    const response = await fetch('/associate-crm', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        alert('CRM associé avec succès pour le mois ' + formData.get('periode_id'));
                        location.reload();
                    } else {
                        alert('Erreur: ' + result.message);
                    }
                } catch (error) {
                    alert('Erreur réseau');
                }
            });
        });
    </script>
</body>
</html>