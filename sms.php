<?php

require("functions.php");
require_once("functions.php-func-sms.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, u_level, o_js
id_lese($id);

// Kopf ausgeben
?>
<HTML>
<HEAD><TITLE><?php echo $body_titel . "_SMS"; ?></TITLE><META CHARSET=UTF-8>
<SCRIPT>
        window.focus()
        function win_reload(file,win_name) {
                win_name.location.href=file;
}
        function opener_reload(file,frame_number) {
                opener.parent.frames[frame_number].location.href=file;
}
</SCRIPT>
<?php echo $stylesheet; ?>
</HEAD> 
<?php

function erzwingeNeuePin()
{
    
    global $conn, $u_id;
    
    if (!$u_id)
        return;
    
    $sql = "select u_sms_extra from user where u_id=$u_id";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0)
        $sms_extra = unserialize(mysql_result($result, 0, "u_sms_extra"));
    
    //falls pin vor mehr als 24 stunden vergeben wurde, muss neue erzwungen werden
    if ($sms_extra['pintime'] && ($sms_extra['pintime'] + 86400) < date("U")) {
        
        $g['u_sms_extra'] = "";
        
        schreibe_db("user", $g, $u_id, "u_id");
        
    }
    
}

function CheckHandynummerVorhanden()
{
    
    global $conn, $u_id;
    
    $sql = "select ui_handy from userinfo where ui_userid = $u_id";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0)
        $handynr = mysql_result($result, 0, "ui_handy");
    
    if ($handynr)
        return true;
    else return false;
    
}

function PinvergabeFormular()
{
    global $f, $http_host, $id, $chat;
    $box = "PIN vergeben";
    
    $text .= "Damit Sie SMS aus dem $chat versenden können, muss zunächste einmal Ihre Handynummer geprüft werden.";
    $text .= "Hierzu bekommen Sie eine PIN aufs Handy geschickt. Sobald Sie die PIN korrekt eingegeben haben, dürfen Sie aufladen.";
    $text .= "<BR>Hier nochmal Ihre Handynummer: <b>$f[ui_handy]</b><BR>";
    $text .= "Sollte die Nummer falsch sein, so können Sie diese <a href=\"profil.php?id=$id&http_host=$httphost&aktion=aendern\">hier</a> korrigieren</a>";
    $text .= "<form action=\"sms.php\" method=post><input type=hidden name=\"id\" value=\"$id\"><input type=hidden name=\"http_host\" value=\"$http_host\">";
    $text .= "<input type=hidden name=\"aktion\" value=\"pinsenden\"><input type=submit value=\"PIN senden\">";
    show_box2($box, $text, "100%");
}

function PinEingabeFormular($message)
{
    global $f, $http_host, $id;
    $box = "PIN eingeben";
    $text = $message . "<BR>";
    $text .= "Bitte geben Sie nun die PIN ein, die Sie per SMS erhalten haben...<BR>";
    $text .= "<form action=\"sms.php\" method=post><input type=hidden name=\"id\" value=\"$id\"><input type=hidden name=\"http_host\" value=\"$http_host\">";
    $text .= "<input name=\"pin\" value=\"\" size=4 maxlength=4>&nbsp;<input type=submit value=\"PIN eingeben\">";
    show_box2($box, $text, "100%");
}

function ZeigSMSHistory($u_id)
{
    GLOBAL $conn, $farbe_tabelle_kopf, $farbe_tabelle_zeile1, $farbe_tabelle_zeile2;
    // Hier die zuletzt versandten 5 SMS ausgeben
    $query = "SELECT date_format(s_zeit,'%d.%m.%y %H:%i:%s') as zeit ,s_an_user_id ,s_text FROM sms WHERE s_von_user_id = '$u_id' order by s_zeit desc LIMIT 0,5";
    $result = mysqli_query($mysqli_link, $query);
    if (mysql_numrows($result) > 0) {
        $atext = "<table border=0 cellpadding=2 cellspacing=0 width=100%>\n";
        $atext .= "<TR BGCOLOR=\"$farbe_tabelle_kopf\"\"><TD>Wann?</TD><TD>An wen?</TD><TD>Nachricht:</TD></TR>\n";
        $farbe = $farbe_tabelle_zeile1;
        while ($a = mysqli_fetch_array($result)) {
            $user_id = $a['s_an_user_id'];
            
            $query = "SELECT user.* FROM user WHERE u_id=$user_id ";
            $result2 = mysqli_query($conn, $query);
            $b = mysqli_fetch_array($result2);
            @mysqli_free_result($result2);
            
            $a['s_an_user_id'] = $b['u_nick'];
            $atext .= "<tr BGCOLOR=\"$farbe\">";
            $atext .= "<td><small>$a[zeit]</small></td>";
            $atext .= "<td><small>$a[s_an_user_id]</small></td>";
            $atext .= "<td><small>$a[s_text]</small></td>";
            $atext . "</tr>\n";
            if ($farbe == $farbe_tabelle_zeile1) {
                $farbe = $farbe_tabelle_zeile2;
            } else {
                $farbe = $farbe_tabelle_zeile1;
            }
        }
        $atext .= "</table>\n";
        $box = "SMS History (Ihre fünf zuletzt versandten SMS)";
        show_box2($box, $atext, "100%");
    }
    @mysqli_free_result($result);
}

function PINErrorFormular()
{
    global $a, $id, $http_host;
    $box = $ft0 . "Fehler!" . $ft1;
    unset($text);
    $text = "Sie haben bei der PIN-Eingabe mehr als 3 Fehler in den letzten 24h gemacht! Sie müssen nun 24h warten, dann können Sie sich eine neue PIN aufs Handy schicken lassen!";
    show_box2($box, $text, "100%");
    echo "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><BR>\n";
}

function SMSEmpfangFormular()
{
    global $a, $id, $http_host, $chat, $ft0, $ft1;
    $box = $ft0 . "SMS" . $ft1;
    unset($text);
    if ($a['u_sms_ok'] == "N") {
        $selj = "";
        $seln = "SELECTED";
    } else {
        $selj = "SELECTED";
        $seln = "";
    }
    $text = "<form action='sms.php' name='ok'>";
    $text .= "Ich möchte SMS von anderen Usern aus dem $chat empfangen: ";
    $text .= "<select name='u_sms_ok'>\n";
    $text .= "<option value='J' $selj>Ja</option>\n";
    $text .= "<option value='N' $seln>Nein</option>\n";
    $text .= "</select>\n";
    $text .= "<input type='hidden' name='http_host' value='$http_host'>\n";
    $text .= "<input type='hidden' name='id' value='$id'>\n";
    $text .= "<input type=submit name='aktion' value='Änderungen speichern'><BR>";
    $text .= "</form>\n";
    $text .= "Dein SMS-Guthaben: $a[u_sms_guthaben] SMS<BR>";
    show_box2($box, $text, "100%");
}
function SMSAnleitung()
{
    global $sms, $dbase, $ft0, $ft1;
    $box = $ft0 . "SMS-Anleitung" . $ft1;
    $text = "Kurze Anleitung zu den SMS-Features:<BR><BR>
Nachdem Sie Ihr SMS Guthaben aufgeladen haben, können Sie mit dem Befehl:<BR>
<B>/sms &lt;nickname&gt; &lt;text&gt;</b> - eine SMS an einen anderen User im Chat schicken.
<BR>";
    if ($dbase == "mainchat") {
        $text .= "<BR>Sie können sogar dem User aus dem Chat wieder per SMS antworten:<BR>
Schicken Sie hierzu eine SMS mit dem Text <B>MC</B> &lt;nickname&gt &lt;Ihre Nachricht&gt; an die Nummer <b>86677</b> (0,19EUR/SMS)<BR>";
    }
    $text .= "
<BR>
Auch können Sie sich per SMS über neue Mails, den Login von Freunden und Antwort
auf eigene Postings im Forum benachrichtigen lassen.<BR>
<BR>
";
    show_box2($box, $text, "100%");
}

function AufladeFormular()
{
    GLOBAL $f, $userdata, $sms, $chat, $u_id, $farbe_tabelle_zeile1, $farbe_tabelle_zeile2, $http_host, $u_nick;
    $box = $ft0 . "Guthaben aufladen" . $ft1;
    $text .= "Wenn Sie SMS aus dem $chat verschicken möchten, so müssen Sie zuerst ein SMS Guthaben kaufen.<BR>";
    $text .= "Dies funktioniert bequem im Lastschriftverfahren von Ihrem Bankkonto.";
    $text .= "<BR>Jede SMS kostet Sie nur <B>$sms[preis] Cent</B>. <BR>Sie können die SMS in verschiedenen Staffelungen kaufen.";
    $handynr = $userdata['u_sms_extra'];
    $handynr = unserialize($handynr);
    $handynr = $handynr['handynr'];
    $text2 .= '
<script>
function CalcSMS()
{
pos=document.forms["sms"].trx_amount.selectedIndex;
centbetrag=document.forms["sms"].trx_amount.options[pos].value;
document.forms["sms"].smsanz.value= (Math.round(centbetrag / ' . $sms[preis]
        . '));
}
function pruefe()
{
alertmsg="";
pos=document.forms["sms"].trx_amount.selectedIndex;
if (pos == "0"){ alertmsg = "Bitte eine Betrag > 0 wählen";}
        
    if (alertmsg == "") {
        return true;
    } else {
        window. alert (alertmsg);
        return false;
    }
        
}
</script>
        
<table align=RIGHT border=0 cellpadding=5 cellspacing=0 BGCOLOR="'
        . $farbe_tabelle_zeile1 . '">
<form action="' . $sms[ipayment]
        . '" METHOD=POST name="sms" onSubmit="return pruefe()">
<input type=hidden name="trxuser_id" value="' . $sms[ipayment_trxuser_id]
        . '">
<input type=hidden name="trxpassword" value="' . $sms[ipayment_trx_password]
        . '">
<input type=hidden name="redirect_url" value="' . $sms[ipayment_redirect_url]
        . '">
<!--
<input type=hidden name="addr_name" value="' . $userdata['u_name']
        . '">
<input type=hidden name="addr_street" value="' . $f['ui_strasse']
        . '">
<input type=hidden name="addr_zip" value="' . $f['ui_plz']
        . '">
<input type=hidden name="addr_city" value="' . $f['ui_ort']
        . '">
<input type=hidden name="addr_email" value="' . $userdata['u_adminemail']
        . '">
-->
<input type=hidden name="http_host" value="' . $http_host
        . '">
<input type=hidden name="u_id" value="' . $u_id
        . '">
<input type=hidden name="invoice_text" value="' . $chat
        . '">
<input type=hidden name="trx_user_comment" value="' . $http_host . '-'
        . $u_nick . '-' . $chat . '">
<input type=hidden name="u_nick" value="' . $u_nick
        . '">
<input type=hidden name="handynr" value="' . $handynr
        . '">
<tr><td>Aufladebetrag</td><td>
<input type=hidden name="trx_currency" value="EUR">
<input type=hidden name="trx_paymenttyp" value="elv">
<select name="trx_amount" onChange=CalcSMS()>
<option value="000">bitte wählen
<option value="300">3.00 Euro
<option value="500">5.00 Euro
<option value="1000">10.00 Euro
<option value="1500">15.00 Euro
<option value="2000">20.00 Euro
<option value="2500">25.00 Euro
</select>
<tr><td>Dafür bekommen Sie</td><td><input name="smsanz" readonly size=5> SMS</td></tr>
<TR><TD COLSPAN=2>
<input type=submit value="Jetzt SMS Guthaben aufladen">
</TD></TR>
</table>
';
    $atext = $text2 . $text;
    show_box2($box, $atext, "100%");
}
// Body-Tag definieren
$body_tag = "<BODY BGCOLOR=\"$farbe_mini_background\" ";
if (strlen($grafik_mini_background) > 0) {
    $body_tag = $body_tag . "BACKGROUND=\"$grafik_mini_background\" ";
}
$body_tag = $body_tag . "TEXT=\"$farbe_mini_text\" "
    . "LINK=\"$farbe_mini_link\" " . "VLINK=\"$farbe_mini_vlink\" "
    . "ALINK=\"$farbe_mini_vlink\">\n";
echo $body_tag;

// Timestamp im Datensatz aktualisieren
aktualisiere_online($u_id, $o_raum);

// Browser prüfen
if (ist_netscape()) {
    $eingabe_breite = 45;
} else {
    $eingabe_breite = 70;
}

if ($u_id && $communityfeatures) {
    
    if (isset($u_sms_ok) && isset($aktion)) {
        $f['u_sms_ok'] = $u_sms_ok;
        $f['ui_id'] = schreibe_db("user", $f, $u_id, "u_id");
        
        unset($f);
    }
    
    //nach 24 Stunden muss Pin geloescht werden
    erzwingeNeuePin();
    
    // Menü als erstes ausgeben
    $box = $ft0 . "Menü SMS" . $ft1;
    $text = "<A HREF=\"hilfe.php?http_host=$http_host&id=$id&aktion=community#sms\">Hilfe</A>\n";
    show_box2($box, $text, "100%");
    echo "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><BR>\n";
    SMSAnleitung();
    echo "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><BR>\n";
    //Nur irgendwas machen wenn User ueberhaupt eine Handynummer eingegeben hat
    if (!CheckHandynummerVorhanden()) {
        
        $box = "Fehler!";
        $text = "<br>Sie habe in Ihrem <a href=\"profil.php?id=$id&http_host=$httphost&aktion=aendern\">Profil</a> keine Handynummer hinterlegt. Um die SMS-Funktionen nutzen zu können, müssen Sie <a href=\"profil.php?id=$id&http_host=$httphost&aktion=aendern\">hier</a> Ihre Handynummer eingeben!";
        show_box2($box, $text, "100%");
        
    } else {
        // Wir holen u_sms_ok und u_sms_guthaben aus der DB
        $q = "SELECT u_sms_ok, u_sms_guthaben FROM user WHERE u_id = '$u_id'";
        $result = mysqli_query($mysqli_link, $q);
        $a = @mysqli_fetch_array($result);
        
        // Wir lesen die Userdaten in $userdata
        $query = "SELECT * FROM user WHERE u_id=$u_id";
        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) == 1) {
            $userdata = mysqli_fetch_array($result);
            mysqli_free_result($result);
        }
        
        // Wir lesen Userinfo in $f
        $query = "SELECT * FROM userinfo WHERE ui_userid=$u_id";
        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) != 0) {
            if (!isset($f) || !is_array($f)) {
                $f = mysqli_fetch_array($result);
            } else {
                $f['ui_id'] = mysql_result($result, 0, "ui_id");
            }
            $profil_gefunden = true;
        } else {
            $profil_gefunden = false;
        }
        @mysqli_free_result($result);
        
        SMSEmpfangFormular();
        
        $u = unserialize($userdata['u_sms_extra']);
        
        if ($aktion == "pinsenden"
            && ($u['pintime'] + 86400 < date("U") || $u['pintime'] == "")) {
            unset($u);
            $u['pin'] = mt_rand(1000, 9999);
            $u['pintime'] = date("U");
            $u['handynr'] = $f['ui_handy'];
            $u['ip'] = $REMOTE_ADDR;
            $u['pinversuche'] = 1;
            $u['handynrok'] = false;
            $g['u_sms_extra'] = serialize($u);
            
            $g['ui_id'] = schreibe_db("user", $g, $u_id, "u_id");
            sms_sende2($f['ui_handy'],
                "Hallo! Die Auflade-PIN fuer den $chat lautet: $u[pin]");
            unset($g);
            
            // Wir lesen die Userdaten in Userdata
            $query = "SELECT * FROM user WHERE u_id=$u_id";
            $result = mysqli_query($conn, $query);
            if ($result && mysqli_num_rows($result) == 1) {
                $userdata = mysqli_fetch_array($result);
                mysqli_free_result($result);
            }
            
        }
        
        $errmsg = "";
        if (isset($pin)) {
            $u = unserialize($userdata['u_sms_extra']);
            if ($u[pin] == $pin && ($u[pinversuche] <= 4)) {
                #print "PIN OK!";
                $u[handynrok] = true;
                $u[ip] = $REMOTE_ADDR;
                $g[u_sms_extra] = serialize($u);
                
                $http_stuff = $_SERVER;
                
                // ausblenden vom uninteressanten Sachen...
                unset($http_stuff['DOCUMENT_ROOT']);
                unset($http_stuff['HTTP_ACCEPT']);
                unset($http_stuff['HTTP_ACCEPT_ENCODING']);
                unset($http_stuff['HTTP_ACCEPT_CHARSET']);
                unset($http_stuff['HTTP_ACCEPT_LANGUAGE']);
                unset($http_stuff['HTTP_CACHE_CONTROL']);
                unset($http_stuff['HTTP_CONNECTION']);
                unset($http_stuff['HTTP_PRAGMA']);
                unset($http_stuff['PATH']);
                unset($http_stuff['SCRIPT_FILENAME']);
                unset($http_stuff['SERVER_ADMIN']);
                unset($http_stuff['SERVER_NAME']);
                unset($http_stuff['SERVER_PORT']);
                unset($http_stuff['SERVER_SIGNATURE']);
                unset($http_stuff['SERVER_SOFTWARE']);
                unset($http_stuff['GATEWAY_INTERFACE']);
                unset($http_stuff['SERVER_PROTOCOL']);
                unset($http_stuff['REQUEST_METHOD']);
                unset($http_stuff['REQUEST_URI']);
                unset($http_stuff['SCRIPT_NAME']);
                unset($http_stuff['PATH_TRANSLATED']);
                unset($http_stuff['PHP_SELF']);
                unset($http_stuff['SERVER_ADDR']);
                unset($http_stuff['argv']);
                unset($http_stuff['argc']);
                unset($http_stuff['CONTENT_TYPE']);
                unset($http_stuff['CONTENT_LENGTH']);
                unset($http_stuff['HTTP_REFERER']);
                unset($http_stuff['HTTP_KEEP_ALIVE']);
                unset($http_stuff['QUERY_STRING']);
                unset($http_stuff['REMOTE_PORT']);
                
                unset($stuff);
                while (list($http_stuff_name, $http_stuff_inhalt) = each(
                    $http_stuff))
                    if ($http_stuff_inhalt)
                        $stuff .= "$http_stuff_name:\t$http_stuff_inhalt\n";
                
                $msg = "PIN-Eingabe OK!\n\nNickname: $userdata[u_nick]\nUsername: $userdata[u_name]\nUserID: $u_id\nHandynummer: $u[handynr]\nIP: $REMOTE_ADDR\n\n$stuff";
                mail($hackmail, "PIN-Eingabe erfolgreich", $msg);
            } else {
                $errmsg = "<b>PIN falsch! Fehlversuche: $u[pinversuche]</b>";
                $u[pinversuche]++;
                $u[handynrok] = false;
                $g[u_sms_extra] = serialize($u);
            }
            
            $g[ui_id] = schreibe_db("user", $g, $u_id, "u_id");
            unset($g);
            
            // Wir lesen die Userdaten in Userdata
            $query = "SELECT * FROM user WHERE u_id=$u_id";
            $result = mysqli_query($conn, $query);
            if ($result && mysqli_num_rows($result) == 1) {
                $userdata = mysqli_fetch_array($result);
                mysqli_free_result($result);
            }
            
        }
        
        echo "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><BR>\n";
        
        // SMS Payment-Gateway eingetragen?
        if ((!isset($sms['ipayment'])) || (!$sms['ipayment'])) {
            
            $box = "Keine Aufladung möglich!";
            $atext = '<BR>Es ist keine Aufladung Ihres Guthabens möglich.';
            show_box2($box, $atext, "100%");
            
        } elseif ($u_punkte_gesamt < $sms[punkte]) {
            
            $box = "Fehler!";
            $atext = '<BR>Um Ihr Guthaben per Lastschrift aufladen zu können, brauchen Sie mindestens '
                . $sms[punkte] . ' Chat-Punkte. Sie haben aber nur '
                . $u_punkte_gesamt . ' Punkte';
            show_box2($box, $atext, "100%");
            
        } else {
            
            $u = unserialize($userdata['u_sms_extra']);
            
            // User bekommt eine PIN wenn, a) noch eine PIN vergeben oder b) PIN älter 24h und handynrok==false
            if (($userdata['u_sms_extra'] == "")
                || ($u['pintime'] + 86400 < date("U")
                    && $u['handynrok'] == false)) {
                PinVergabeFormular($message);
            }
            
            // Wenn PIN gesetzt UND PIN-Vergabe in den letzten 24h UND PINVERSUCHE <= 4 UND HANDYNUMMER == FALSCH dann PinEingabeFormular
            if (($u['pin'] != "") && ($u['pintime'] + 86400 > date("U"))
                && ($u['pinversuche'] <= 4) && ($u['handynrok'] == false)) {
                PinEingabeFormular($errmsg);
            }
            
            // Wenn Fehleingaben > 4 dann Fehlermeldung
            if (($u['pin'] != "") && ($u['pintime'] + 86400 > date("U"))
                && ($u['pinversuche'] > 4) && ($u['handynrok'] == false)) {
                PinErrorFormular();
            }
            
            // Wenn Handynummer richtig dann Aufladen OK
            if ($u['handynrok'] == true) {
                AufladeFormular();
            }
            
        }
        
        echo "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><BR>\n";
        ZeigSMSHistory($u_id);
        
    }
}
?>
</BODY></HTML>
