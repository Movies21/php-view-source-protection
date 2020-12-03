<?php
class vsp {
    /* 
    penjelasannya mirip ya seperti viewsource_protect
    untuk memahaminya silahkan melihat file yang satu lagi, penjelasannya jelas disana.
    */
    function loader($dir,$time){
        $this->loader       = $dir;
        $this->loader_time  = $time;
    }

    function render_opt($render,$path,$key){
        $this->render = $render;
        $this->path = $path;
        $this->keys = $key;
    }
    function render_load(){
        if(isset($_POST['flock_load'])){
            if($_POST['flock_load'] == $this->keys){
                include $this->path;
                die();
            };
        };
    }
    function inspect($var){
        /*
        kode javascript didapatkan dari stackoverflow 
        https://stackoverflow.com/a/53511363
        thanks to PK-1825
        */
        $this->inspect = '
            <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha256-4+XzXVhsDmqanXGHaHvgh1gMQKX40OUvDEBTu8JcmNs=" crossorigin="anonymous"></script>
            <script type="text/javascript">
            document.addEventListener("contextmenu", event => event.preventDefault());
            window.onload = function () {
            document.addEventListener("contextmenu", function (e) {
            e.preventDefault();
            }, false);
            document.addEventListener("keydown", function (e) {
            //document.onkeydown = function(e) {
            // "I" key
            if (e.ctrlKey && e.shiftKey && e.keyCode == 73) {
            disabledEvent(e);
            }
            // "J" key
            if (e.ctrlKey && e.shiftKey && e.keyCode == 74) {
            disabledEvent(e);
            }
            // "S" key + macOS
            if (e.keyCode == 83 && (navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey)) {
            disabledEvent(e);
            }
            // "U" key
            if (e.ctrlKey && e.keyCode == 85) {
            disabledEvent(e);
            }
            // "F12" key
            if (event.keyCode == 123) {
            disabledEvent(e);
            }
            }, false);
            function disabledEvent(e) {
            if (e.stopPropagation) {
            e.stopPropagation();
            } else if (window.event) {
            window.event.cancelBubble = true;
            }
            e.preventDefault();
            return false;
            }
            }
            </script>
            ';
        if($var == true){
            echo $this->inspect;
        }
    }

    function start(){
        session_start();
        if($_GET['load'] == 'on'){
            if(isset($this->loader)){
                include $this->loader;
                $time = $this->loader_time;
            }else{
                $time = 0;
            }
            $_SESSION["protect"] = true;
            $link_situs = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $link_situs = preg_replace('/(&|\?)'.preg_quote('load').'=[^&]*$/', '', $link_situs);
            $link_situs = preg_replace('/(&|\?)'.preg_quote('load').'=[^&]*&/', '$1', $link_situs);
            header("Refresh:$time;url=$link_situs");
            die();
        }else{
            switch($_SESSION['protect']){
                case false:
                    header("location: ?load=on");
                    die();
                    break;
                default :
                    $_SESSION['protect'] = false;
                    if($this->render == true){
                        echo '
<vsprender>
';
include $this->loader;
echo '
<!-- this is render stage -->
</vsprender>
<script id="load_1" src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script id="load_2"type="text/javascript">
$.ajax({
url : "?",
method : "POST",
data: {
    "flock_load": "'.$this->keys.'",
},
success: function(data){
    $("html").html(data);
    $("#load_1").remove();
    $("#load_2").remove();
}
});
</script>
';
                        die();
                    }else{
                        include $this->path;
                    }
            }
        }
    }

    function stop(){
        session_start();
        $_SESSION['protect'] = false;
    }

    function start_render($render,$path,$key){
    }
}
