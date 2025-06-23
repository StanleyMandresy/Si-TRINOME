<!DOCTYPE html> 
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter/Modifier un Budget</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'yellow-custom': '#FFF085',
                        'orange-light': '#FCB454',
                        'orange-dark': '#FF9B17',
                        'red-custom': '#F16767'
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #FFF085 0%, #FCB454 35%, #FF9B17 70%, #F16767 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="p-8">
    <div class="max-w-2xl mx-auto">
        <!-- Conteneur principal avec effet glassmorphism -->
        <div class="bg-white/20 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/30 p-8 mb-8">
            <h2 class="text-4xl font-bold text-white mb-8 text-center drop-shadow-lg">
                Ajouter un Budget
            </h2>
            
            <form action="/addbudget" method="POST" class="space-y-6">
                <!-- Catégorie -->
                <div class="group">
                    <label for="idCategorie" class="block text-white font-semibold mb-2 text-lg drop-shadow">
                        Catégorie :
                    </label>
                    <select id="idCategorie" name="idCategorie" required 
                            class="w-full px-4 py-3 rounded-xl bg-white/90 backdrop-blur border-2 border-white/50 
                                   focus:border-orange-light focus:ring-4 focus:ring-orange-light/30 
                                   transition-all duration-300 text-gray-800 font-medium
                                   hover:bg-white/95 hover:shadow-lg">
                        <option value="">Sélectionnez une catégorie</option>
                        <?php foreach ($data['categories'] as $c) : ?>
                            <option value="<?= $c['idCategorie'] ?>"><?= htmlspecialchars($c['NomCategorie']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Période -->
                <div class="group">
                    <label for="idPeriode" class="block text-white font-semibold mb-2 text-lg drop-shadow">
                        Période :
                    </label>
                    <select id="idPeriode" name="idPeriode" required 
                            class="w-full px-4 py-3 rounded-xl bg-white/90 backdrop-blur border-2 border-white/50 
                                   focus:border-orange-light focus:ring-4 focus:ring-orange-light/30 
                                   transition-all duration-300 text-gray-800 font-medium
                                   hover:bg-white/95 hover:shadow-lg">
                        <option value="">Sélectionnez une période</option>
                        <?php 
                        $mois = [
                            1 => "Janvier", 2 => "Février", 3 => "Mars", 4 => "Avril",
                            5 => "Mai", 6 => "Juin", 7 => "Juillet", 8 => "Août",
                            9 => "Septembre", 10 => "Octobre", 11 => "Novembre", 12 => "Décembre"
                        ];
                        
                        foreach ($mois as $id => $nom) {
                            echo "<option value='$id'>$nom</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Grille pour Prévision et Réalisation -->
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Prévision -->
                    <div class="group">
                        <label for="prevision" class="block text-white font-semibold mb-2 text-lg drop-shadow">
                            Prévision :
                        </label>
                        <input type="number" id="prevision" name="prevision" step="0.01" required 
                               class="w-full px-4 py-3 rounded-xl bg-white/90 backdrop-blur border-2 border-white/50 
                                      focus:border-orange-dark focus:ring-4 focus:ring-orange-dark/30 
                                      transition-all duration-300 text-gray-800 font-medium
                                      hover:bg-white/95 hover:shadow-lg">
                    </div>

                    <!-- Réalisation -->
                    <div class="group">
                        <label for="realisation" class="block text-white font-semibold mb-2 text-lg drop-shadow">
                            Réalisation :
                        </label>
                        <input type="number" id="realisation" name="realisation" step="0.01" 
                               class="w-full px-4 py-3 rounded-xl bg-white/90 backdrop-blur border-2 border-white/50 
                                      focus:border-orange-dark focus:ring-4 focus:ring-orange-dark/30 
                                      transition-all duration-300 text-gray-800 font-medium
                                      hover:bg-white/95 hover:shadow-lg">
                    </div>
                </div>

                <!-- Date du Budget -->
                <div class="group">
                    <label for="dateBudget" class="block text-white font-semibold mb-2 text-lg drop-shadow">
                        Date du Budget :
                    </label>
                    <input type="datetime-local" id="dateBudget" name="dateBudget" 
                           value="<?= date('Y-m-d\TH:i') ?>" required 
                           class="w-full px-4 py-3 rounded-xl bg-white/90 backdrop-blur border-2 border-white/50 
                                  focus:border-red-custom focus:ring-4 focus:ring-red-custom/30 
                                  transition-all duration-300 text-gray-800 font-medium
                                  hover:bg-white/95 hover:shadow-lg">
                </div>

                <!-- Bouton Submit -->
                <div class="pt-4">
                    <button type="submit" name="save" value="Ajouter" 
                            class="w-full bg-gradient-to-r from-orange-light via-orange-dark to-red-custom 
                                   text-white font-bold py-4 px-8 rounded-xl shadow-xl
                                   hover:shadow-2xl hover:scale-105 
                                   active:scale-95 transition-all duration-300
                                   focus:ring-4 focus:ring-orange-light/50 focus:outline-none
                                   text-xl tracking-wide">
                        ✨ Ajouter le Budget
                    </button>
                </div>
            </form>
        </div>

        <!-- Effet décoratif flottant -->
        <div class="fixed top-10 left-10 w-20 h-20 bg-yellow-custom/30 rounded-full blur-xl animate-pulse"></div>
        <div class="fixed bottom-10 right-10 w-32 h-32 bg-red-custom/20 rounded-full blur-2xl animate-pulse"></div>
        <div class="fixed top-1/2 right-20 w-16 h-16 bg-orange-light/25 rounded-full blur-lg animate-pulse"></div>
    </div>
</body>
</html>