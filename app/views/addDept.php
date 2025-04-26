<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Catégorie</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
        }
        form {
            width: 300px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        label {
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

    <h2>Ajouter une Departement</h2>

    <form action="dept" method="POST">
        <label for="NumeroDepartement">NumeroDepartement :</label>
        <input type="number" id="NumeroDepartement" name="NumDept" >

        <label for="nomCategorie">Nom de Departement:</label>
        <input type="text" name="nom" >

        <label for="responsable">Responsable:</label>
        <input type="text" name="responsable" >

    
        <button type="submit" name="save" value="<?= $button ?>">Ajouter</button>
    </form>

</body>
</html>
