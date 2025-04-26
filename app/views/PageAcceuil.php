<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de Gestion du Budget</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 50px;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .button-container a {
            text-decoration: none;
            background-color: #4CAF50;
            color: white;
            padding: 15px 25px;
            font-size: 18px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .button-container a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <h1>Bienvenue dans le système de gestion du Budget</h1>
    <div class="button-container">
        <!-- Lien vers la page Voir Budget -->
        <a href="/budget">Voir Budget</a>

        <!-- Lien vers la page CRUD Budget -->
        <a href="/budgetList">CRUD Budget</a>

        <!-- Lien vers la page Valider Budget -->
        <a href="/validation">Valider Budget</a>
    </div>

</body>
</html>
