<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insertion Utilisateur</title>
</head>
<body>
    <h2>Formulaire d'Insertion Utilisateur</h2>

    <!-- Le formulaire d'insertion -->
    <form action="/addUser" method="POST">
        <div>
            <label for="Nom">Nom:</label>
            <input type="text" id="Nom" name="Nom" required>
        </div>

        <div>
            <label for="MotDepasse">Mot de Passe:</label>
            <input type="password" id="MotDepasse" name="MotDepasse" required>
        </div>

        <div>
            <label for="Genre">Genre:</label>
            <select id="Genre" name="Genre" required>
                <option value="H">Homme</option>
                <option value="F">Femme</option>
            
            </select>
        </div>


        <div>
            <label for="idDepartement">Département:</label>
            <select name="idDepartement" id="idDepartement">
            <?php foreach($dept as $d) { ?>
                <option value="<?php echo $d['idDepartement']; ?>"> <?php echo $d['NomDepartement'];  ?></option>
          
            <?php } ?>
            </select>
        </div>

        <div>
            <button type="submit">Insérer</button>
        </div>

        <!-- Affichage du message d'erreur s'il y en a -->
        <?php if(isset($message)): ?>
            <p style="color:red;"><?php echo $message; ?></p>
        <?php endif; ?>
    </form>
</body>
</html>
