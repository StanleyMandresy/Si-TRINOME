<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page avec boutons</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
            background-color: #f4f4f9;
        }

        .button {
            display: inline-block;
            padding: 20px 40px;
            font-size: 24px;
            margin: 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #45a049;
        }

        .button:active {
            background-color: #388e3c;
        }
    </style>
</head>
<body>
    <h1>Bienvenue sur la page avec des boutons</h1>
    <div>
        <a href="categorie" class="button">Catégorie</a>
        <a href="dept" class="button">Département</a>
        <a href="listUser" class="button">Utilisateur</a>
    </div>
</body>
</html>
