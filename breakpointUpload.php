<?php
/**
 * Created by PhpStorm.
 * User: jayding
 * Date: 2018/4/16
 * Time: 上午9:36
 */
$dir=$_POST['fileName'];
$dir="uploads/".md5($dir).date('His');
file_exists($dir) or mkdir($dir,0777,true);
$path=$dir."/".$_POST['blob'];
move_uploaded_file($_FILES["file"]["tmp_name"],$path);
if(isset($_POST['lastOne'])){
    $count=$_POST['lastOne'];
    $fp   = fopen($_POST['fileName'],"abw");
    for($i=0;$i<$count;$i++){
        $handle = fopen($dir."/".$i,"rb");
        fwrite($fp,fread($handle,filesize($dir."/".$i)));
        fclose($handle);
    }
    fclose($fp);
    echo 111;
}

?>