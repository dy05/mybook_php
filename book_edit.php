<?php
include "functions/app.php";
redirectForAuth(false);
if (!isset($_GET['id']) || (isset($_GET['id']) && !filter_var($_GET['id'], FILTER_VALIDATE_INT))) {
    redirectNotFound();
}

$book = getDatabase()->query('SELECT * FROM books
                            LEFT JOIN users ON books.id_author_book = users.id_user
                            LEFT JOIN editions ON books.id_edition_book = editions.id_edition
                            WHERE books.id_book  = '.$_GET['id'])->fetch();

if (!$book) {
    redirectNotFound();
}

$errors = [];
$values = [];
if(isset($_POST) && !empty($_POST)) {
    $datas = array_map('trim', $_POST);
    $datas = array_map('htmlentities', $datas);
    $datas = array_map('strip_tags', $datas);
    $datas = array_map('stripslashes', $datas);
    $datas = array_map('htmlspecialchars', $datas);
    $fileContent = [];
    $coverContent = [];
    $dateYear = [];

    if (!isset($datas['nom'])) {
        $errors['nom'] = 'Le champ `nom du livre` est obligatoire';
    }

    if (!isset($datas['auteur'])) {
        $errors['auteur'] = 'Le champ `nom de l\'auteur` est obligatoire';
    }

    if (!isset($datas['description'])) {
        $errors['description'] = 'Le champ `description` est obligatoire';
    }

    if (!isset($datas['edition'])) {
        $errors['edition'] = 'Le champ `edition` est obligatoire';
    }

    if (!isset($datas['annee'])) {
        $errors['annee'] = 'Le champ `annee` est obligatoire';
    } elseif (isset($datas['annee']) && !empty($datas['annee'])) {
        $date = date_create_from_format('Y-m-d', $datas['annee']);
        if ($err = date_get_last_errors()) {
            if ($err['warning_count'] > 0) {
                $errors['annee'] = 'Ce champ `annee` est une date incorrecte !';
            } else {
                $dateYear['Y'] = $date->format('Y');
                $dateYear['m-Y'] = $date->format('m-Y');
            }
        }
    }

    if (isset($_FILES['file']) && isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
        $filename = $_FILES['file']['name'];
        $filename_parts = explode('.', $filename);
        $file_ext = strtolower(end($filename_parts));

        if (!in_array($file_ext, array('pdf', 'ebook', 'docx'))) {
            $errors['file'] = 'Vous devez entrez un pdf ou ebook ou document valide comme fichier livre !';
        } else {
            $fileContent['ext'] = $file_ext;
            $fileContent['file_tmp'] = $_FILES['file']['tmp_name'];
//            $fileContent['filename'] = $_FILES['file']['name'];
//            $fileContent['size'] = $_FILES['file']['size'];
        }
    }

    if (isset($_FILES['cover']) && isset($_FILES['cover']['name']) && $_FILES['cover']['name'] != '') {
        $covername = $_FILES['cover']['name'];
        $covername_parts = explode('.', $covername);
        $cover_ext = strtolower(end($covername_parts));

        if (!in_array($cover_ext, array('jpg', 'jpeg', 'png', 'gif'))) {
            $errors['cover'] = 'Vous devez entrez une image valide comme photo de couverture !';
        } else {
            $coverContent['ext'] = $cover_ext;
            $coverContent['file_tmp'] = $_FILES['cover']['tmp_name'];
//            $coverContent['filename'] = $_FILES['cover']['name'];
//            $coverContent['size'] = $_FILES['cover']['size'];
        }
    }

    if (empty($errors)) {
        $verification_required = true;
        $role = getRole('author');
        $auteur = [];
        $edition = [];

        if ($datas['auteur'] != $book->id_author_book) {
            if (isset($datas['auteur']) && filter_var($datas['auteur'], FILTER_VALIDATE_INT)) {
                $auteurQuery = getDatabase()->prepare("SELECT * FROM users WHERE id_user = ? && id_role_user = ?");
                $auteurQuery->execute([$datas['auteur'], $role->id_role]);
                $auteurRow = $auteurQuery->fetch();
                if ($auteurRow) {
                    $auteur['id'] = $auteurRow->id_user;
                    $auteur['nom'] = $auteurRow->name_user;
                } else {
//                        $errors['action'] = 'Une erreur innatendue s\'est produite';
                    $errors['auteur'] = 'L\'utilisateur selectionne n\'existe plus';
                }
            } else {
                $auteurQuery = getDatabase()->prepare("SELECT * FROM users WHERE name_user = ?");
                $auteurQuery->execute([$datas['auteur']]);
                $auteurRow = $auteurQuery->fetch();
                if ($auteurRow) {
                    $auteur['id'] = $auteurRow->id_user;
                    $auteur['nom'] = $auteurRow->name_user;
                } else {
                    $db = getDatabase();
                    $auteurQuery = $db->prepare("INSERT INTO users (name_user, id_role_user) 
                        VALUES (:name_user, :id_role_user)");
                    $auteurQuery->bindParam(':name_user', $datas['auteur']);
                    $auteurQuery->bindParam(':id_role_user', $role->id_role);
                    if ($auteurQuery->execute()) {
                        $auteur['id'] = $db->lastInsertId();
                        $auteur['nom'] = $datas['auteur'];
                        $verification_required = false;
                    } else {
                        $errors['action'] = 'Une erreur innatendue s\'est produite';
                    }
                }
            }
        } else {
            $verification_required = false;
            $auteur['nom'] = $book->name_user;
            $auteur['id'] = $book->id_author_book;
        }

        if ($datas['edition'] != $book->id_edition_book) {
            if (isset($datas['edition']) && filter_var($datas['edition'], FILTER_VALIDATE_INT)) {
                $editionQuery = getDatabase()->prepare("SELECT * FROM editions WHERE id_edition = ?");
                $editionQuery->execute([$datas['edition']]);
                $editionRow = $editionQuery->fetch();
                if ($editionRow) {
                    $edition['id'] = $editionRow->id_edition;
                    $edition['nom'] = $editionRow->name_edition;
                } else {
//                        $errors['action'] = 'Une erreur innatendue s\'est produite';
                    $errors['edition'] = 'L\'utilisateur selectionne n\'existe plus';
                }
            } else {
                $editionQuery = getDatabase()->prepare("SELECT * FROM editions WHERE name_edition = ?");
                $editionQuery->execute([$datas['edition']]);
                $editionRow = $editionQuery->fetch();
                if ($editionRow) {
                    $edition['id'] = $editionRow->id_edition;
                    $edition['nom'] = $editionRow->name_edition;
                } else {
                    $db = getDatabase();
                    $editionQuery = $db->prepare("INSERT INTO editions (name_edition) VALUES (:name_edition)");
                    $editionQuery->bindParam(':name_edition', $datas['edition']);
                    if ($editionQuery->execute()) {
                        $edition['id'] = $db->lastInsertId();
                        $edition['nom'] = $datas['edition'];
                        $verification_required = false;
                    } else {
                        $errors['action'] = 'Une erreur innatendue s\'est produite';
                    }
                }
            }
        } else {
            $verification_required = false;
            $edition['nom'] = $book->name_edition;
            $edition['id'] = $book->id_edition_book;
        }


        if ($verification_required && empty($errors)) {
            $query = getDatabase()->prepare('SELECT * FROM books
                    WHERE edited_at = ? && name_book = ? && id_edition_book = ? && id_author_book = ?');
            $query->execute([$datas['annee'], $datas['nom'], $datas['edition'], $datas['auteur']]);
            $result = $query->fetchAll();
            if (count($result) > 0) {
                $errors['action'] = "Ce livre est deja present dans notre base de donnees !";
            }
        }

        if (empty($errors)) {
            if (!empty($fileContent)) {
                $fileName = $auteur['nom'] . ' - ' . $datas['nom'] . ' - ' . $edition['nom'] . ' - ' . $dateYear['Y'] . '.' . $fileContent['ext'];
                $fileLink = 'storage/books/' . $fileName;
                if (move_uploaded_file($fileContent['file_tmp'], $fileLink)) {
                    $datas['filename'] = $fileName;
                } else {
                    $errors['file'] = 'Une erreur innatendue s\'est produite lors de l\'upload du fichier du livre !';
                }
            }

            if (!empty($coverContent)) {
                $coverName = $auteur['nom'].' - '.$datas['nom'].' - '.$edition['nom'].' - '.$dateYear['Y'].'-cover.'.$coverContent['ext'];
                $coverLink = 'storage/covers/' . $coverName;
                if (move_uploaded_file($coverContent['file_tmp'], $coverLink)) {
                    $datas['couverture_book'] = $coverName;
                } else {
                    $errors['cover'] = 'Une erreur innatendue s\'est produite lors de l\'upload de la photo de couverture !';
    //                $errors['cover'] = 'Une erreur innatendue s\'est produite lors de l\'upload de la photo de couverture (verifier si le dossier de destination existe vraiment) !';
                }
            }

            if (empty($errors)) {
                $couverture = $datas['couverture_book'] ?? $book->couverture_book;
                $fichier = $datas['filename'] ?? $book->filename;
                $newQuery = getDatabase()->prepare('UPDATE books SET name_book = :name_book, description_book = :description_book, 
                 couverture_book = :couverture_book, filename = :file, edited_at = :annee, id_edition_book = :id_edition_book, 
                 id_author_book = :id_author_book WHERE id_book = '.$book->id_book);
                $newQuery->bindParam(':name_book', $datas['nom']);
                $newQuery->bindParam(':annee', $datas['annee']);
                $newQuery->bindParam(':description_book', $datas['description']);
                $newQuery->bindParam(':id_edition_book', $edition['id']);
                $newQuery->bindParam(':id_author_book', $auteur['id']);
                $newQuery->bindParam(':couverture_book', $couverture);
                $newQuery->bindParam(':file', $fichier);
                if($newQuery->execute()) {
                    $values['success'] = 'La modification a ete faite avec succes';
                    if (isset($datas['filename']) && $fichier != $book->filename) {
                        deleteFile($book->filename);
                    }
                    if (isset($datas['couverture_book']) && $couverture != $book->couverture_book) {
                        deleteCover($book->couverture_book);
                    }
                    $book = getDatabase()->query('SELECT * FROM books WHERE books.id_book  = '.$book->id_book)->fetch();
                } else {
                    $errors['action'] = 'Une erreur innatendue s\'est produite';
                }
            }
        }
    }
}

$editions = getEditions();
$auteurs = getUsers('author');

$values['nom'] = $book->name_book;
$values['description'] = $book->description_book;
$values['annee'] = $book->edited_at;
$values['auteur'] = $book->id_author_book;
$values['edition'] = $book->id_edition_book ;

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $page = 'Modifier un livre'; ?></title>

    <!-- Styles -->
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/app.css">
</head>
<body class="font-sans px-4 pt-4 pb-8 flex bg-gray-100 text-gray-600">
<div class="container m-auto">
    <div class="flex flex-wrap xl:justify-between">
        <?php include_once "header.php"; ?>

        <div class="w-full md:w-8/12 bg-white rounded-lg shadow-lg xl:rounded-sm container h-auto relative p-4 xl:p-8">
            <div class="flex items-center px-4 xl:px-8">
                <img src="img/book.png" class="h-8 xl:h-16" alt="">
                <h1 class="text-xl xl:text-4xl uppercase text-gray-700">MyBook</h1>
            </div>
            <div class="w-full mt-48 px-4 xl:px-8 text-gray-500">
                <form action="" method="POST" enctype="multipart/form-data">
                    <?php if (!empty($errors)): ?>
                    <div class="bg-red-500 text-white rounded-md mb-8 px-4 py-2">
                        <ul class="space-y-1">
                            <?php foreach ($errors as $error): ?>
                                <li>
                                    <?= $error; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php elseif (isset($values['success'])): ?>
                    <div class="bg-green-500 text-white rounded-md mb-8 px-4 py-2">
                        <?= $values['success'] ?? ''; ?>
                    </div>
                    <?php endif; ?>

                    <div class="pb-2">
                        <label for="nom" class="hidden">Nom du livre</label>
                        <input class="px-4 py-4 bg-gray-100 w-full text-sm placeholder-gray-500 border border-transparent focus:border-indigo-500"
                               placeholder="Nom du livre" type="text" id="nom" name="nom" value="<?= $values['nom'] ?? ''; ?>" required>
                    </div>

                    <div class="pb-2 mt-8 relative">
                        <label for="auteur" class="hidden">Nom de l'auteur</label>
                        <div class="bg-gray-100">
                            <select type="text" id="auteur" name="auteur"
                                    class="w-full bg-transparent py-4 form-multiselect border-transparent rounded-none text-opacity-0" required>
                                <option value="" <?= !isset($values['auteur']) || (isset($values['auteur']) && $values['auteur'] == '')
                                    ? 'selected' : ''; ?> disabled></option>
                                <?php foreach($auteurs as $auteur): ?>
                                    <option value="<?= $auteur->id_user; ?>"
                                        <?= isset($values['auteur']) && $values['auteur'] == $auteur->id_user ? 'selected' : ''; ?>>
                                        <?= $auteur->name_user; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="pb-2 mt-8">
                        <label for="description" class="hidden">Description du livre</label>
                        <textarea placeholder="Description du livre" name="description" id="description" maxlength="300"
                              class="px-4 py-4 bg-gray-100 w-full text-sm placeholder-gray-500" required><?= $values['description'] ?? ''; ?></textarea>
                        <span id="messageLength" class="float-right text-gray-500 text-md capitalize">
                            0/300 caracteres
                        </span>
                    </div>

                    <div class="mt-8 flex flex-col xl:flex-row">
                        <div class="mt-2 flex flex-col xl:w-1/2">
                            <label for="edition" class="text-md leading-7">
                                Edition
                            </label>
                            <div class="bg-gray-100 relative w-full">
                                <select name="edition" id="edition" class="w-full bg-transparent py-4 form-multiselect border-transparent rounded-none text-opacity-0 relative" required>
                                    <option value="" <?= !isset($values['edition']) || (isset($values['edition']) && $values['edition'] == '')
                                        ? 'selected' : ''; ?> disabled></option>
                                    <?php foreach($editions as $edition): ?>
                                    <option value="<?= $edition->id_edition; ?>"
                                        <?= isset($values['edition']) && $values['edition'] == $edition->id_edition ? 'selected' : ''; ?>>
                                        <?= $edition->name_edition; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="absolute flex items-center justify-center w-4 h-4 top-5 right-2 text-gray-400 font-sans text-xl z-0 select-none cursor-pointer">
                                    ˅
                                </span>
                            </div>
                        </div>
                        <div class="mt-2 xl:ml-2 flex flex-col xl:w-1/2">
                            <label id="annee" class="text-md leading-7">
                                Annee
                            </label>
                            <div class="bg-gray-100 relative w-full">
                                <input type="date" id="annee" name="annee" value="<?= $values['annee'] ?? ''; ?>"
                                       class="w-full border-none bg-transparent py-4 pr-4 pl-8 relative z-10 focus:boder-none" required>
                                <span class="absolute flex items-center justify-center w-4 h-full bottom-o top-0 left-2 z-0">
                                    <img src="img/icon-calendar.png" class="h-4 mx-auto" alt="">
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 relative">
                        <label for="file" class="w-full bg-gray-100 cursor-pointer flex items-center px-4 py-3 absolute">
                            <span class="mr-2">
                                <img src="img/upload.png" class="h-8" alt="">
                            </span>
                            Upload Book
                        </label>
                        <input type="file" id="file" name="file" class="bg-transparent w-full py-4">
                    </div>

                    <div class="xl:absolute absolute top-0 right-0 mt-12 xl:mt-8 mr-8 xl:mr-16">
                        <label for="cover" class="flex flex-col xl:flex-col-reverse flex-col-reverse">
                            <span class="justify-end mb-2 xl:mb-0 mb-0 xl:mt-2 mt-2">
                                Image de couverture
                            </span>
                            <span class="w-36 h-36 bg-gray-100 flex justify-center items-center cursor-pointer">
                                <img src="img/download.png" id="imgPreview" class="h-16" alt="">
                            </span>
                        </label>
                        <input class="hidden" type="file" id="cover" name="cover">
                    </div>

                    <div class="mt-16 w-full flex justify-end">
                        <button class="w-36 h-12 flex items-center justify-center px-4 py-4  bg-indigo-500 hover:bg-indigo-700 text-white rounded-md uppercase" name="publish" type="submit">
                            Modifier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="js/jquery-3.4.1.min.js"></script>
<script src="js/select2.min.js"></script>
<script>
    window.onload = function() {
        $('#auteur').select2({
            tags: true,
            placeholder: "Auteur",
        });
        $('#edition').select2({
            tags: true,
            placeholder: "Selectionner",
        });

        $inputCover = document.getElementById('cover');
        if ($inputCover) {
            $inputCover.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    let $parts = this.files[0].name.split('.')
                    if (['jpg', 'jpeg', 'png', 'gif'].includes($parts[$parts.length - 1])) {
                        let reader = new FileReader();
                        reader.onload = function (e) {
                            $img = document.getElementById('imgPreview');
                            if ($img) {
                                $img.setAttribute('src', e.target.result);
                            }
                        }

                        reader.readAsDataURL(this.files[0]);
                    }
                }
            });
        }

        let $description = document.getElementById('description');
        let $htmlElmt = document.getElementById('messageLength');
        if ($description.value.length && $htmlElmt) {
            updateMessageLength($htmlElmt, $description.value.length)
        }
        if ($description && $htmlElmt) {
            $description.addEventListener('input', function () {
                updateMessageLength($htmlElmt, this.value.length)
            });
        }
    }

    function updateMessageLength($elmtHtml, value = 0) {
        $elmtHtml.innerText = value + '/300 caracteres';
    }

</script>
</body>
</html>
