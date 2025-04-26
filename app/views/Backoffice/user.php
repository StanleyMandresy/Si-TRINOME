<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des utilisateurs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 30px;
        }
        .table th, .table td {
            text-align: center;
        }
    </style>
</head>
<body>


    <div class="container">
        <h2 class="text-center mb-4">Liste des utilisateurs</h2>
        <button type="button" value=""><a href="addUser">Ajouter</a></button>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Genre</th>
                    <th>Position</th>
                    <th>Département</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($User as $user): ?>
                <tr>
                    <td><?php echo $user['idUser']; ?></td>
                    <td><?php echo $user['Nom']; ?></td>
                    <td><?php echo $user['Genre']; ?></td>
                    <td><?php echo $user['Position']; ?></td>
                    <td><?php echo $user['NomDepartement']; ?></td> <!-- Assurez-vous que le nom du département est inclus dans la réponse -->
                   
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
