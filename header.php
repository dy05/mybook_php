<?php

require_once "functions/app.php";

?>
<div class="w-full">
    <h1 class="w-full xl:w-7/12 text-xl text-gray-800 font-bold mb-4 bg-white rounded-sm shadow-md px-4 py-2">
        <?= $page ??= 'Accueil'; ?>
    </h1>
</div>

<div class="w-full xl:w-1/4">
    <div class="w-full bg-white rounded-sm shadow-md pt-4 pb-8 px-4 mb-4">
        <ul class="space-y-2">
            <li>
                <a class="<?= $page == 'Accueil' ? 'active text-gray-800' : 'text-gray-400'; ?>" href="index.php">Accueil</a>
            </li>
            <?php if(isset($_SESSION['auth_user'])): ?>
            <li>
                <a class="<?= $page == 'Liste des livres' ? 'active text-gray-800' : 'text-gray-400'; ?>" href="books_list.php">Liste des livres</a>
            </li>
            <li>
                <a class="<?= $page == 'Ajouter un livre' ? 'active text-gray-800' : 'text-gray-400'; ?>" href="book_add.php">Ajouter un livre</a>
            </li>
            <li>
                <a class="text-gray-400" href="logout.php">Se deconnecter</a>
            </li>
            <?php else: ?>
            <li>
                <a class="<?= $page == 'Connexion' ? 'active text-gray-800' : 'text-gray-400'; ?>" href="login.php">Se connecter</a>
            </li>
            <li>
                <a class="<?= $page == 'Inscription' ? 'active text-gray-800' : 'text-gray-400'; ?>" href="register.php">S'inscrire</a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</div>

