<?php
if (session_status() === PHP_SESSION_NONE) {
    // Initialiser la session
    session_start();
}
ini_set('date.timezone', 'Africa/Douala');

function getDatabase()
{
    $host = 'localhost';
    $dbname = 'mybook';
    $username = 'root';
    $password = '';

    try {
        $pdo = new \PDO('mysql:host='. $host .';dbname='.$dbname, $username, $password);
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $pdo;

    } catch (\PDOException $e) {
        echo 'Erreur provenant de la base de donnees. Error: ' . $e->getMessage();
        die();
    }
}


function redirectForAuth($ifIsConnect = true)
{
    if ($ifIsConnect && isset($_SESSION['auth_user'])) {
        header('Location: index.php');
    } elseif (!$ifIsConnect && !isset($_SESSION['auth_user'])) {
        header('Location: login.php');
    }
}

function redirectNotFound() {
    header('HTTP/1.1 404 Not Found');
    include('404.php');
    exit();
}

function getRole($role_name = 'user')
{
    $roles = ['admin', 'user', 'author'];

    foreach ($roles as $role_value) {
        $query = getDatabase()->prepare('SELECT * FROM roles WHERE name_role = ?');
        $query->execute([$role_value]);
        $role = $query->fetch();

        if (!$role) {
            $newQuery = getDatabase()->prepare('INSERT INTO roles (name_role) VALUES (:role_name)');
            $newQuery->bindParam(':role_name', $role_value);
            $newQuery->execute();
        } elseif ($role_name == $role->name_role) {
            return $role;
        }
    }

    if (!in_array($role_name, $roles)) {
        $role_name = 'user';
    }

    return getDatabase()->query("SELECT * FROM roles WHERE name_role = '$role_name'")->fetch();
}


function getEditions()
{
    $editions = getDatabase()->query('SELECT * FROM editions')->fetchAll();
    if (count($editions) > 2) {
        return $editions;
    }

    $editions = ['Edition cle', '3ag edition', 'ugobok'];

    foreach ($editions as $edition) {
        $query = getDatabase()->prepare('SELECT * FROM editions WHERE name_edition = ?');
        $query->execute([$edition]);
        if(!($query->fetch())) {
            $newQuery = getDatabase()->prepare('INSERT INTO editions (name_edition) VALUES (:edition_name)');
            $newQuery->bindParam(':edition_name', $edition);
            $newQuery->execute();
        }
    }

    return getDatabase()->query("SELECT * FROM editions")->fetchAll();
}

function getUsers($role = null)
{
    $sql = 'SELECT * FROM users left join roles ON users.id_role_user = roles.id_role';
    $params = [];
    if ($role) {
        $sql .= ' WHERE roles.name_role = ?';
        $params = [$role];
    }

    $query = getDatabase()->prepare($sql);
    $query->execute($params);

    return $query->fetchAll();
}

function getFile ($filename, $withCover = true) {
    return 'files/books/'.$filename;
}

function deleteFile ($filename, $withCover = true) {
    if ($filename) {
        $filename = getFile($filename);
        if (file_exists($filename)) {
            unlink($filename);
        }
    }
}


function getCover ($filename, $withCover = true) {
    return 'img/covers/'.$filename;
}

function deleteCover ($filename, $withCover = true) {
    if ($filename) {
        $filename = getCover($filename);
        if (file_exists($filename)) {
            unlink($filename);
        }
    }
}

