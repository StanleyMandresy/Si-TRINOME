<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Custom color palette */
        .bg-custom-red-light { background-color: #FFDCDC; }
        .bg-custom-white-peach { background-color: #FFF2EB; }
        .bg-custom-peach-light { background-color: #FFE8CD; }
        .bg-custom-peach-dark { background-color: #FFD6BA; }
        .text-custom-red-light { color: #FFDCDC; }
    </style>
</head>
<body class="bg-custom-white-peach min-h-screen flex items-center justify-center p-4">
    <div class="bg-custom-peach-light p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-3xl font-bold text-center mb-6 text-gray-800">Voir les budgets, connectez-vous</h2>
        <form action="/" method="post" class="space-y-6">
            <div>
                <label for="Nom" class="block text-sm font-medium text-gray-700 mb-1">Nom :</label>
                <input type="text" id="Nom" name="Nom" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-custom-red-light focus:border-custom-red-light sm:text-sm bg-custom-white-peach text-gray-900">
            </div>
            <div>
                <label for="MotDepasse" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe :</label>
                <input type="password" id="MotDepasse" name="MotDepasse" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-custom-red-light focus:border-custom-red-light sm:text-sm bg-custom-white-peach text-gray-900">
            </div>
            <div>
                <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-lg font-medium text-white bg-custom-peach-dark hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-peach-dark transition duration-300 ease-in-out">
                    Entrer
                </button>
            </div>
        </form>
    </div>
</body>
</html>