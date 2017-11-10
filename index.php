<?php

ini_set('max_execution_time', 20*60);
set_time_limit(20*60);

include('inc/functions.php');

                $email                  = "aeronet.biz.id@gmail.com";
                $password               = "n0f12146412@";
                $tags                   = "dailymotion,api,sdk,test";
                $apiKeyDaily            = "fc7c94354c254c7f881c";
                $apiSecretDaily         = "b4108bfdf580d9f582984b18f29527aaf0c27629";
                $channel                = "videogames";
                $description            = "This My Best Video Ever On This Year";



if(isset($_GET['movie']) and !empty($_GET['movie'])) {

                $videometa          = scrapeIMDB($_GET['movie']);
                $pixelVideo         = $videometa[0];
                $mimeVideo          = $videometa[1];
                $urlVideo           = $videometa[2];
                $titleVideo         = $videometa[3];
                $posterVideo        = $videometa[4]; 
                $fileNameVideo      = download_content($urlVideo,"video",$titleVideo);
                $fileNamePoster     = download_content($posterVideo,"poster",$titleVideo);
                $file               = dirname(__FILE__).'/video/'.$fileNameVideo;
                upload_streamable($file,$titleVideo,$email,$password);
                upload_dailymotion($file,$titleVideo,$tags,$channel,$email,$password,$apiKeyDaily,$apiSecretDaily);
                upload_vimeo($file,$titleVideo,$description);
              
} else {

    echo '<center><h1>~~~</h1></center>';
 
}

