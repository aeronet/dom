<?php

// Include the library
include("imdb/imdb.php");
include('scrape/dom.php');
include('scrape/node.php');
include('inc/functions.php');




if(isset($_GET['movie']) and !empty($_GET['movie'])) {

ini_set('max_execution_time', 20*60);
set_time_limit(20*60);

$i          = new Imdb();
$movieName  = utf8_decode(urldecode($_GET['movie']));
$mArr       = array_change_key_case($i->getMovieInfo($movieName), CASE_UPPER);
// echo '<pre>';
// print_r($mArr);
            $email                  = "aeronet.biz.id@gmail.com";
            $password               = "n0f12146412@";
            $tags                   = 'dailymotion,api,sdk,test';
            $apiKeyDaily            = 'fc7c94354c254c7f881c';
            $apiSecretDaily         = 'b4108bfdf580d9f582984b18f29527aaf0c27629';
            $titleVideo             = str_replace('#x26;', '' ,$mArr['TITLE']);
            $urlVideo               = $mArr['VIDEOS'][0];
            $posterVideo            = $mArr['POSTER_FULL'];
            $base_url               = 'http://www.imdb.com';
            $referer                = '';
            $arr_http_call_result   = httpCall($urlVideo,$referer,FALSE,'',TRUE);
            $arr_http_call_result   = handle302Redirect($arr_http_call_result,$base_url);
            $arr_http_call_result   = handleMetaRedirect($arr_http_call_result,$base_url);

            if(!empty($arr_http_call_result['response'])){
                    $contents       = str_get_html_my($arr_http_call_result['response']);
                    $d              = $contents->find('body');
                    $filename       = "$titleVideo.txt";
                                      file_put_contents($filename,$d);
            }

            $data                    = file_get_contents("$titleVideo.txt");
            $after                   = substr($data, strpos($data, "push(") +5); 
            $explode                 = explode("</script>", $after);
            $jsondata                = str_replace(");","",$explode[0]);
            $obj                     = json_decode($jsondata, true);

                foreach ($obj['videos']['playlists'] as $key => $value) {
                    $videoID = $value['id'];
                }

                // print_r($obj['videos']['videoMetadata'][$videoID]);
                    $videometa = array();
                foreach ($obj['videos']['videoMetadata'][$videoID]['encodings'][0] as $key =>  $value) {
                    $videometa[] = $value;
                }

                $pixelVideo     = $videometa[0];
                $mimeVideo      = $videometa[1];
                $urlVideo       = $videometa[2];

                // echo $urlVideo ;
                 $fileNameVideo  = download_content($urlVideo,"video",$titleVideo);
                 $fileNamePoster = download_content($posterVideo,"poster",$titleVideo);

                 $file = dirname(__FILE__).'/video/'.$fileNameVideo;

                 upload_streamable($file,$titleVideo,$email,$password);
                 upload_dailymotion($file,$titleVideo,$tags,$email,$password,$apiKeyDaily,$apiSecretDaily);
              

       




} else {

    echo '<center><h1>Hello Words</h1></center>';
 


echo utf8_decode("Fast &#x26; Furious");
}

