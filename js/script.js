/**
 * 
 * @returns array of loading classes names.
 */
function getLoadingClass() {
    return loadingClass = [
        "lds-circle",
        "lds-dual-ring",
        "lds-ring",
        "lds-hourglass",
        "lds-ripple",
    ];
}

/**
 * 
 * @returns array of string messages.
 */
function getMessage() {
    return message = [
        "It's hard to be a program you know?",
        "You can make a coffee",
        "Even Drake has to wait sometimes",
        "Test de Julien à nouveau",
        "I found something ! not for you",
    ];
}

/**
 * 
 * @param { minimal number } min 
 * @param { maximal number } max 
 * @returns random number between interval.
 */
function randomIntFromInterval(min, max) { // min and max included 
    return Math.floor(Math.random() * (max - min + 1) + min)
}

/**
 * set audio at 75% volume and set mediaMetadata for music found by the script.
 */
$( document ).ready(function() {
    $('.audio').prop("volume", 0.75);
    if($( "#searchBtnNext" ).hasClass('queueBtnNext')){
        $( ".play.queueBtn" ).addClass("none");
    }else{
        if ("mediaSession" in navigator){
            navigator.mediaSession.metadata = new MediaMetadata({
                title: infoMusic[2],
                artist: infoMusic[1],
                album: "Lysun",
                artwork: [{src: infoMusic[0]}]
            });
        }
    }
});

/**
 * when #searchForm are submited show random css animation and message to the user.
 */
$( "#searchBtn" ).on( "click", function() {
    $( "#searchForm" ).submit(function(event) {
        var rndInt = randomIntFromInterval(0, 4);
        var loadingClass = getLoadingClass();
        $( "#loading" ).removeClass();
        $( "#loading" ).addClass( loadingClass[rndInt]);
        var message = getMessage();
        $( ".message" ).removeClass("none");
        setInterval(function() {
            var rndInt = randomIntFromInterval(0, 4);
            $( ".message" ).html(message[rndInt]);
        }, 5000);
        });
  });

/**
 * play button.
 */
  $( "#play" ).on( "click", function() {
    if($( "#player" ).hasClass('fa-solid fa-play')){
        $( "#player" ).removeClass();
        $( "#player" ).addClass("fa-solid fa-pause");
        $( ".audio" ).trigger("play");
    }else{
        $( "#player" ).removeClass();
        $( "#player" ).addClass("fa-solid fa-play");
        $( ".audio" ).trigger("pause");
    }
});

/**
 * like button.
 */
$( ".like" ).on( "click", function() {
    
    if($( ".like" ).hasClass('red')){
        $( ".like" ).removeClass("red animate__animated animate__heartBeat");
        $( ".unlike" ).removeClass("red animate__animated animate__heartBeat");
    }else{
        $( ".like" ).addClass("red animate__animated animate__heartBeat");
        $( ".unlike" ).removeClass("red animate__animated animate__heartBeat");
    }
});

/**
 * unlike button.
 */
$( ".unlike" ).on( "click", function() {
    if($( ".unlike" ).hasClass('red')){
        $( ".unlike" ).removeClass("red animate__animated animate__heartBeat");
        $( ".like" ).removeClass("red animate__animated animate__heartBeat");
    }else{
        $( ".unlike" ).addClass("red animate__animated animate__heartBeat");
        $( ".like" ).removeClass("red animate__animated animate__heartBeat");
    }
});

/**
 * syncronize audio element and play button.
 */
$("audio").on({
    play:function(){ // the audio is playing!
        $( "#player" ).removeClass();
        $( "#player" ).addClass("fa-solid fa-pause");
    },
    pause:function(){ // the audio is paused!
        $( "#player" ).removeClass();
        $( "#player" ).addClass("fa-solid fa-play");
    },
})

/**
 * show alert meassage when the checkbox are checked.
 */
$( "#checkbox" ).change(function() {
    if(this.checked) {
        alert("Don't make a mistake in the music and artist name !")
    }else{
        //I'm not checked
    }
});

/**
 * when .queueBtn and form assosiate are submited, submit a form who not realod the page.
 */
$( ".queueBtn" ).on( "click", function() {
    $('form').on('submit', function (e) {
        console.log($('form').serialize());
        $.ajax({
          type: 'post',
          url: './head/queue.php',
          data: $('form').serialize(),
          success: function () {
          }
        });
        e.preventDefault();
    });
    $( ".queue" ).removeClass("none");
    $( ".queue" ).html('The queue is being processed, it appears at the end of your music !')
    var rndInt = randomIntFromInterval(0, 4);
    var loadingClass = getLoadingClass();
    $( "#loadingQueue" ).removeClass();
    $( "#loadingQueue" ).addClass( loadingClass[rndInt]);
});

/**
 * when the music are ended, change music to the next with the queue variables created in index.php and create a new mediaMetaData.
 */
$('#audio').on('ended', function() {
    if($("#searchBtnNext").hasClass("queueBtnNext")){
        var musicInQueue = [queue[20], queue[0], queue[1], queue[2]]
        var coverInQueue = [queue[17], queue[3], queue[4], queue[5]]
        var musicNameInQueue = [queue[19], queue[6], queue[7], queue[8]]
        var artistNameInQueue = [queue[18], queue[12], queue[13], queue[14]]
        var i = 0;
        if(queue[16] === 3){
            queue[16] = 0;
            i = queue[16];
        }else{
            queue[16] += 1;
            i = queue[16];
        }
        $("#descriptionQueue").replaceWith("<p>I don't have any more info dude.</p>");
        $("#titleInQueue").replaceWith("<h2 id='titleInQueue'>" + musicNameInQueue[i] + "</h2>");
        $("#artistNameQueue").replaceWith("<h4 id='artistNameQueue'>" + artistNameInQueue[i] + "</h4>");
        $("#albumCover").attr("src", coverInQueue[i]);
        $("#ppArtist").attr("src", coverInQueue[i]);
        $("#audio").attr("src", musicInQueue[i]);
        if ("mediaSession" in navigator){
            navigator.mediaSession.metadata = new MediaMetadata({
                title: musicNameInQueue[i],
                artist: artistNameInQueue[i],
                album: "Lysun",
                artwork: [{src: coverInQueue[i]}]
            });
        }
        $( ".audio" ).trigger("play");
    }else{
        location.reload();
    }
    
 });

 /**
 * when the nextBtn is cliked, change music to the next with the queue variables created in index.php and create a new mediaMetaData.
 */
 $( "#searchBtnNext" ).on( "click", function() {
    var musicInQueue = [queue[20], queue[0], queue[1], queue[2]]
    var coverInQueue = [queue[17], queue[3], queue[4], queue[5]]
    var musicNameInQueue = [queue[19], queue[6], queue[7], queue[8]]
    var artistNameInQueue = [queue[18], queue[12], queue[13], queue[14]]
    var i = 0;
    if(queue[16] === 3){
        queue[16] = 0;
        i = queue[16];
    }else{
        queue[16] += 1;
        i = queue[16];
    }
    if(coverInQueue[i] == null){
        coverInQueue[i] = "./img/ninho.jfif"
    }
    $("#descriptionQueue").replaceWith("<p>I don't have any more info dude.</p>");
    $("#titleInQueue").replaceWith("<h2 id='titleInQueue'>" + musicNameInQueue[i] + "</h2>");
    $("#artistNameQueue").replaceWith("<h4 id='artistNameQueue'>" + artistNameInQueue[i] + "</h4>");
    $("#albumCover").attr("src", coverInQueue[i]);
    $("#ppArtist").attr("src", coverInQueue[i]);
    $("#audio").attr("src", musicInQueue[i]);
    if ("mediaSession" in navigator){
        navigator.mediaSession.metadata = new MediaMetadata({
            title: musicNameInQueue[i],
            artist: artistNameInQueue[i],
            album: "Lysun",
            artwork: [{src: coverInQueue[i]}]
        });
    }
    $( ".audio" ).trigger("play");
 });

 /**
  * when the previousBtn is cliked, change music to the next with the queue variables created in index.php and create a new mediaMetaData.
  */
 $( "#searchBtnPrevious" ).on( "click", function() {
    var musicInQueue = [queue[20], queue[0], queue[1], queue[2]]
    var coverInQueue = [queue[17], queue[3], queue[4], queue[5]]
    var musicNameInQueue = [queue[19], queue[6], queue[7], queue[8]]
    var artistNameInQueue = [queue[18], queue[12], queue[13], queue[14]]
    var i = 0;
    if(queue[16] === 0){
        queue[16] = 3;
        i = queue[16];
    }else{
        queue[16] -= 1;
        i = queue[16];
    }

    if(coverInQueue[i] == null){
        coverInQueue[i] = "./img/ninho.jfif"
    }
    $("#descriptionQueue").replaceWith("<p>I don't have any more info dude.</p>");
    $("#titleInQueue").replaceWith("<h2 id='titleInQueue'>" + musicNameInQueue[i] + "</h2>");
    $("#artistNameQueue").replaceWith("<h4 id='artistNameQueue'>" + artistNameInQueue[i] + "</h4>");
    $("#albumCover").attr("src", coverInQueue[i]);
    $("#ppArtist").attr("src", coverInQueue[i]);
    $("#audio").attr("src", musicInQueue[i]);
    if ("mediaSession" in navigator){
        navigator.mediaSession.metadata = new MediaMetadata({
            title: musicNameInQueue[i],
            artist: artistNameInQueue[i],
            album: "Lysun",
            artwork: [{src: coverInQueue[i]}]
        });
    }
    $( ".audio" ).trigger("play");
 });

 /**
  * when the piste1 is cliked, change music to the next with the queue variables created in index.php and create a new mediaMetaData.
  */
 $( ".piste1" ).on( "click", function() {
    var musicInQueue = [queue[20], queue[0], queue[1], queue[2]]
    var coverInQueue = [queue[17], queue[3], queue[4], queue[5]]
    var musicNameInQueue = [queue[19], queue[6], queue[7], queue[8]]
    var artistNameInQueue = [queue[18], queue[12], queue[13], queue[14]]
    queue[16] = 1;
    var i = queue[16]
    if(coverInQueue[i] == null){
        coverInQueue[i] = "./img/ninho.jfif"
    }
    $("#descriptionQueue").replaceWith("<p>I don't have any more info dude.</p>");
    $("#titleInQueue").replaceWith("<h2 id='titleInQueue'>" + musicNameInQueue[i] + "</h2>");
    $("#artistNameQueue").replaceWith("<h4 id='artistNameQueue'>" + artistNameInQueue[i] + "</h4>");
    $("#albumCover").attr("src", coverInQueue[i]);
    $("#ppArtist").attr("src", coverInQueue[i]);
    $("#audio").attr("src", musicInQueue[i]);
    if ("mediaSession" in navigator){
        navigator.mediaSession.metadata = new MediaMetadata({
            title: musicNameInQueue[i],
            artist: artistNameInQueue[i],
            album: "Lysun",
            artwork: [{src: coverInQueue[i]}]
        });
    }
    $( ".audio" ).trigger("play");
 })

 /**
  * when the piste2 is cliked, change music to the next with the queue variables created in index.php and create a new mediaMetaData.
  */
 $( ".piste2" ).on( "click", function() {
    var musicInQueue = [queue[20], queue[0], queue[1], queue[2]]
    var coverInQueue = [queue[17], queue[3], queue[4], queue[5]]
    var musicNameInQueue = [queue[19], queue[6], queue[7], queue[8]]
    var artistNameInQueue = [queue[18], queue[12], queue[13], queue[14]]
    queue[16] = 2;
    var i = queue[16]
    if(coverInQueue[i] == null){
        coverInQueue[i] = "./img/ninho.jfif"
    }
    $("#descriptionQueue").replaceWith("<p>I don't have any more info dude.</p>");
    $("#titleInQueue").replaceWith("<h2 id='titleInQueue'>" + musicNameInQueue[i] + "</h2>");
    $("#artistNameQueue").replaceWith("<h4 id='artistNameQueue'>" + artistNameInQueue[i] + "</h4>");
    $("#albumCover").attr("src", coverInQueue[i]);
    $("#ppArtist").attr("src", coverInQueue[i]);
    $("#audio").attr("src", musicInQueue[i]);
    if ("mediaSession" in navigator){
        navigator.mediaSession.metadata = new MediaMetadata({
            title: musicNameInQueue[i],
            artist: artistNameInQueue[i],
            album: "Lysun",
            artwork: [{src: coverInQueue[i]}]
        });
    }
    $( ".audio" ).trigger("play");
})

/**
  * when the piste3 is cliked, change music to the next with the queue variables created in index.php and create a new mediaMetaData.
  */
$( ".piste3" ).on( "click", function() {
    var musicInQueue = [queue[20], queue[0], queue[1], queue[2]]
    var coverInQueue = [queue[17], queue[3], queue[4], queue[5]]
    var musicNameInQueue = [queue[19], queue[6], queue[7], queue[8]]
    var artistNameInQueue = [queue[18], queue[12], queue[13], queue[14]]
    queue[16] = 3;
    var i = queue[16];
    if(coverInQueue[i] == null){
        coverInQueue[i] = "./img/ninho.jfif"
    }
    $("#descriptionQueue").replaceWith("<p>I don't have any more info dude.</p>");
    $("#titleInQueue").replaceWith("<h2 id='titleInQueue'>" + musicNameInQueue[i] + "</h2>");
    $("#artistNameQueue").replaceWith("<h4 id='artistNameQueue'>" + artistNameInQueue[i] + "</h4>");
    $("#albumCover").attr("src", coverInQueue[i]);
    $("#ppArtist").attr("src", coverInQueue[i]);
    $("#audio").attr("src", musicInQueue[i]);
    if ("mediaSession" in navigator){
        navigator.mediaSession.metadata = new MediaMetadata({
            title: musicNameInQueue[i],
            artist: artistNameInQueue[i],
            album: "Lysun",
            artwork: [{src: coverInQueue[i]}]
        });
    }
    $( ".audio" ).trigger("play");
})