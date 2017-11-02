<?php

// Include the library
include('dom.php');
 include('node.php');




function httpCall($url='',$referer='',$post_call=FALSE,$postdata='',$include_header=FALSE,$arr_curl_option=array()){

		$arr_result=array();
       // $cookie_file = __FILE__ . "image_grabber.cookie";
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
        //curl_setopt($ch,CURLOPT_COOKIEFILE,$cookie_file);
        //curl_setopt($ch,CURLOPT_COOKIEJAR,$cookie_file);
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


$base_url = 'http://www.imdb.com';
  $referer='';
 $url=$base_url.'/video/imdb/vi3225003545';

        $arr_http_call_result=httpCall($url,$referer,FALSE,'',TRUE);
        $arr_http_call_result=handle302Redirect($arr_http_call_result,$base_url);
       $arr_http_call_result=handleMetaRedirect($arr_http_call_result,$base_url);

 if(!empty($arr_http_call_result['response'])){
echo '<pre>';
            $contents = str_get_html_my($arr_http_call_result['response']);
$d=$contents->find('body');

$filename ='data.txt';
file_put_contents($filename,$d);
}

//////////////////


$data       = file_get_contents('data.txt');
$after      = substr($data, strpos($data, "push(") +5); 
$explode    = explode("</script>", $after);
$jsondata   = str_replace(");","",$explode[0]);
$obj        = json_decode($jsondata, true);

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
                

$url = $urlVideo ;
$parse_url = parse_url($url) ;
$path_info = pathinfo($parse_url['path']) ;
$file_extension = $path_info['extension'] ;
$save_path = 'video/' ;
$file_name = 'name' . "." . $file_extension ;
file_put_contents($save_path . $file_name , fopen($url, 'r'));

