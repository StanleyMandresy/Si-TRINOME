<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Évaluer le ticket</title>
    <style>
        .stars {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
        }
        .stars input[type="radio"] {
            display: none;
        }
        .stars label {
            font-size: 2rem;
            color: lightgray;
            cursor: pointer;
            transition: color 0.2s;
        }
        .stars input[type="radio"]:checked ~ label,
        .stars label:hover,
        .stars label:hover ~ label {
            color: gold;
        }
        form {
            text-align: center;
            max-width: 400px;
            margin: 30px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
        textarea {
            width: 100%;
            height: 100px;
            margin-top: 10px;
        }
        button {
            margin-top: 15px;
            padding: 8px 15px;
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">Évaluer le ticket</h2>

<form method="POST" action="/Ticket/evaluer-ticket">
    <input type="hidden" name="id_ticket" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">

    <div class="stars">
        <?php for ($i = 5; $i >= 1; $i--): ?>
            <input type="radio" id="star<?= $i ?>" name="note" value="<?= $i ?>" required>
            <label for="star<?= $i ?>">★</label>
        <?php endfor; ?>
    </div>

    <label for="commentaire">Commentaire :</label><br>
    <textarea name="commentaire" id="commentaire" placeholder="Votre avis..." required></textarea><br>

    <button type="submit">Envoyer l’évaluation</button>
</form>

</body>
</html>
