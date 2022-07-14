<?php 
include "functions.php";
if(isset($_POST)){
    session_start();
    $artist = $_POST["artistName"];
    $music = $_POST["musicName"];

    /**
     * check accuracy selected by the user
     */
    if(empty($_POST["checkbox"])){

    }else{
        $accurate = $_POST["checkbox"];
    }
    if(empty($_SESSION["queue"])){

    }else{
        unset($_SESSION["queue"]);
    }
    if(empty($accurate)){
        $searchUrls = getSearchUrls($artist, $music);
        /**
         * launch js script who return Urls
         */
        exec("node ../js/getAllUrls.js --ytUrl=" . $searchUrls[0] . "  --spUrl=". $searchUrls[1] . " --accurate=false --playlistUrl=" . $searchUrls[2] , $urls, $error);
    }else{
        $searchUrls = getSearchAccurateUrls($artist, $music);
        /**
         * launch js script who return Urls with more precision
         */
        exec("node ../js/getAllUrls.js --ytUrl=" . $searchUrls[0] . "  --spUrl=". $searchUrls[1] . " --accurate=true --playlistUrl=" . $searchUrls[2], $urls, $error);
    }

    /**
     * set into $_SESSION all information like, cover, artist name, music name, description, and song
     */
    if(empty($urls)){
        echo "No urls";
    }else{
        $spInfo = getSpInfo($urls[1]);
        $ytInfo = getYtInfo($urls[0]);
        if($spInfo == NULL && $ytInfo == NULL){
            $_SESSION["error"] = "I haven't found anything, all seems suspicious";
            unset($_SESSION["cover"]);
            unset($_SESSION["artistName"]);
            unset($_SESSION["musicName"]);
            unset($_SESSION["description"]);
            unset($_SESSION["mp3"]);
            header("Location: ../index.php");
        }
        if($spInfo == NULL){
            if(empty($ytInfo[0])){
                unset($_SESSION["cover"]);
            }else{
                $_SESSION["cover"] = $ytInfo[0];
            }

            if(empty($ytInfo[1])){
                unset($_SESSION["artistName"]);
            }else{
                $_SESSION["artistName"] = $ytInfo[1];
            }

            if(empty($ytInfo[2])){
                unset($$_SESSION["musicName"]);
            }else{
                $_SESSION["musicName"] = $ytInfo[2];
            }

            $ytMp3 = getYoutubeMp3($urls[0], $_SESSION["musicName"]);
            if(empty($ytMp3)){
                unset($_SESSION["mp3"]);
            }else{
                $_SESSION["mp3"] = $ytMp3;
            }

        }else{
            $_SESSION["cover"] = $spInfo[0];
            $_SESSION["artistName"] = $spInfo[1];
            $_SESSION["musicName"] = $spInfo[2];
            if(str_contains(mime_content_type("../mp3/" . $spInfo[2] . ".mp3"), "audio")){
                $_SESSION["mp3"] = "mp3/" . $spInfo[2] . ".mp3";
            }else{
                $ytMp3 = getYoutubeMp3($urls[0], $_SESSION["musicName"]);
                if(empty($ytMp3)){
                    unset($_SESSION["mp3"]);
                }else{
                    $_SESSION["mp3"] = $ytMp3;
                }
            }
        }
        /**
         * SET DESC
         */
        if(empty($ytInfo[3])){
            unset($_SESSION["description"]);
        }else{
            $_SESSION["description"] = $ytInfo[3];
        }

        /**
         * SETUP QUEUE
         */
        if(empty($urls[2])){
        }else{
            $_SESSION["searchQueue"] = [true, $urls[2]];
        }
    }
    header("Location: ../index.php");
}