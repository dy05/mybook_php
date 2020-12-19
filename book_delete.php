<?php
include "functions/app.php";
redirectForAuth(false);

if (!isset($_GET['id']) || (isset($_GET['id']) && !filter_var($_GET['id'], FILTER_VALIDATE_INT))) {
    redirectNotFound();
}

$book = getDatabase()->query('SELECT * FROM books WHERE books.id_book  = '.$_GET['id'])->fetch();

if (!$book) {
    redirectNotFound();
}
$query = getDatabase()->query('DELETE FROM books WHERE id_book = '.$_GET['id']);
if ($query->execute()) {
    deleteFile($book->filename);
    deleteCover($book->couverture_book);
    $_SESSION['delete'] = true;
    header('Location: books_list.php');
}

