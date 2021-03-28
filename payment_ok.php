<?php

require("functions.php");
// Target Sonderzeichen raus

$fenster = str_replace("+", "", $u_nick);
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
<HEAD><TITLE><?php echo $body_titel . "_SMS-Payment"; ?></TITLE><meta charset="utf-8">
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
        function toggle(tostat ) {
                for(i=0; i<document.forms["mailbox"].elements.length; i++) {
                     e = document.forms["mailbox"].elements[i];
                     if ( e.type=='checkbox' )
                         e.checked=tostat;
                }
        }
</SCRIPT>
<?php echo $stylesheet; ?>
</HEAD> 
<?php
$body_tag = "<BODY BGCOLOR=\"$farbe_mini_background\" ";
if (strlen($grafik_mini_background) > 0) {
    $body_tag = $body_tag . "BACKGROUND=\"$grafik_mini_background\" ";
}
$body_tag = $body_tag . "TEXT=\"$farbe_mini_text\" "
    . "LINK=\"$farbe_mini_link\" " . "VLINK=\"$farbe_mini_vlink\" "
    . "ALINK=\"$farbe_mini_vlink\">\n";
echo $body_tag;

// Menü als erstes ausgeben
$box = $ft0 . "Zahlung erfolgreich" . $ft1;
$text = "Die Zahlung der SMS war erfolgreich. Vielen Dank<BR>";
$text .= 'Bitte klicken Sie <a href="javascript:window.close()">hier</a> um das Fenster zu schliessen.';
show_box2($box, $text, "100%");

?>
</BODY></HTML>
