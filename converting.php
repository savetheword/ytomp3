<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type:application/json");

$data=json_decode(file_get_contents("php://input"),true);

if($data === null){
    echo  json_encode(['success' => false , 'error' => 'invalid json'] );
    exit;
}



$url=filter_var($data['url'],FILTER_VALIDATE_URL);
$format= $format = isset($data['format']) && $data['format'] === 'mp4' ? 'mp4' : 'mp3';
if(!filter_var($url,FILTER_VALIDATE_URL)){
    echo json_encode(['success' => false ,'error'=>'invalid url']);
    exit;

}

$VideoId=extractVideoId($url);


if($VideoId){
    $infoCommand=escapeshellcmd("yt-dlp.exe --print-json \"$url\"");
    $infoJson=shell_exec($infoCommand);
    $info=json_decode($infoJson,true);
    if(!$info && !isset($info['title'])){
        echo json_encode(['success'=>false,'error' =>'failed to fetch']);
        exit;
    }

    $title=preg_replace('/[^a-zA-Z0-9-_]/','',$info['title']);
    $title=str_replace(' ','_',$title);

    $OutputDir=__DIR__.'/downloads/';
    if(!is_dir($OutputDir)){
        mkdir($OutputDir,0777,true);
    }

    $uniqueId=uniqid();
    if($format==='mp4'){
        $OutputFile=$OutputDir . $uniqueId .'_'. $title .'.mp4';
        $command = escapeshellcmd("yt-dlp.exe -f bestvideo+bestaudio --merge-output-format mp4 --output \"$OutputFile\" \"$url\"");


    }else{
        $OutputFile=$OutputDir . $uniqueId . '_' . $title . '.mp3';
        $command=escapeshellcmd("yt-dlp.exe -x --audio-format mp3 --audio-quality 0 --output \"$OutputFile\" \"$url\"");
    }
    exec($command,$output,$returnCode);
    if(file_exists($OutputFile) && $returnCode===0){
        $downloadUrl= 'downloads/' . basename($OutputFile);
        echo json_encode(['success'=>true,'downloadUrl'=>$downloadUrl]);

    }else{
        echo json_encode(['success'=>false , 'error'=>"conversion failed"]);
    }
}else{
    echo json_encode(['success'=>false,'error'=>'invalid youtube url']);
}





function extractVideoId($url){
    preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/',$url,$matches);
    return $matches[1] ?? null;

}
?>