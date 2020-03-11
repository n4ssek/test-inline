<?php

include_once 'MyLogger.php';
include_once 'PDOAdapter.php';
//функция для красивого и понятного вывода информации
function vardumper($var)
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}
//подключение к бд
$dsn = 'mysql:host=localhost;dbname=testdb';
$username = 'root';
$password = '';
$log_file = 'log.txt';
//экземляры классов для работы с бд и записи ошибок в лог файл
$errorLogger = new MyLogger($log_file);
$pdo = new PDOAdapter($dsn, $username, $password, $errorLogger);


//выбор ячейки максимального возраста из бд
$sql = 'SELECT max(age) as maxAge FROM `person`';
$maxAge = $pdo->execute('selectOne', $sql);
vardumper($maxAge);

//выбор случайного ряда из бд где mother_id не задан и возраст меньше максимального
$sql = 'SELECT * FROM `person` WHERE `mother_id` is null AND `age` < (SELECT max(age) FROM `person`) ORDER BY RAND() LIMIT 1';
$stmt = $pdo->execute('selectAll', $sql);
vardumper($stmt);


//изменение возраста у выбранной записи в предыдущем запросе на максимальный
//$sql = 'UPDATE `person` SET age = (SELECT maxAge FROM (SELECT max(age) AS maxAge FROM `person`) as subMaxAge)' .
//    ' WHERE age = (SELECT a.age (SELECT age FROM `person` WHERE mother_id is null AND age < (SELECT max(age) FROM `person`)' .
//    ' ORDER BY RAND() LIMIT 1) as `a`)';
//$stmt = $pdo->execute('execute', $sql);
//vardumper($stmt);

//Получить список персон максимального возраста (фамилия, имя)
$sql = 'SELECT lastname, firstname FROM `person` WHERE age = (SELECT max(age) FROM `person`)';
$stmt = $pdo->execute('selectAll', $sql);
vardumper($stmt);

//выбор ячеек с фамилией именем и возрастом отсортированными по фамилии и имени
$sql = 'SELECT lastname, firstname, age FROM `person` ORDER BY lastname, firstname DESC';
$tableValues = $pdo->execute('selectAll', $sql);
?>

<!--Заголовок "Список персон максимального возраста (здесь значение п.1)"-->
<h1>Список персон максимального возраста - <?= $maxAge->maxAge; ?></h1>

<!--Таблица, содержащая колонки: фамилия, имя, возраст.
В строках таблицы - персоны, упорядоченные по возрастанию фамилии и имени-->
<table>
    <tr>
        <th>Фамилия</th>
        <th>Имя</th>
        <th>Возраст</th>
    </tr>
    <?php foreach ($tableValues as $tableValue): ?>
    <tr>
        <td><?= $tableValue->lastname; ?></td>
        <td><?= $tableValue->firstname; ?></td>
        <td><?= $tableValue->age; ?></td>
    </tr>
    <?php endforeach; ?>
</table>
