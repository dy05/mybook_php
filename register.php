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

    if (!isset($datas['email'])) {
        $errors['email'] = 'Le champ email est obligatoire';
    } else {
        $values['email'] = $datas['email'];
        if (filter_var($values['email'], FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = 'L\'adresse email est incorrect';
        }
    }

    if (!isset($datas['password']) || !isset($datas['password_confirmation'])) {
        $errors['password'] = 'Les champs mot de passe sont obligatoires';
    } elseif ($datas['password'] != $datas['password_confirmation']) {
        $errors['password'] = 'Les mots de passe ne correspondent pas';
    } elseif (strlen($datas['password']) < 4) {
        $errors['password'] = 'Le mot de passe doit contenir au moins 4 caracteres';
    }

    if (empty($errors)) {
        $query = getDatabase()->prepare('SELECT * FROM users WHERE email_user = ?');
        $query->execute([$values['email']]);
        if($result = $query->fetch()) {
            $errors['action'] = 'L\'adresse email n\'est pas disponible';
        }

        if (empty($errors)) {
            $query = getDatabase()->prepare('SELECT * FROM roles');
            $role = null;
            if (count($query->fetchAll()) < 3) {
                // if user inscription
                $role_name = 'user';
                // if admin inscription
//                $role_name = 'admin';
                // if author inscription
//                $role_name = 'author';

                $role = getRole($role_name);
            }

            $password = password_hash($datas['password'], PASSWORD_BCRYPT);
            $newQuery = getDatabase()->prepare("INSERT INTO users (email_user, password_user, id_role_user) 
                    VALUES (:email_user, :password_user, :id_role_user)");
//            $newQuery = getDatabase()->prepare("INSERT INTO users (name_user, surname_user, email_user, password_user, id_role_user)
//                    VALUES (:name_user, :surname_user, :email_user, :password_user, :id_role_user)");
//            $newQuery->bindParam(':name_user', $datas['name']);
//            $newQuery->bindParam(':surname_user', $datas['surname']);
            $newQuery->bindParam(':email_user', $datas['email']);
            $newQuery->bindParam(':password_user', $password);
            $newQuery->bindParam(':id_role_user', $role->id_role);
            if($newQuery->execute()) {
                $values['success'] = 'Inscription valide avec succes';
            } else {
                $errors['action'] = 'Une erreur innatendue s\'est produite';
            }
        }
    }
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Inscription</title>

    <!-- Styles -->
    <link rel="stylesheet" href="css/app.css">
</head>
<body class="font-sans xl:px-32 pt-4 pb-8 flex">
<div class="bg-gray-100 shadow-lg rounded-lg container xl:flex xl:flex-row-reverse h-auto m-auto">
    <div class="w-full xl:w-1/3 bg-white pb-16">
        <img src="img/plants.png" class="float-right h-24" alt="">
        <div class="w-full mt-32 px-1 flex flex-col items-center">
            <img src="img/head.png" alt="">
            <h1 class="text-4xl font-bold uppercase text-gray-700 mb-4">MyBook</h1>

            <div class="w-full mt-8 px-8">
                <?php if (isset($values['success'])): ?>
                    <div class="bg-green-500 text-white rounded-md mb-8 px-4 py-2">
                        <?= $values['success']; ?>
                    </div>
                <?php endif; ?>


                <form action="" method="POST">
                    <div class="relative shadow-lg w-full bg-white rounded-full overflow-hidden">
                        <label for="email" class="hidden">Email</label>
                        <input class="rounded-full px-8 py-4 w-full text-gray-800 text-sm placeholder-gray-500 border border-transparent focus:border-indigo-500 <?= isset($errors['email'])? 'border-red-400' : '';?>"
                               placeholder="Email" type="email" id="email" name="email" value="<?= $values['email'] ?? ''; ?>">
                        <span class="absolute h-5 w-5 bottom-0 top-0 my-auto left-2 flex items-center">
                            <img src="img/icon-message.png" alt="">
                        </span>
                    </div>
                    <div class="mt-8 relative shadow-lg w-full bg-white rounded-full overflow-hidden">
                        <label for="password" class="hidden">Password</label>
                        <input class="rounded-full px-8 py-4 w-full text-gray-800 text-sm placeholder-gray-500 border border-transparent focus:border-indigo-500 <?= isset($errors['email'])? 'border-red-400' : '';?>"
                               placeholder="Password" type="password" id="password" name="password" required>
                        <span class="absolute h-5 w-5 bottom-0 top-0 my-auto left-2 flex items-center">
                            <img src="img/icon-lock.png" alt="">
                        </span>
                    </div>
                    <div class="mt-8 relative shadow-lg w-full bg-white rounded-full overflow-hidden">
                        <label for="password_confirmation" class="hidden">Confirm Password</label>
                        <input class="rounded-full px-8 py-4 w-full text-gray-800 text-sm placeholder-gray-500 border border-transparent focus:border-indigo-500"
                               placeholder="Retype Password" type="password" id="password_confirmation" name="password_confirmation" required>
                        <span class="absolute h-5 w-5 bottom-0 top-0 my-auto left-2 flex items-center">
                            <img src="img/icon-lock.png" alt="">
                        </span>
                    </div>
                    <?php if(!empty($errors)):?>
                        <div class="bg-red-700 text-white rounded-md mt-4 px-4 py-2">
                            <ul class="space-y-1">
                                <?php if (isset($errors['email'])): ?>
                                <li>
                                    <?= $errors['email'] ?? ''; ?>
                                </li>
                                <?php endif; ?>
                                <?php if (isset($errors['password'])): ?>
                                <li>
                                    <?= $errors['password'] ?? ''; ?>
                                </li>
                                <?php endif; ?>
                                <?php if (isset($errors['action'])): ?>
                                <li>
                                    <?= $errors['action'] ?? ''; ?>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>


                    <div class="mt-16 w-full flex">
                        <button class="bg-blue-500 hover:bg-blue-700 text-white w-full py-2 rounded-3xl uppercase font-bold mx-auto" type="submit">
                            Inscription
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="xl:w-2/3 h-auto bg-white text-gray-600 xl:bg-blue-300 xl:px-16">
        <div class="relative w-full h-full xl:flex xl:flex-row xl:items-end pb-4">
            <img src="img/home.png" class="hidden xl:block absolute h-full w-full object-center" alt="">
            <img src="img/human.png" class="hidden xl:block absolute h-96 object-cover left-32" alt=""/>
            <div class="w-full px-8 xl:w-128 xl:h-64 pt-4 py-8 xl:text-white relative border-t-2 border-gray-200 xl:border-transparent mb-4">
                <h1 class="font-bold text-4xl leading-tight">Bienvenu sur MyBook</h1>
                <p class="text-sm mt-4 font-semibold">
                    Rejoindre la communaute de plusieurs createur de livre. Faire votre inspiration.
                </p>
                <div class="mt-8">
                    <a href="login.php" class="px-8 py-2 rounded-3xl uppercase font-bold border-2 xl:border-white bg-transparent hover:border-transparent hover:bg-blue-500 hover:text-white">
                    Connexion
                </a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
