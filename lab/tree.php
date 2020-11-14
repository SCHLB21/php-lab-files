<?php
function outdirInfo( $name, $path )
{
echo '<div>'; // начало блока с содержимым каталога
    echo 'Каталог '.$name.'<br>'; // выводим имя каталога
    $dir = opendir( $path ); // открываем каталог
    // перебираем элементы каталога пока они не закончатся
    while( ($file=readdir($dir) ) !== false )
    {
        if( is_dir($file) ) // если элемент каталог
        echo 'Подкаталог '.$file.'<br>'; // выводим его имя
//        outdirInfo( $file, $path.'/'.$file );
    else
        if( is_file($file) ) // если элемент файл
        echo 'Файл '.$file.'<br>'; // выводим его имя
    }
    closedir($dir); // закрываем каталог
    echo '</div>'; // конец блока с содержимым каталога
}
outdirInfo('lab1','./дфи');
