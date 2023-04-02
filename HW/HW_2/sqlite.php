<?
// проверка подключения, тестовые данные:


$connection = new \PDO('sqlite:'. __DIR__ . "/blog.sqlite");

$connection->exec(
    "INSERT INTO users (firstname, lastname) VALUES ('Ivan','Petrov')"
);