<?php
session_start();
if(!isset($_SESSION['user'])){
    echo '<a href="/">Необходима аутентификация!</a>';
    exit();
}
echo '<div id="dir_tree">';
$iter = 0;
outdirInfo('lab1',getcwd(), $iter);
echo '</div>';
function outdirInfo($name, $path, $iter)
{
    $maxIters = 5; //Максимальное количество каталогов
    $iter += 1;
    echo '<div>'; // начало блока с содержимым каталога
    if ($iter == 1) {
        echo 'Каталог ' . $name . '<br>'; // выводим имя каталога
    } else {
        for ($j = 1; $j < $iter; $j++) {
            echo '&nbsp&nbsp&nbsp&nbsp';
        }
        echo 'Подкаталог ' . $name . '<br>'; // выводим имя каталога
    }
    if ($iter >= $maxIters) return;
    $dir = opendir($path); // открываем каталог
    // перебираем элементы каталога пока они не закончатся
    while (($file = readdir($dir)) !== false) {
        if (is_dir($path . '/' . $file) && $file != '.' && $file != '..') // если элемент каталог
        {
            outdirInfo($file, $path . '/' . $file, $iter);
        } else
            if (is_file($path . '/' . $file)) // если элемент файл
            {
                for ($j = 0; $j < $iter; $j++) {
                    echo '&nbsp&nbsp&nbsp&nbsp';
                }
//                echo 'Файл ' . $file . '<br>'; // выводим его имя
                echo makeLink($file, $path);
            }
    }
    closedir($dir); // закрываем каталог
    echo '</div>'; // конец блока с содержимым каталога
}

function makeLink( $name, $path )
{
// формируем адрес ссылки
    $link='viewer.php?filename='.UrlEncode($path).'/'.$name;
// выводим ссылку в HTML-код страницы
    echo '<a href="'.$link.'" target="_blank">Файл '.$name.'</a><br>';

}
?>
<form method="post" enctype="multipart/form-data" action="index.php">
    <label for="dir-name">Каталог на сервере</label>
    <input type="text" name="dir-name" id="dir-name">
    <label for="myfilename">Локальный файл</label>
    <input type="file" name="myfilename[]" multiple>
    <input type="submit" value="Отправить файл на сервер">
</form>
<?php
if (isset($_FILES['myfilename'])) // были отправлены данные формы
{
    if (isset($_FILES['myfilename']['tmp_name'])) // если файл загружен
    {
        for($i=0;$i<count($_FILES['myfilename']);$i++){
//            print_r($_FILES['myfilename']);
        }
        if ($_FILES['myfilename']['tmp_name']) // если файл существует
        {
            foreach( $_FILES['myfilename']['tmp_name'] as $i=>$f ) {
            move_uploaded_file($f, makeName($_FILES['myfilename']['name'][$i]));
                echo 'Файл ' . $_FILES['myfilename']['name'][$i] . ' загружен на сервер<br>';
            }
        } elseif ($_POST['dir-name'] != '') {
            deleteCatalog(getcwd() .'/'. $_POST['dir-name']);
        }else{
            echo "<script>alert('Нельзя удалить корневой диалог')</script>";
        }
    }
}
function makeName($filename)
{
    if (!file_exists(getcwd() . '/' . $_POST['dir-name'])) // если каталога не существует
    {
        umask(0); // сбрасываем значение umask
        mkdir($_POST['dir-name'], 0777, true); // создаем ее
    }
    $ext = end(explode('.', $filename));
    $n = 1;
//    echo getcwd().$_POST['dir-name'].'/'.$n.'.'.$ext;
    if ($_POST['dir-name'] == '') {
        while (file_exists(getcwd() . '/' . $_POST['dir-name'] . '/' . $n . '.' . $ext))
            $n++;
        updateFileList(getcwd() . '/users.csv', getcwd() . $_POST['dir-name'] . '/' . $n . '.' . $ext);
        return getcwd() . $_POST['dir-name'] . '/' . $n . '.' . $ext;
    } else {
        while (file_exists(getcwd() . '/' . $_POST['dir-name'] . '/' . $n . '.' . $ext))
            $n++;
        updateFileList(getcwd() . '/users.csv', getcwd() . '/' . $_POST['dir-name'] . '/' . $n . '.' . $ext);
        return getcwd() . '/' . $_POST['dir-name'] . '/' . $n . '.' . $ext;
    }
}

function deleteCatalog($path){
    if(!file_exists($path)){
        echo 'Такого каталога не существует';
        return;
    }
    $dir = opendir($path);
    while (($file = readdir($dir)) !== false) {
        if (is_dir($path . '/' . $file) && $file != '.' && $file != '..')
        {
            deleteCatalog($path . '/' . $file);
        }elseif(is_file($path . '/' . $file)){
            unlink($path . '/' . $file);
        }
    }
    closedir($dir);
    rmdir($path);
    return;
}

function updateFileList($tablename,$filename){
    $info = file($tablename);
//    echo $info;
//    print_r($info);
    $f=fopen($tablename, 'wt');
    flock($f, LOCK_EX);
    foreach( $info as $k=>$user ){
        $data = str_getcsv($user, ',');
        $user = trim($user);
        if( $data[0]== $_SESSION['user'][0] ) {
            $user .= ',' . $filename;
        }
        $user = $user."\n";
        fputs($f,$user);
    }
    flock($f, LOCK_UN );
    fclose($f);
//    print_r($info);
}