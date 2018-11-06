<?php
if ($_SERVER['REQUEST_METHOD']=='POST'){
    $GLOBALS['key'] = $_POST['key'];
}
$GLOBALS['loggedIn'] = isset($GLOBALS['key'])&&($GLOBALS['key'] === 'Key');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Erstkommunion</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>
        var curr = 0;
        <?php
        #echo "console.log('".json_encode($_POST)."');";
        function endsWith($haystack, $needle){
            $length = strlen($needle);
            if ($length == 0) {
                return true;
            }
            return (substr($haystack, -$length) === $needle);
        }
        $GLOBALS['playlist']=array();
        $dir = './audio/';
        $i = 1;
        foreach (scandir($dir) as $item) {
            if (strlen($item)>4 && endsWith($item,'.mp3')) {
                $onclick = "onclick=\"set(this,'".strval($dir.$item)."')\"";
                $name = str_replace('.mp3','',$item);
                if (strpos($name,'_')===0){
                    $name = substr($name,strpos($name,'_')+1);
                }
                array_push($GLOBALS['playlist'],"<tr id='playlist-$i' $onclick><td>".$i++."</td><td>$name</td></tr>\n");
            }
        }
        ?>
        function keydown(e){
            var player = document.getElementById('player');
            if(player instanceof HTMLAudioElement) {
                //console.log(player.seekable.end(0));
                if (e.which === 32) {
                    playpause();
                } else if (e.which === 39) {
                    if (player.currentTime + 5 > player.duration) {
                        player.currentTime = player.duration;
                    } else {
                        player.currentTime += 5;
                    }
                } else if (e.which === 37) {
                    if (player.currentTime - 5 < 0) {
                        player.currentTime = 0;
                    } else {
                        player.currentTime -= 5;
                    }
                } else if (e.which === 40) {
                    getNext();
                }
            }
        }
        function playEvent(e) {
            //console.log(e);
        }
        function pauseEvent(e) {
            //console.log(e);
        }
        function play(){
            var player=document.getElementById('player');
            if(player instanceof HTMLAudioElement)player.play();
        }
        function pause() {
            var player=document.getElementById('player');
            if(player instanceof HTMLAudioElement)player.pause();
        }
        function playpause() {
            var player = document.getElementById('player');
            if(player instanceof HTMLAudioElement) {
                if (player.paused) {
                    play();
                } else {
                    pause();
                }
            }
        }
        function set(caller,name) {
            document.getElementById('player-source').src = name;
            var oldE = $('#playlist-'+curr);
            oldE.removeClass('active');
            var newE = $('#'+caller.id);
            newE.addClass('active');
            curr = Number(String(caller.id).replace('playlist-',''));
            player_div = document.getElementById('player-div');
            html=player_div.innerHTML;
            player_div.innerHTML=html;
            play();
        }
        function getNext(){
            $('#playlist-'+((curr+1 > <?php echo count($GLOBALS['playlist']) ?>) ? 1 : curr+1)).click();
        }
    </script>
    <link rel="stylesheet" href="erstkommunion-musik.css" />
</head>
<body>
<article>
    <div style="margin-left: auto; margin-right: auto; margin-top: 15%; width: 60%">
        <h2>Musik f&uuml;r die Erstkommunion</h2>
        <?php
            if ($GLOBALS['loggedIn']) {
                echo "
                <div id=\"player-container\">
                    <div id=\"player-div\" class=\"center\">
                        <audio id=\"player\" preload=\"metadata\" controls=\"controls\" onended=\"getNext()\">
                            <source id=\"player-source\" src=\"\" type=\"audio/mpeg\" />
                            Ihr browser kennt das audio element nicht.
                        </audio>
                        <script>
                            document.getElementById('player').onpause=pauseEvent; 
                            document.getElementById('player').onplay=playEvent;
                        </script>
                    </div>
                    <div id=\"playlist\" class=\"center\">
                        <table>";
                foreach ($GLOBALS['playlist'] as $item) {
                    echo $item;
                }
                echo "
                        </table>
                    </div>
                    <br />
                    <div class=\"center\">
                        <button onclick=\"getNext()\" name='next'>Nächstes</button>
                        <button><a style=\"color: black; text-decoration: none\" href=\"<?php echo $dir ?>erstkommunion.zip\" download>Download</a></button>
                    </div>
                    <br />
                </div>";
            } else {
                echo "
                <div id=\"login-div\" style='margin-left: auto;margin-right: auto; width: 315px'>
                    <form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">
                        <label for='key'>Schl&uuml;ssel:</label>
                        <input type=\"password\" name=\"key\" />
                        <button type=\"submit\">Best&auml;tigen</button>
                    </form>
                </div>";
            }
        ?>
    </div>
</article>
<script>
    document.body.onkeydown = keydown;
    bodyHTML=document.body.innerHTML;
    document.body.innerHTML=bodyHTML;
</script>
</body>
</html>