<?php
error_reporting(0);
header('Content-type: application/json');

$ip = $_SERVER['REMOTE_ADDR'];

function scrap($url) {
 $curl = curl_init($url);
 curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36");
 curl_setopt($curl, CURLOPT_FAILONERROR, true);
 curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
 curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($curl, CURLOPT_HTTPHEADER, array("REMOTE_ADDR: $ip", "HTTP_X_FORWARDED_FOR: $ip"));
 $html = curl_exec($curl);
 curl_close($curl);
 return $html;
}

$g_id = $_GET["id"];
$video_link = "https://drive.google.com/file/d/".$g_id."/view";
$scrap_page = scrap($video_link);
preg_match('/google\.com\/file\/d\/([^\&\?\/]+)/', $video_link, $id);
$values = $id[1];
$dt = file_get_contents("https://docs.google.com/get_video_info?docid=$values");
$x = explode("&",$dt);
$t = array(); $g = array(); $h = array();

foreach($x as $r){
    $c = explode("=",$r);
    $n = $c[0]; $v=$c[1];
    $y = urldecode($v);
    $t[$n] = $v;
}

$streams = explode(',',urldecode($t['url_encoded_fmt_stream_map']));

foreach($streams as $dt){
    $x = explode("&",$dt);
    foreach($x as $r){
        $c = explode("=",$r);
        $n = $c[0]; $v = $c[1];
        $h[$n] = urldecode($v);
    }
    $g[] = $h;

}

for( $i= 0 ; $i <= 10 ; $i++ ){
 $quality = $g[$i]['itag'];
 if($quality == '18'){
  $file_sd = $g[$i]['url'];
 }$quality = $g[$i]['itag'];
 if($quality == '22'){
  $file_hd = $g[$i]['url'];
 }$quality = $g[$i]['itag'];
 if($quality == '37'){
  $file_full_hd = $g[$i]['url'];
 }
}

$regex = '/<meta property="og:image" content="(.+?)">/';
preg_match($regex,$scrap_page,$match);
$img = $match[1];

$regex2 = '/,\["title","(.+?)"]/';
preg_match($regex2,$scrap_page,$match2);
$title = $match2[1];

$array = array("file"=> $file_full_hd,"file_md"=>$file_hd,"file_sd"=>$file_sd,"img"=>$img,"title"=>$title);
echo json_encode($array);
?>
