<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $page = '404 - Page Not Found'; ?></title>

    <!-- Styles -->
    <link rel="stylesheet" href="css/app.css">
</head>
<body class="font-sans px-4 pt-4 pb-8 flex bg-gray-100 text-gray-600">
<div class="container m-auto">
    <div class="flex flex-wrap xl:justify-between">
        <!--?php include_once "header.php"; ?-->

        <div class="w-full">
            <p class="bg-red-500 text-white rounded-sm shadow-lg px-4 py-3">
                404 - Page non disponible.
                <br/>
                <a href="index.php" class="font-bold">Revenir a l'accueil</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>
