<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Budgets</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        pastel: {
                            yellow: {
                                50: '#FFFDF5',
                                100: '#FFFCEB',
                                200: '#FFF8D6',
                                300: '#FFF4C2',
                                400: '#FFEC99',
                                500: '#FFE470',
                                600: '#E6CD65',
                                700: '#998A43',
                                800: '#736732',
                                900: '#4D4521',
                            },
                            orange: {
                                100: '#FFE8D9',
                                200: '#FFD1B3',
                                300: '#FFBA8D',
                                400: '#FF8D42',
                                500: '#FF5F00',
                            }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .positive {
            color: #10B981; /* vert pastel */
        }
        .negative {
            color: #EF4444; /* rouge pastel */
        }
    </style>
</head>
<body class="bg-pastel-yellow-50 min-h-screen font-sans">

    <!-- Conteneur principal avec sidebar et contenu -->
    <div class="flex flex-col min-h-screen">
        <!-- Sidebar et contenu en ligne -->
        <div class="flex flex-1">
            <!-- Sidebar à gauche -->
           <?php include('Head.php'); ?>
            
            <!-- Contenu principal à droite -->
            <div class="flex-1 p-8 overflow-auto">
                <div class="max-w-6xl mx-auto">
                    <div class="flex justify-between items-center mb-8">
                        <h2 class="text-3xl font-bold text-pastel-yellow-900">Liste des Budgets</h2>
                        <form action="addbudget" method="get">
                            <button name="button" value="ajouter" class="bg-pastel-orange-400 hover:bg-pastel-orange-500 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                Ajouter budget
                            </button>
                        </form>
                    </div>

                    <?php if (!empty($budgets)) : ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-pastel-yellow-100">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-pastel-yellow-800 uppercase tracking-wider">Département</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-pastel-yellow-800 uppercase tracking-wider">Catégorie</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-pastel-yellow-800 uppercase tracking-wider">Prévision</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-pastel-yellow-800 uppercase tracking-wider">Réalisation</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-pastel-yellow-800 uppercase tracking-wider">Écart</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-pastel-yellow-800 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-pastel-yellow-800 uppercase tracking-wider">Modifier</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-pastel-yellow-800 uppercase tracking-wider">Supprimer</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($budgets as $budgets) : ?>
                                        <tr class="hover:bg-pastel-yellow-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($budgets['nomDepartement']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($budgets['nomCategorie']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= number_format($budgets['Prevision'], 2, ',', ' ') ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= number_format($budgets['Realisation'], 2, ',', ' ') ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm <?= ($budgets['Ecart'] >= 0) ? 'positive' : 'negative' ?>">
                                                <?= number_format($budgets['Ecart'], 2, ',', ' ') ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= date('d/m/Y H:i', strtotime($budgets['DateBudget'])) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <form action="addmodifier" method="get">
                                                    <input type="hidden" name="idBudget" value="<?= $budgets['idBudget'] ?>" >
                                                    <button type="submit" class="text-pastel-orange-500 hover:text-pastel-orange-700">
                                                        Modifier
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <form action="deletebudget" method="get">
                                                    <input type="hidden" name="idBudget" value="<?= $budgets['idBudget'] ?>" >
                                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                                        Supprimer
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <div class="bg-white p-6 rounded-lg shadow-md text-center">
                            <p class="text-gray-600">Aucun budget trouvé.</p>
                        </div>
                    <?php endif; ?>

                    <div class="mt-8 space-y-4">
                        <!-- Importation CSV -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <form action="csv" method="post" enctype="multipart/form-data">
                                <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">Sélectionner un fichier CSV pour l'importation :</label>
                                <div class="flex space-x-4">
                                    <input type="file" name="csv_file" required class="border border-gray-300 rounded-md px-3 py-2 flex-1">
                                    <button type="submit" name="csv" value="import" class="bg-pastel-orange-400 hover:bg-pastel-orange-500 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                        Importer
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Exportation CSV -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <form action="csv" method="post">
                                <button type="submit" name="csv" value="export" class="bg-pastel-orange-400 hover:bg-pastel-orange-500 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                    Exporter les données
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>