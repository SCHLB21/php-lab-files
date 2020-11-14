<?php

$iter = 0;
function outdirInfo( $name, $path,  $iter)
{
    $iter+=1;
    echo '<div>'; // начало блока с содержимым каталога
    if($iter==1) {
        echo 'Каталог ' . $name . '<br>'; // выводим имя каталога
    }else{
        for($j=1; $j<$iter;$j++){
            echo '&nbsp&nbsp';
        }
        echo 'Подкаталог ' . $name . '<br>'; // выводим имя каталога
    }
    $dir = opendir( $path ); // открываем каталог
    // перебираем элементы каталога пока они не закончатся
    $i = 0;
    while( ($file=readdir($dir) ) !== false )
    {
        $i+=1;
        if( is_dir($file) && $i>2) // если элемент каталог
//        echo 'Подкаталог '.$file.'<br>'; // выводим его имя
            outdirInfo( $file,  $path.'/'.$file, $iter);
    else
        if( is_file($file) ) // если элемент файл
        {
            for($j=0; $j<$iter;$j++){
                echo '&nbsp&nbsp';
            }
            echo 'Файл ' . $file . '<br>'; // выводим его имя
        }
    }
    closedir($dir); // закрываем каталог
    echo '</div>'; // конец блока с содержимым каталога
}
outdirInfo('lab1','./', $iter);
