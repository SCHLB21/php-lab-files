<?php
session_start();
if(!isset($_SESSION['user'])){
    echo '<a href="/">Необходима аутентификация!</a>';
    exit();
}

//Проверка на пустое значения переменной $_GET
if($_GET['filename']==''){
    echo '<a href="/">Пустое значение параметра filename</a>';
    exit();
}
if($_GET['filename']=='users.csv'){
    echo '<a href="/">Секретная информация</a>';
    exit();
}
if(!file_exists($_GET['filename'])){
    echo '<a href="/">Файла с таким названием не существует</a>';
    exit();
}
//Проверка на принадлежность файла другому пользователю
$f = fopen('users.csv', 'rt');
while (!feof($f)) // пока не найден конец файла
{
    $test_user = explode(',', fgets($f));

    $access = False;
    foreach ($test_user as $filename) {
        if (trim($filename) == $_GET['filename']) {
            $access = True;
            break;
        }
    }
    if ($access) {
        if ($_SESSION['user'][0] == $test_user[0]) {
            break;
        } else {
            echo '<a href="/">Файл пренадлежит другому пользователю</a>';
            exit();
        }
    }
}
fclose($f);
header('Content-Type: text/plain');
echo file_get_contents($_GET['filename']);