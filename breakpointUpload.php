<?php
/**
 * Created by PhpStorm.
 * User: jayding
 * Date: 2018/4/16
 * Time: 上午9:36
 */
date_default_timezone_set('Asia/Chongqing');
//file_put_contents('ding',var_export($_FILES,1).'---'.var_export($_POST,1));
$dir_prefix = "uploads/".date("Y")."/".date("m");
$lastone = $_POST['lastOne'];
$content = $_POST['content'];
$filename = $_POST['fileName'];
//$dir = $dir_prefix."/tmp/".md5( $filename.$user_id.$user_name );
$dir = $dir_prefix."/tmp/".md5( $filename );
if ( !jd_mkdir($dir) )
    errorOutput("临时目录创建失败!");

//$videodir =  $dir_prefix."/target/".md5( $filename.$user_id.$user_name );
$videodir =  $dir_prefix."/target/".md5( $filename );
if ( !jd_mkdir($videodir) )
    errorOutput("临时目录创建失败!");

$path = $dir."/".$_POST['blob'];
if ( !move_uploaded_file( $_FILES["file"]["tmp_name"] , $path ) )
    errorOutput("临时文件读取失败!");

$return_data = array();
if( isset($lastone) && $lastone )
{
    $count = $lastone;
    $fp    = fopen( $videodir."/".$filename, "abw");
    for( $i=0; $i<$count; $i++)
    {
        $handle = fopen( $dir."/".$i, "rb");
        fwrite( $fp, fread($handle,filesize($dir."/".$i)));
        fclose($handle);
    }
    fclose($fp);

    if ( $content )
    {
        $f = fopen($videodir."/".$filename,"r");
        $fcontent = fread($f,filesize($videodir."/".$filename));
        fclose($f);
        if ( $content != md5($fcontent) )
        {
            jd_rmdirs($dir);
            jd_rmdirs($videodir);
            errorOutput("文件已损坏,请清空缓存重新上传!");
        }
    }
    $return = array('status'=>1);
    echo json_encode($return);
    die;
}else{
    $return = array('status'=>0);
    echo json_encode($return);
    die;
}

function jd_mkdir($dir)
{
    if (!is_dir($dir))
    {
        if(!@mkdir($dir, 0777, 1))
        {
            return false;//创建目录失败
        }
    }
    return true;
}

function jd_rmdirs($path)
{
    if ($handle = @opendir($path))//打开路径成功
    {
        while ($file = readdir($handle))//循环读取目录中的文件名并赋值给$file
        {
            if ($file != '.' && $file != '..')//排除当前路径和前一路径
            {
                if (is_dir($path."/".$file))
                {
                    jd_rmdirs($path . '/' . $file);
                }
                else
                {
                    @unlink($path . '/' . $file);
                }
            }
        }
        @rmdir($path);
        closedir($handle);
    }
}

function errorOutput($msg)
{
    $return = array('msg'=>$msg,'status'=>0);
    echo json_encode($return);
    die;
}
?>