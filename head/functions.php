<?php
/**
 * getSeachUrls()
 * return google links whith $artist and $music name needed
 */
function getSearchUrls($artist, $music){
    $searchName = str_replace("&", "%26", str_replace(":", "%3A", str_replace("'", "%27", str_replace(" ", "+", $artist)))) . "+" . str_replace("&", "%26", str_replace(":", "%3A", str_replace("'", "%27", str_replace(" ", "+", $music))));
    $artist = str_replace("&", "%26", str_replace(":", "%3A", str_replace("'", "%27", str_replace(" ", "+", $artist))));
    $searchNameLowercase = strtolower($searchName);
    $artistToLowerCase = strtolower($artist);
    $ytUrl = "https://www.google.com/search?q=Link%3Ayoutube.com+" . $searchNameLowercase . "+official";
    $spUrl = "https://www.google.com/search?q=Link%3Aspotify.com+" . $searchNameLowercase . "+track";
    $playlistUrl = "https://www.google.com/search?q=Link%3Aspotify.com+" . $artistToLowerCase . "+playlist";
    $urls = [$ytUrl, $spUrl, $playlistUrl];
    return $urls;
}

/**
 * getSeachUrls()
 * return google links with $artist and $music name needed with quote for more precision
 */
function getSearchAccurateUrls($artist, $music){
    $artist = str_replace("&", "%26", str_replace(":", "%3A", str_replace("'", "%27", str_replace(" ", "+", $artist))));
    $music = str_replace("&", "%26", str_replace(":", "%3A", str_replace("'", "%27", str_replace(" ", "+", $music))));
    $artist = strtolower($artist);
    $music = strtolower($music);
    $ytUrl = "https://www.google.com/search?q=Link%3Ayoutube.com+". '"'. "'" .  $artist . "+" . $music. "'". '"';
    $spUrl = "https://www.google.com/search?q=Link%3Aspotify.com+" . '"'. "'" . $artist . "+" . $music. "'". '"';
    $playlistUrl = "https://www.google.com/search?q=Link%3Aspotify.com+" . $artist . "+playlist";
    $urls = [$ytUrl, $spUrl, $playlistUrl];
    return $urls;
}

/**
 * getYtInfo()
 * return an array with $thumbnail, $author, $title, $short_description in a youtube video thanks url given in parameter
 */
function getYtInfo($ytUrl){
    if($ytUrl == "undefined"){
        $infos = null;
    }else{
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $ytUrl, $match);
        $videoId =  $match[1];
        $video = json_decode(getVideoInfo($videoId));
        $author = $video->videoDetails->author;
        $thumbnail = end($video->videoDetails->thumbnail->thumbnails)->url;
        $title = $video->videoDetails->title;
        $short_description = $video->videoDetails->shortDescription;
        $infos = [$thumbnail, $author, $title, $short_description];
    }
    return $infos;
}

/**
 * getVideoInfo()
 * retun an object $video who cantains all informations about it thanks youtube API
 */
function getVideoInfo($video_id){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://www.youtube.com/youtubei/v1/player?key=AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '{  "context": {    "client": {      "hl": "en",      "clientName": "WEB",      "clientVersion": "2.20210721.00.00",      "clientFormFactor": "UNKNOWN_FORM_FACTOR",   "clientScreen": "WATCH",      "mainAppWebInfo": {        "graftUrl": "/watch?v='.$video_id.'",           }    },    "user": {      "lockedSafetyMode": false    },    "request": {      "useSsl": true,      "internalExperimentFlags": [],      "consistencyTokenJars": []    }  },  "videoId": "'.$video_id.'",  "playbackContext": {    "contentPlaybackContext": {        "vis": 0,      "splay": false,      "autoCaptionsDefaultOn": false,      "autonavState": "STATE_NONE",      "html5Preference": "HTML5_PREF_WANTS",      "lactMilliseconds": "-1"    }  },  "racyCheckOk": false,  "contentCheckOk": false}');
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    return $result;
}

/**
 * getSpInfo()
 * return an array with $imgUrl, $artistName, $musicName, $downloadSp thanks to getSpotifyInfo.js
 */
function getSpInfo($spUrl){
    if($spUrl == "undefined"){
        $info = NULL;
    }else{
        exec("node ../js/getSpotifyInfo.js --spUrl=" . $spUrl, $spInfo, $error);
        if(sizeof($spInfo) == 5){
            $imgUrl = $spInfo[4];
            if(str_starts_with($imgUrl, 'https://i.scdn.co/image/')){
                $musicName = $spInfo[0];
                $artistName = substr($spInfo[1], 3);
                $downloadSp = downloadSp($spInfo[2], $musicName);
                if(str_contains($musicName, "&amp;")){
                    $musicName = str_replace("&amp;", "&", $musicName);
                }
                if(str_contains($musicName, '"')){
                    $musicName = str_replace('"', "", $musicName);
                }
                if(str_contains($fileName, 'ê')){
                    $fileName = str_replace('ê', "e", $fileName);
                }
                if(str_contains($fileName, '#')){
                    $fileName = str_replace('#', "", $fileName);
                }
            }else{
                $imgUrl = NULL;
                $musicName = NULL;
                $artistName = substr($spInfo[2], 3);
            }
            $info = [$imgUrl, $artistName, $musicName, $downloadSp];
        }else{
            $result = array_unique($spInfo);
            $imgUrl = end($result);
            for($i = 0; $i < sizeof($result); $i++){
                if(str_starts_with($result[$i], 'https://api.spotify-downloader.com/download/')){
                    $mp3Url = $result[$i];
                }
            }
            $musicName = $spInfo[0];
            $artistName = substr($spInfo[1], 3);
            $downloadSp = downloadSp($mp3Url, $musicName);
            if(str_contains($musicName, "&amp;")){
                $musicName = str_replace("&amp;", "&", $musicName);
            }
            if(str_contains($musicName, '"')){
                $musicName = str_replace('"', "", $musicName);
            }
            if(str_contains($fileName, 'ê')){
                $fileName = str_replace('ê', "e", $fileName);
            }
            if(str_contains($fileName, '#')){
                $fileName = str_replace('#', "", $fileName);
            }
            
            $info = [$imgUrl, $artistName, $musicName, $downloadSp];
        }
    }
    
    return $info;
}

/**
 * downloadSp()
 * download download a spotify track thanks url given in parameter
 */
function downloadSp($downloadUrl, $musicName){
    $url = "https://api.spotify-downloader.com/download/" . urlencode(str_replace("https://api.spotify-downloader.com/download/", "", $downloadUrl));
    $fileName = $musicName.'.mp3';

    if(str_contains($fileName, "&amp;")){
        $fileName = str_replace("&amp;", "&", $fileName);
    }
    if(str_contains($fileName, '"')){
        $fileName = str_replace('"', "", $fileName);
    }
    if(str_contains($fileName, 'ê')){
        $fileName = str_replace('ê', "e", $fileName);
    }
    if(str_contains($fileName, '#')){
        $fileName = str_replace('#', "", $fileName);
    }

    if (file_put_contents("../mp3/".$fileName, file_get_contents($url)))
        {
            return true;
        }
        else
        {
            return false;
        }
}

/**
 * getYoutubeMp3()
 * download a youtube video in mp3 from downloader 
 */
function getYoutubeMp3($ytUrl, $musicName){
    exec("node ../js/getYoutubeMp3.js --ytUrl=" . $ytUrl, $ytMp3Url, $error);
    if($ytMp3Url[0] != NULL){
        $ytMp3 = downloadYt($ytMp3Url[0], $musicName);
    }
    return $ytMp3;
}

/**
 * getYoutubeMp3() 
 * download a youtube video in mp3 from link return by the downloader for change path, music name and so on 
 */
function downloadYt($downloadUrl, $musicName){
    $fileName = $musicName.'.mp3';
    if(str_contains($fileName, "&amp;")){
        $fileName = str_replace("&amp;", "&", $fileName);
    }
    if(str_contains($fileName, '"')){
        $fileName = str_replace('"', "", $fileName);
    }

    if(str_contains($fileName, 'ê')){
        $fileName = str_replace('ê', "e", $fileName);
    }
    if(str_contains($fileName, '#')){
        $fileName = str_replace('#', "", $fileName);
    }

    if (file_put_contents("../mp3/".$fileName, file_get_contents($downloadUrl))){
        return "mp3/" . $fileName;
    }else{
        return false;
    }
}

/**
 * makeAQueue() 
 * get informations downloaded by queueDl.js for make one array with all informations needed to make a queue
 */
function makeAQueue($reponseArray){
    $folder = rand(5, 10000000);
    $folderName = strval($folder);
    mkdir($folderName, 0777);
    if(sizeof($reponseArray) >= 4){
        $mp3 = [];
        $duration = [];
        $titles = [$reponseArray[0], $reponseArray[4], $reponseArray[8]];
        $artistNames = [substr($reponseArray[1], 3), substr($reponseArray[5], 3), substr($reponseArray[9], 3)];
        $musicUrls = [$reponseArray[2], $reponseArray[6], $reponseArray[10]];
        $imgUrls = [$reponseArray[3], $reponseArray[7], $reponseArray[11]];

        for($i = 0; $i < sizeof($titles); $i++){
            $fileName = $titles[$i].'.mp3';
            if(str_contains($fileName, "&amp;")){
                $fileName = str_replace("&amp;", "&", $fileName);
            }
            if(str_contains($fileName, '"')){
                $fileName = str_replace('"', "", $fileName);
            }
        
            if(str_contains($fileName, 'ê')){
                $fileName = str_replace('ê', "e", $fileName);
            }
            if(str_contains($fileName, '#')){
                $fileName = str_replace('#', "", $fileName);
            }

            if (file_put_contents($folderName . "/" .$fileName, file_get_contents($musicUrls[$i]))){
                array_push($mp3, "head/" . $folderName . "/" .$fileName);
                $mp3file = new MP3File($folderName . "/" .$fileName);
                $duration1 = $mp3file->getDurationEstimate();
                $durationMinute = gmdate("i:s", $duration1);
                array_push($duration, $durationMinute);
            }else{
                //
            }
        }
    }else{
        //
    }
    $queue = array_merge($mp3, $imgUrls, $titles, $duration, $artistNames);
    array_push($queue, $folderName, 0);
    return $queue;
}

/**
 * MP3File class by zedwood.com used for get duration of track.
 */
class MP3File
{
    protected $filename;
    public function __construct($filename)
    {
        $this->filename = $filename;
    }
 
    public static function formatTime($duration) //as hh:mm:ss
    {
        //return sprintf("%d:%02d", $duration/60, $duration%60);
        $hours = floor($duration / 3600);
        $minutes = floor( ($duration - ($hours * 3600)) / 60);
        $seconds = $duration - ($hours * 3600) - ($minutes * 60);
        return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    }
 
    //Read first mp3 frame only...  use for CBR constant bit rate MP3s
    public function getDurationEstimate()
    {
        return $this->getDuration($use_cbr_estimate=true);
    }
 
    //Read entire file, frame by frame... ie: Variable Bit Rate (VBR)
    public function getDuration($use_cbr_estimate=false)
    {
        $fd = fopen($this->filename, "rb");
 
        $duration=0;
        $block = fread($fd, 100);
        $offset = $this->skipID3v2Tag($block);
        fseek($fd, $offset, SEEK_SET);
        while (!feof($fd))
        {
            $block = fread($fd, 10);
            if (strlen($block)<10) { break; }
            //looking for 1111 1111 111 (frame synchronization bits)
            else if ($block[0]=="\xff" && (ord($block[1])&0xe0) )
            {
                $info = self::parseFrameHeader(substr($block, 0, 4));
                if (empty($info['Framesize'])) { return $duration; } //some corrupt mp3 files
                fseek($fd, $info['Framesize']-10, SEEK_CUR);
                $duration += ( $info['Samples'] / $info['Sampling Rate'] );
            }
            else if (substr($block, 0, 3)=='TAG')
            {
                fseek($fd, 128-10, SEEK_CUR);//skip over id3v1 tag size
            }
            else
            {
                fseek($fd, -9, SEEK_CUR);
            }
            if ($use_cbr_estimate && !empty($info))
            { 
                return $this->estimateDuration($info['Bitrate'],$offset); 
            }
        }
        return round($duration);
    }
 
    private function estimateDuration($bitrate,$offset)
    {
        $kbps = ($bitrate*1000)/8;
        $datasize = filesize($this->filename) - $offset;
        return round($datasize / $kbps);
    }
 
    private function skipID3v2Tag(&$block)
    {
        if (substr($block, 0,3)=="ID3")
        {
            $id3v2_major_version = ord($block[3]);
            $id3v2_minor_version = ord($block[4]);
            $id3v2_flags = ord($block[5]);
            $flag_unsynchronisation  = $id3v2_flags & 0x80 ? 1 : 0;
            $flag_extended_header    = $id3v2_flags & 0x40 ? 1 : 0;
            $flag_experimental_ind   = $id3v2_flags & 0x20 ? 1 : 0;
            $flag_footer_present     = $id3v2_flags & 0x10 ? 1 : 0;
            $z0 = ord($block[6]);
            $z1 = ord($block[7]);
            $z2 = ord($block[8]);
            $z3 = ord($block[9]);
            if ( (($z0&0x80)==0) && (($z1&0x80)==0) && (($z2&0x80)==0) && (($z3&0x80)==0) )
            {
                $header_size = 10;
                $tag_size = (($z0&0x7f) * 2097152) + (($z1&0x7f) * 16384) + (($z2&0x7f) * 128) + ($z3&0x7f);
                $footer_size = $flag_footer_present ? 10 : 0;
                return $header_size + $tag_size + $footer_size;//bytes to skip
            }
        }
        return 0;
    }
 
    public static function parseFrameHeader($fourbytes)
    {
        static $versions = array(
            0x0=>'2.5',0x1=>'x',0x2=>'2',0x3=>'1', // x=>'reserved'
        );
        static $layers = array(
            0x0=>'x',0x1=>'3',0x2=>'2',0x3=>'1', // x=>'reserved'
        );
        static $bitrates = array(
            'V1L1'=>array(0,32,64,96,128,160,192,224,256,288,320,352,384,416,448),
            'V1L2'=>array(0,32,48,56, 64, 80, 96,112,128,160,192,224,256,320,384),
            'V1L3'=>array(0,32,40,48, 56, 64, 80, 96,112,128,160,192,224,256,320),
            'V2L1'=>array(0,32,48,56, 64, 80, 96,112,128,144,160,176,192,224,256),
            'V2L2'=>array(0, 8,16,24, 32, 40, 48, 56, 64, 80, 96,112,128,144,160),
            'V2L3'=>array(0, 8,16,24, 32, 40, 48, 56, 64, 80, 96,112,128,144,160),
        );
        static $sample_rates = array(
            '1'   => array(44100,48000,32000),
            '2'   => array(22050,24000,16000),
            '2.5' => array(11025,12000, 8000),
        );
        static $samples = array(
            1 => array( 1 => 384, 2 =>1152, 3 =>1152, ), //MPEGv1,     Layers 1,2,3
            2 => array( 1 => 384, 2 =>1152, 3 => 576, ), //MPEGv2/2.5, Layers 1,2,3
        );
        //$b0=ord($fourbytes[0]);//will always be 0xff
        $b1=ord($fourbytes[1]);
        $b2=ord($fourbytes[2]);
        $b3=ord($fourbytes[3]);
 
        $version_bits = ($b1 & 0x18) >> 3;
        $version = $versions[$version_bits];
        $simple_version =  ($version=='2.5' ? 2 : $version);
 
        $layer_bits = ($b1 & 0x06) >> 1;
        $layer = $layers[$layer_bits];
 
        $protection_bit = ($b1 & 0x01);
        $bitrate_key = sprintf('V%dL%d', $simple_version , $layer);
        $bitrate_idx = ($b2 & 0xf0) >> 4;
        $bitrate = isset($bitrates[$bitrate_key][$bitrate_idx]) ? $bitrates[$bitrate_key][$bitrate_idx] : 0;
 
        $sample_rate_idx = ($b2 & 0x0c) >> 2;//0xc => b1100
        $sample_rate = isset($sample_rates[$version][$sample_rate_idx]) ? $sample_rates[$version][$sample_rate_idx] : 0;
        $padding_bit = ($b2 & 0x02) >> 1;
        $private_bit = ($b2 & 0x01);
        $channel_mode_bits = ($b3 & 0xc0) >> 6;
        $mode_extension_bits = ($b3 & 0x30) >> 4;
        $copyright_bit = ($b3 & 0x08) >> 3;
        $original_bit = ($b3 & 0x04) >> 2;
        $emphasis = ($b3 & 0x03);
 
        $info = array();
        $info['Version'] = $version;//MPEGVersion
        $info['Layer'] = $layer;
        //$info['Protection Bit'] = $protection_bit; //0=> protected by 2 byte CRC, 1=>not protected
        $info['Bitrate'] = $bitrate;
        $info['Sampling Rate'] = $sample_rate;
        //$info['Padding Bit'] = $padding_bit;
        //$info['Private Bit'] = $private_bit;
        //$info['Channel Mode'] = $channel_mode_bits;
        //$info['Mode Extension'] = $mode_extension_bits;
        //$info['Copyright'] = $copyright_bit;
        //$info['Original'] = $original_bit;
        //$info['Emphasis'] = $emphasis;
        $info['Framesize'] = self::framesize($layer, $bitrate, $sample_rate, $padding_bit);
        $info['Samples'] = $samples[$simple_version][$layer];
        return $info;
    }
 
    private static function framesize($layer, $bitrate,$sample_rate,$padding_bit)
    {
        if ($layer==1)
            return intval(((12 * $bitrate*1000 /$sample_rate) + $padding_bit) * 4);
        else //layer 2, 3
            return intval(((144 * $bitrate*1000)/$sample_rate) + $padding_bit);
    }
}