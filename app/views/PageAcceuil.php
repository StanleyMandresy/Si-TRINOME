<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de Gestion du Budget</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Custom color palette for Tailwind */
        .bg-custom-light-yellow { background-color: #FFF085; }
        .bg-custom-cheerful-orange { background-color: #FCB454; }
        .bg-custom-bright-orange { background-color: #FF9B17; }
        .bg-custom-reddish-orange { background-color: #F16767; }
        
        .text-custom-bright-orange { color: #FF9B17; }
        .text-custom-reddish-orange { color: #F16767; }

        .hover-bg-custom-reddish-orange:hover { background-color: #F16767; }
    </style>
</head>
<body class="bg-custom-light-yellow min-h-screen flex flex-col items-center justify-center p-4 font-sans">

    <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-800 mb-12 text-center leading-tight">
        Cindy - Fitahiana - Stanley <span class="text-custom-bright-orange"></span>
    </h1>

    <div class="flex flex-col sm:flex-row justify-center items-center gap-6 w-full max-w-3xl">

        <a href="/budget" 
           class="flex flex-col items-center justify-center p-6 bg-custom-cheerful-orange text-white font-semibold text-lg rounded-xl shadow-lg 
                  transition duration-300 ease-in-out transform hover:scale-105 hover-bg-custom-reddish-orange w-full sm:w-auto flex-1 
                  min-h-[150px] text-center"
           onclick="showBudgetInfo(event)">
            <i class="fas fa-chart-line fa-3x mb-3 text-white"></i>
            <span>Voir Budget</span>
        </a>

        <a href="/budgetList" 
           class="flex flex-col items-center justify-center p-6 bg-custom-cheerful-orange text-white font-semibold text-lg rounded-xl shadow-lg 
                  transition duration-300 ease-in-out transform hover:scale-105 hover-bg-custom-reddish-orange w-full sm:w-auto flex-1
                  min-h-[150px] text-center"
           onclick="showCrudInfo(event)">
            <i class="fas fa-cogs fa-3x mb-3 text-white"></i>
            <span>Gérer le Budget (CRUD)</span>
        </a>

        <a href="/validation" 
           class="flex flex-col items-center justify-center p-6 bg-custom-cheerful-orange text-white font-semibold text-lg rounded-xl shadow-lg 
                  transition duration-300 ease-in-out transform hover:scale-105 hover-bg-custom-reddish-orange w-full sm:w-auto flex-1
                  min-h-[150px] text-center"
           onclick="showValidationInfo(event)">
            <i class="fas fa-check-circle fa-3x mb-3 text-white"></i>
            <span>Valider Budget</span>
        </a>
    </div>

    <script>
        
    </script>

</body>
</html>