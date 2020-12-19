<?php

include "functions/app.php";

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $page = 'Accueil'; ?></title>

    <!-- Styles -->
    <link rel="stylesheet" href="css/app.css">
</head>
<body class="font-sans px-4 pt-4 pb-8 flex bg-gray-100 text-gray-600">
<div class="container m-auto">
    <div class="flex flex-wrap xl:justify-between">
        <?php include_once "header.php"; ?>

        <div class="w-full md:w-8/12 bg-white rounded-sm shadow-lg px-4 py-3">
            <p>
                Pas de contenu disponible pour le moment.
            </p>
        </div>
    </div>
</div>
</body>
</html>
