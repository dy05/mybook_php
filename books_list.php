<?php
include "functions/app.php";
redirectForAuth(false);

$books = getDatabase()->query('SELECT * FROM books 
    left join users ON books.id_author_book = users.id_user 
    left join editions ON books.id_edition_book = editions.id_edition
    ')->fetchAll();

$message = null;
if (isset($_SESSION['delete'])) {
    $message = 'Livre supprime avec succes';
    unset($_SESSION['delete']);
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $page = 'Liste des livres'; ?></title>

    <!-- Styles -->
    <link rel="stylesheet" href="css/app.css">
</head>
<body class="font-sans px-4 pt-4 pb-8 flex bg-gray-100 text-gray-600">
<div class="container m-auto">
    <div class="flex flex-wrap xl:justify-between">
        <?php include_once "header.php"; ?>

        <?php if($message): ?>
        <div class="absolute top-2 right-2 p-3 text-md text-white bg-green-400 rounded-md leading-2" id="alertDelete">
            <?= $message; ?>
        </div>
        <?php endif; ?>

        <div class="w-full md:w-8/12 flex flex-col">
            <div class="overflow-x-auto">
                <div class="align-middle inline-block min-w-full">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-md">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Titre
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nom de l'auteur
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Edition
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Annee
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach($books as $book): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full" src="<?= getCover($book->couverture_book); ?>" alt="">
                                        </div>
                                        <div class="ml-4 text-sm font-medium text-gray-900 capitalize">
                                            <?= $book->name_book; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 capitalize">
                                        <?= $book->name_user; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 capitalize">
                                        <?= $book->name_edition; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <?= $book->edited_at; ?>
                                    </span>
                                </td>
                                <td class="px-2 py-2 whitespace-nowraps text-right text-sm font-medium space-x-1 space-y-1 flex flex-col">
                                    <a href="<?= getFile($book->filename); ?>" class="text-white text-center px-2 py-1 rounded-md bg-green-500 hover:bg-green-700">Telecharger</a>
                                    <a href="book_edit.php?id=<?= $book->id_book; ?>" class="text-white text-center px-2 py-1 rounded-md bg-indigo-600 hover:bg-indigo-800">Modifier</a>
                                    <a href="book_delete.php?id=<?= $book->id_book; ?>" onclick="return confirm('Voulez vous vraiment supprimer ce livre ?')" class="text-white text-center px-2 py-1 rounded-md bg-red-600 hover:bg-red-800">Supprimer</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>

                            <!-- More rows... -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        let $alertDelete = document.getElementById('alertDelete');
        if ($alertDelete) {
            setTimeout(function() {
                $alertDelete.remove();
            }, 3000);
        }
    }
</script>
</body>
</html>
