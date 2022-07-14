<?php include "head/head.php"; ?>
<?php session_start(); ?>
<body>
    <div class="dFrame">
        <div class="center">
            <form id="searchForm" action="head/search.php" method="POST">
                <div class="marginB">
                    <label class="searchLabel" for="artistName">Artist name : </label>
                    <input type="text" id="artistName" name="artistName" required minlength="1">
                </div>
                <div class="marginB">
                    <label class="searchLabel" for="musicName">Music name : </label>
                    <input type="text" id="musicName" name="musicName" required minlength="1">
                </div>
                <div class="marginB">
                    <input class="play" id="searchBtn" type="submit" value="Search">
                    <!-- Thanks to https://loading.io/css/ for css animations -->
                    <div id="loading">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
                <div>
                    <input type="checkbox" id="checkbox" name="checkbox">
                    <label class="white" for="checkbox">100% accuracy</label>
                </div>
                <p class="message none"></p>
            </form>
            <!-- Thanks to https://codepen.io/kylelavery88/pen/vmqXrm for music player idea -->
        </div>
        <div class="sectionTrack">
            <div class="trackInfo">
                <div class="controls">
                    <div class="flex">
                        <img id="albumCover" src="<?php if(isset($_SESSION["cover"])){ echo $_SESSION["cover"]; }else{ echo "img/ninho.jfif"; }; ?>" class="imgSizeCover" alt="album cover">
                        <div class="textInfo">
                            <div class="artistAccount flex">
                                <img id="ppArtist" src="<?php if(isset($_SESSION["cover"])){ echo $_SESSION["cover"]; }else{ echo "img/ninho.jfif"; }; ?>" alt="">
                                <h4 id="artistNameQueue"><?php if(isset($_SESSION["artistName"])){ echo $_SESSION["artistName"]; }else{ echo "ninho"; }; ?></h4>
                            </div>
                            <div class="musicTitle">
                                <h2 id="titleInQueue"><?php if(isset($_SESSION["musicName"])){ echo $_SESSION["musicName"]; }else{ echo "jefe"; }; ?></h2>
                            </div>
                            <div class="musicInfo">
                                <span><?php if(isset($_SESSION["type"])){ echo $_SESSION["type"] ;}else{ echo "Probably Kpop"; };?></span>
                                <span><?php if(isset($_SESSION["label"])){ echo "©" . $_SESSION["label"] ;}else{ echo "© Lysun"; };?></span>
                            </div>
                            <div class="musicDescription">
                                <p id="descriptionQueue"><?php if(isset($_SESSION["description"])){ echo substr($_SESSION["description"], 0, 300); }else{ echo substr("Jefe (stylisé JEFE) est le troisième album studio du rappeur français Ninho, sorti le 3 décembre 2021 sous le label Rec. 1181.", 0, 300); } ?></p>
                            </div>
                        </div>
                    </div>
                    <?php if(isset($_SESSION["mp3"])){ ?><div class="flex">
                        <div id="play" class="play">
                            <i id="player" class="fa-solid fa-play"></i>
                        </div>
                        <div id="like" class="like">
                            <i class="fa-solid fa-heart"></i>
                        </div>
                        <div class="unlike">
                            <i class="fas fa-heart-broken"></i>
                        </div>
                    </div><?php }?>
                    
                </div>
            </div>
        </div>
        <?php if(isset($_SESSION["mp3"])){ ?><div class="audioSection center">
            <audio id="audio" class="audio" controls title="<?php if(isset($_SESSION["musicName"])){ echo $_SESSION["musicName"] ;}else{ echo "Probably Kpop"; };?>">
                <source src="<?php if(isset($_SESSION["mp3"])){ echo $_SESSION["mp3"]; };?>" type="audio/ogg">
                <source src="<?php if(isset($_SESSION["mp3"])){ echo $_SESSION["mp3"]; };?>"type="audio/mpeg">
            </audio>
            <script>
                /**
                 * infoMusic is an array who translate php $_SESSION variables into js variables for create a queue
                 */
                var infoMusic = []
                infoMusic.push(<?php echo json_encode($_SESSION["cover"]); ?>)
                infoMusic.push(<?php echo json_encode($_SESSION["artistName"]); ?>)
                infoMusic.push(<?php echo json_encode($_SESSION["musicName"]); ?>)
            </script>
        </div>
        <?php }?>
<?php if(isset($_SESSION["searchQueue"][0])) { ?><div class="center marginB">
            <form id="queueForm">
                <input style="display:none" type="text" name="spPlaylist" value="<?php echo $_SESSION["searchQueue"][1]; ?>"><?php if(isset($_SESSION["queue"])) { ?>
                    <i class="fa-solid fa-angle-left queueBtnPrevious" id="searchBtnPrevious"></i>
                <?php } ?>
                <?php if(isset($_SESSION["error"])) { echo "<p id='error'>" . $_SESSION["error"]. "</p>"; }else{ ?>
                    <input class="play queueBtn" id="searchBtn" type="submit" value="Find queue">
                <?php }?>
                <?php if(isset($_SESSION["queue"])) { ?>
                    <i class="fa-solid fa-angle-right queueBtnNext" id="searchBtnNext"></i>
                <?php } ?>
            </form>
            <div class="center">
                <div id="loadingQueue">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
            <p class="queue none"></p>
        </div><?php } ?>

        <?php if(isset($_SESSION["queue"])) { ?><div class="albumTracklist">
            <ol>
                <?php for($i = 6; $i < 9; $i++){ ?><li class="<?php echo "piste" . $i - 5; ?> searchQueueBtn musicQueue">
                    <span>
                    <?php echo $_SESSION["queue"][$i]; ?>
                    </span>
                    <span>
                    <?php echo $_SESSION["queue"][$i + 3]; ?>
                    </span>
                </li>
                <?php } ?>           
            </ol>
        </div><?php } ?>
    
    </div>
    <script>
        /**
         * Same as infoMusic, queue variable translate php $_SESSION variables into js array for queue
         */
        var queue = [];
        <?php foreach ($_SESSION["queue"] as $info): ?>
            queue.push(<?php echo json_encode($info); ?>);
        <?php endforeach; ?>
            queue.push(<?php echo json_encode($_SESSION["cover"]); ?>);
            queue.push(<?php echo json_encode($_SESSION["artistName"]); ?>);
            queue.push(<?php echo json_encode($_SESSION["musicName"]); ?>);
            queue.push(<?php echo json_encode($_SESSION["mp3"]); ?>);
    </script>
</body>
</html>