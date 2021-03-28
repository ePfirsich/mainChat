<?php

require("functions.php");
require_once("functions.php-home.php");
require("functions.php-hash.php");

$fenster = str_replace("+", "", $ui_userid);
$fenster = str_replace("-", "", $fenster);
$fenster = str_replace("ä", "", $fenster);
$fenster = str_replace("ö", "", $fenster);
$fenster = str_replace("ü", "", $fenster);
$fenster = str_replace("Ä", "", $fenster);
$fenster = str_replace("Ö", "", $fenster);
$fenster = str_replace("Ü", "", $fenster);
$fenster = str_replace("ß", "", $fenster);

?>
<HTML>
<HEAD><TITLE><?php echo $body_titel . "_Home"; ?></TITLE><meta charset="utf-8">
<SCRIPT>
        window.focus()
        function win_reload(file,win_name) {
                win_name.location.href=file;
        }
        function opener_reload(file,frame_number) {
                opener.parent.frames[frame_number].location.href=file;
        }
        function neuesFenster(url,name) {
                hWnd=window.open(url,name,"resizable=yes,scrollbars=yes,width=300,height=580");
        }
        function neuesFenster2(url) {
                hWnd=window.open(url,"<?php echo "640_" . $fenster; ?>","resizable=yes,scrollbars=yes,width=780,height=580");
        }
</SCRIPT>
<?php echo $stylesheet; ?>
</HEAD> 
<?php

// Pfad auf Cache
$cache = "home_bild";

if (!checkhash($hash, $ui_userid)) {
    print "<B>Fehler!</B> Hash stimmt nicht!";
    exit;
}

if (!$ui_userid)
    $ui_userid = $u_id;

if (isset($preview) && $preview == "yes") {
    id_lese($preview_id);
}

if (!isset($farben))
    $farben = "";

if (isset($u_id) && $ui_userid == $u_id) {
    zeige_home($ui_userid, TRUE, $farben);
} else {
    zeige_home($ui_userid, FALSE, $farben);
}

?>
</HTML>