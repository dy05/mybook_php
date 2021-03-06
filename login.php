<?php
include "functions/app.php";
redirectForAuth();

$errors = [];
$values = [];
if(isset($_POST) && !empty($_POST)) {
    $datas = array_map('trim', $_POST);
    $datas = array_map('htmlentities', $datas);
    $datas = array_map('strip_tags', $datas);
    $datas = array_map('stripslashes', $datas);
    $datas = array_map('htmlspecialchars', $datas);

    if (!isset($datas['email']) || !isset($datas['password'])) {
        $errors['action'] = 'Veuillez remplir tous les champs';
    }

    if (empty($errors)) {
        $values['email'] = $datas['email'];
        $query = getDatabase()->prepare('SELECT * FROM users WHERE email_user = ?');
        $query->execute([$values['email']]);
        if($user = $query->fetch()) {
            if (password_verify($datas['password'], $user->password_user)) {
                $_SESSION['auth_user'] = $user;
                header('Location: index.php');
            }
        }
        $errors['action'] = 'Ces informations d\'identification ne correspondent pas à nos enregistrements';
    }

    if (empty($errors)) {
        $errors['action'] = 'Une erreur innatendue s\'est produite';
    }
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Connexion</title>

    <!-- Styles -->
    <link rel="stylesheet" href="css/app.css">
</head>
<body class="font-sans xl:px-32 pt-4 pb-8 flex">
<div class="bg-gray-100 shadow-lg rounded-lg container flex h-auto m-auto">
    <div class="hidden xl:block relative md:pl-2 pt-4 md:w-1/3 h-auto">
        <span class="bg-indigo-600 h-16 w-16 block transform rotate-45 ml-1/3"></span>
        <span class="bg-indigo-900 h-4 w-4 block transform rotate-45 absolute right-16 top-16"></span>
        <span class="bg-yellow-400 h-6 w-6 block transform rotate-45 absolute left-16 bottom-8 z-10"></span>
        <span class="bg-yellow-400 w-80 h-80 block rounded-full -mt-4 mb-48"></span>
        <span class="bg-indigo-600 w-16 h-16 block rounded-full absolute bottom-0 -mb-4 right-16"></span>
        <img src="img/font-1.png" class="mt-8 absolute left-8 w-72 top-24" alt="">
    </div>

    <div class="w-full xl:w-2/3 bg-white p-8 xl:pr-16 xl:pl-32">
        <h1 class="text-right text-sm text-gray-400 font-semibold mb-8">Connexion projet cs2i 3</h1>
        <div class="w-full xl:w-11/12 px-1">
            <h1 class="text-4xl leading-tight font-bold text-gray-500 mb-4">Bienvenu sur MyBook</h1>
            <p class="text-sm font-bold text-gray-400 mb-8">
                Rejoindre la communaute de plusieurs createurs de livre. Faire jouer votre inspiration.
            </p>

            <div class="w-full">
                <form action="" method="POST">
                    <?php if (isset($errors['action'])): ?>
                        <div class="bg-red-700 text-white rounded-md mb-8 px-4 py-2">
                            <?= $errors['action']; ?>
                        </div>
                    <?php endif; ?>

                    <div class="mb-4 relative shadow-lg w-full bg-white">
                        <label for="email" class="uppercase text-xs text-gray-300 absolute top-1 pl-5">Email</label>
                        <input class="px-4 py-5 w-full text-gray-500 focus:outline-none text-sm border-transparent border-l-4 focus:border-indigo-500 <?= isset($errors['email'])? 'border-red-400' : '';?>" type="email" id="email" name="email" value="<?= $values['email'] ?? ''; ?>" required>
                    </div>
                    <div class="mb-4 relative shadow-lg w-full bg-white">
                        <label for="password" class="uppercase text-xs text-gray-300 absolute top-1 pl-5">Password</label>
                        <input class="px-4 py-5 w-full text-gray-500 focus:outline-none text-sm border-transparent border-l-4 focus:border-indigo-500" type="password" id="password" name="password" required>
                        <a href="#" class="absolute right-8 top-0 text-gray-400 text-sm font-bold">Oublier</a>
                    </div>

                    <div class="mt-16 flex">
                        <button class="bg-indigo-500 hover:bg-indigo-900 text-white px-16 py-4 rounded-3xl uppercase font-bold mx-auto" type="submit">
                            Connexion
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="mt-16 flex float-right">
            <a href="register.php" class="flex font-semibold uppercase text-gray-500">
                Creer compte
                <img src="img/icon-logout.png" class="ml-4 w-6 h-6"/>
            </a>
        </div>
    </div>
</div>
</body>
</html>
