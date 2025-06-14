<?php
// Database config
$host = "localhost";    // getenv("MYSQL_HOSTNAME");
$db = "blog";           // getenv("MYSQL_DATABASE");
$user = "newuser";      // getenv("MYSQL_USER");
$password = "password"; // getenv("MYSQL_PASSWORD");

try {
    $conn = new PDO("mysql:host={$host};dbname={$db}", $user, $password);
} catch (PDOException $e) {
    echo "Connection error: " . $e->getMessage();
}

$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function exec_query($query, ...$values)
{
    global $conn;
    try {
        $sth = $conn->prepare($query);
        $sth->execute($values);
        $sth->setFetchMode(PDO::FETCH_ASSOC);
        return $sth;
    } catch (PDOException $e) {
        echo $e->getMessage();
        die('Error');
    }
}

function select_all($query, ...$values)
{
    $res = exec_query($query, ...$values);
    return $res->fetchAll();
}

function select_one($query, ...$values)
{
    $res = exec_query($query, ...$values);
    return $res->fetch();
}

function insert_one($query, ...$values)
{
    exec_query($query, ...$values);
}
