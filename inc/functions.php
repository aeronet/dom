<?php



function httpCall($url='',$referer='',$post_call=FALSE,$postdata='',$include_header=FALSE,$arr_curl_option=array()){

        $arr_result=array();
        $ch=curl_init();
        if($include_header!==FALSE)
            curl_setopt($ch,CURLOPT_HEADER,TRUE);
        curl_setopt($ch,CURLOPT_AUTOREFERER,1);
        curl_setopt($ch,CURLOPT_COOKIESESSION,0);
        curl_setopt($ch,CURLOPT_FAILONERROR,0);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,0);
        curl_setopt($ch,CURLOPT_FRESH_CONNECT,1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch,CURLOPT_UNRESTRICTED_AUTH,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,60);
        curl_setopt($ch,CURLOPT_TIMEOUT,60);
        curl_setopt($ch,CURLOPT_ENCODING,"");
        if($post_call!==FALSE){
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$postdata);
        }
        if(!empty($referer))
            curl_setopt($ch,CURLOPT_REFERER,$referer);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:25.0) Gecko/20100101 Firefox/25.0');

        if(!empty($arr_curl_option)){
            foreach($arr_curl_option as $key_option=>$value_option){
                curl_setopt($ch,$key_option,$value_option);
            }
        }
        $response=curl_exec($ch);
        $error=curl_error($ch);
        $info=curl_getinfo($ch);
        $err_no=curl_errno($ch);
        curl_close($ch);
        unset($ch);
        $arr_result['response']=$response;
        $arr_result['error']=$error;
        $arr_result['info']=$info;
        $arr_result['err_no']=$err_no;
        return $arr_result;
    }


    function handleMetaRedirect(
        $arr_http_call_result=array(),$base_url=''){
        $pos=FALSE;
        do{
            $html=new Htmldom();
            $html->load($arr_http_call_result['response']);
            $noscript_meta=$html->find('noscript meta',0);
            if(!empty($noscript_meta)){
                $str_meta_redirection=trim(htmlspecialchars_decode($noscript_meta->content));
                $find_me='0;url=';$pos=strpos($str_meta_redirection,$find_me);
                if($pos!==FALSE){
                    $meta_redirection_url=trim(substr($str_meta_redirection,$pos+strlen($find_me)));
                    if(!empty($base_url)){
                        $find_me=$base_url;
                        $pos=strpos($meta_redirection_url,$find_me);
                        if(($pos===FALSE)&&($meta_redirection_url[0]==='/'))
                            $meta_redirection_url=$base_url.$meta_redirection_url;
                    }
                    if(!empty($url))
                        $referer=$url;
                    else $referer='';
                    $url=$meta_redirection_url;
                    $arr_http_call_result=$this->httpCall($url,$referer);
                }
            }
            $html->clear();
            unset($html);
        }
        while($pos!==FALSE);
        return $arr_http_call_result;
    }

function handle302Redirect($arr_http_call_result=array(),$base_url=''){
        while(($arr_http_call_result['info']['http_code']===302)||($arr_http_call_result['info']['http_code']===301)){
            list($header,$body)=explode("\r\n\r\n",$arr_http_call_result['response'],2);
            $meta_redirection_url='';
            $find_me='Location: ';
            $pos=stripos($header,$find_me);
            if($pos!==FALSE){
                $meta_redirection_url=trim(substr($header,$pos+strlen($find_me)));
                $find_me2="\r\n";
                $pos2=strpos($meta_redirection_url,$find_me2);
                if($pos2!==FALSE)
                    $meta_redirection_url=trim(substr($meta_redirection_url,0,$pos2));
            }else{
                $pos=stripos($arr_http_call_result['response'],$find_me);
                if($pos!==FALSE){
                    $meta_redirection_url=trim(substr($arr_http_call_result['response'],$pos+strlen($find_me)));
                    $find_me2="\r\n";
                    $pos2=strpos($meta_redirection_url,$find_me2);
                    if($pos2!==FALSE)
                        $meta_redirection_url=trim(substr($meta_redirection_url,0,$pos2));
                }
            }
            if(!empty($meta_redirection_url)&&!empty($base_url)){
                $find_me=$base_url;$pos=strpos($meta_redirection_url,$find_me);
                if(($pos===FALSE)&&($meta_redirection_url[0]==='/'))
                    $meta_redirection_url=$base_url.$meta_redirection_url;
            }
            if(!empty($url))
                $referer=$url;else $referer='';
            $url=$meta_redirection_url;
            $arr_http_call_result=httpCall($url,$referer,FALSE,'',TRUE);
        }
        return $arr_http_call_result;
    }



    function str_get_html_my($str, $lowercase=true, $forceTagsClosed=true, $target_charset = 'UTF-8', $stripRN=true, $defaultBRText="\r\n", $defaultSpanText=" ")
    {
        $dom = new Htmldom(null, $lowercase, $forceTagsClosed, $target_charset, $stripRN, $defaultBRText, $defaultSpanText);
        if (empty($str) || strlen($str) > 600000)
        {
            $dom->clear();
            return false;
        }
        $dom->load($str, $lowercase, $stripRN);
        return $dom;
    }

    function download_content($url,$save_path,$file_name) {
        $parse_url      = parse_url($url) ;
        $path_info      = pathinfo($parse_url['path']) ;
        $file_extension = $path_info['extension'] ;
        $file           = "$file_name.$file_extension" ;
                          file_put_contents("$save_path/$file" , fopen($url, 'r'));
        return $file;
    }



    function upload_streamable($file,$file_name,$email,$password) {


        if (file_exists($file)) {

           // echo "The file $file exists <br>";

                // Connecting to website.
                $ch = curl_init();
                $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';
                curl_setopt($ch, CURLOPT_POST,1);

                curl_setopt($ch, CURLOPT_USERPWD, "$email:$password");
                curl_setopt($ch, CURLOPT_URL, 'https://api.streamable.com/upload');
                curl_setopt($ch, CURLOPT_USERAGENT, $agent);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                $postData = array(
                    
                    'file' => new CURLFile($file,'video/mpeg',$file_name),
                   
                );
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData );

                     curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);


                $server_output = curl_exec ($ch);
                // echo "<pre>";
                // print_r($postData);
                // echo 'info: <br> ';
                // print_r(curl_getinfo($ch));
                // echo 'error : <br>';
                // print_r(curl_error($ch));
                // echo 'out : <br> ';
                // print_r($server_output);


                // {"status": 1, "shortcode": "jrpri"}
                $hasil = json_decode($server_output, true);
    echo '<strong>'.$file_name.'</strong> Has Uploaded On <a href="https://streamable.com/'.$hasil['shortcode'].'">Streamable</a><br/>';
                
        } else {
            echo "The file $file does not exist <br>";
        }


    }


    function upload_dailymotion($pathfile,$titleVideo,$tags,$channel,$email,$password,$apiKey,$apiSecret) {

        require_once 'sdk/Dailymotion.php';

        // Scopes you need to run your tests
        $scopes = array(
            'userinfo',
            'feed',
            'manage_videos',
        );
        // Dailymotion object instanciation
        $api = new Dailymotion();
        $api->setGrantType(
            Dailymotion::GRANT_TYPE_PASSWORD,
            $apiKey,
            $apiSecret,
            $scopes,
            array(
                'username' => $email,
                'password' => $password,
            )
        );


        $url = $api->uploadFile($pathfile);

        $result = $api->post(
            '/videos',
            array(
                'url'       => $url,
                'title'     => $titleVideo,
                'tags'      => $tags,
                'channel'   => $channel,
                'published' => true,
            )
        );
        
        //http://www.dailymotion.com/video/x681rsd

echo '<strong>'.$titleVideo.'</strong> Has Uploaded On <a href="http://www.dailymotion.com/video/'.$result['id'].'"> Dailymotion</a><br/>';
   

    }




    function scrapeIMDB($movieName){

            include("imdb/imdb.php");
            include('scrape/dom.php');
            include('scrape/node.php');

            $i                      = new Imdb();
            $movieName              = utf8_decode(urldecode($movieName));
            $mArr                   = array_change_key_case($i->getMovieInfo($movieName), CASE_UPPER);
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

                $add_meta = array($titleVideo,$posterVideo);
                $videometa = array_merge($videometa,$add_meta);
                return $videometa;
    }




    function upload_vimeo($file,$title,$description) {


ini_set('max_execution_time', 20*60);
set_time_limit(20*60);

require_once('vimeo/vimeo.php');

if (!function_exists('json_decode')) {
    throw new Exception('We could not find json_decode. json_decode is found in php 5.2 and up, but not found on many linux systems due to licensing conflicts. If you are running ubuntu try "sudo apt-get install php5-json".');
}

$config = json_decode(file_get_contents('vimeo/config.json'), true);



if (empty($config['access_token'])) {
    throw new Exception('You can not upload a file without an access token. You can find this token on your app page, or generate one using auth.php');
}

//$lib = new Vimeography_Vimeo($config['client_id'], $config['client_secret'], $config['access_token']);
$lib = new Vimeography_Vimeo($config['client_id'], $config['client_secret'], $config['access_token']);

//  Get the args from the command line to see what files to upload.
$files = array($file);
//array_shift($files);

//   Keep track of what we have uploaded.
$uploaded = array();
//echo '<pre>';
//  Send the files to the upload script.
foreach ($files as $file_name) {
    //  Update progress.
    //print 'Uploading ' . $file_name . "\n";
    //echo '<br>';
    try {
        //  Send this to the API library.
        $uri = $lib->upload($file_name);
 
        //  Now that we know where it is in the API, let's get the info about it so we can find the link.
        $metadata = array(
    'name' => $title,
    'description' => $description,
);

        $video_data = $lib->request($uri,$metadata, 'PATCH');

 

        //  Pull the link out of successful data responses.
        $link = '';
        if($video_data['status'] == 204) {
            $link =  str_replace('/videos/', '',$video_data['headers']['Location']);
            // foreach ($header as $key => $value) {
            //     = $value;
            // }



        }

        //  Store this in our array of complete videos.
        $uploaded[] = array('file' => $file_name, 'api_video_uri' => $uri, 'link' => $link);
    }
    catch (VimeoUploadException $e) {
        //  We may have had an error.  We can't resolve it here necessarily, so report it to the user.
        //print 'Error uploading ' . $file_name . "\n";
        //echo '<br>';
        //print 'Server reported: ' . $e->getMessage() . "\n";
    }
}

//  Provide a summary on completion with links to the videos on the site.
//print 'Uploaded ' . count($uploaded) . " files.\n\n";
foreach ($uploaded as $site_video) {
    extract($site_video);
    //print "$file is at https://vimeo.com/$link.\n <br>";

    echo '<strong>'.$title.'</strong> Has Uploaded On <a href="https://vimeo.com/'.$link.'">Vimeo</a><br/>';
   
}


    }





    ?>