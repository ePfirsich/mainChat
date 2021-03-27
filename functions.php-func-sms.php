<?php

function sms_msg(
    $von_user,
    $von_user_id,
    $an_user,
    $farbe,
    $text,
    $userdata = "")
{
    // Schreibt privaten Text von $von_user an User $an_user
    // Art:           N: Normal
    //                S: Systemnachricht
    //                P: Privatnachticht
    //                H: Versteckte Nachricht
    
    global $conn, $chat, $http_host, $u_punkte_gesamt, $sms;
    
    unset($fehler);
    
    $smszulang = false;
    $emp = user($von_user_id, $userdata, FALSE, FALSE, "&nbsp;", "", "", FALSE,
        TRUE);
    $absender = $emp . "@" . $chat . ": "; // Wir basteln uns den Absender der SMS
    
    if (160 - strlen($text) - strlen($absender) < 0) {
        $smszulang = true;
    }
    
    $text = substr($text, 0, 160 - strlen($absender)); // Text um Absender kürzen
    $text = preg_replace("/[\\\\" . chr(1) . "-" . chr(31) . "]/", "", $text); // Ungültige Zeichen filtern
    
    $complete = $absender . $text;
    
    // Prüfen ob genug Punkte
    #if ($u_punkte_gesamt < $sms[punkte]) { $fehler = "Um SMS verschicken zu dürfen brauchst Du mehr als $sms[punkte] Punkte.";}
    
    // Prüfen ob noch genug SMS-Guthaben da
    $guthaben = hole_smsguthaben($von_user_id);
    
    if (!isset($fehler)) {
        if ($guthaben <= 0) {
            $fehler = "Du hast kein SMS-Guthaben mehr!";
        }
    }
    
    // Prüfen ob User SMS möchte
    $query = "SELECT u_sms_ok FROM user WHERE u_id = " . intval($an_user);
    $result = mysqli_query($mysqli_link, $query);
    $a = mysqli_fetch_array($result);
    mysqli_free_result($result);
    $sms_ok = $a['u_sms_ok'];
    if (!isset($fehler)) {
        if ($sms_ok == "N") {
            $fehler = "Dieser User möchte keine SMS empfangen.";
        }
    }
    
    // Prüfen auf gültige Handynummer des Empfängers
    
    $handynr = hole_handynummer($an_user);
    if (!isset($fehler)) {
        if (!pruefe_handynummer($handynr)) {
            $fehler = "Dieser User hat leider keine gültige Handynummer eingetragen.";
        }
    }
    
    if (isset($fehler)) {
        system_msg("", 0, $von_user_id, $u_farbe,
            "<b>Fehler:</b> Die SMS konnte nicht verschickt werden. $fehler");
    } else {
        $query = "SELECT u_nick FROM user WHERE u_id = " . intval($an_user);
        $result = mysqli_query($mysqli_link, $query);
        $emp2 = mysqli_fetch_array($result);
        
        sms_sende($von_user_id, $an_user, $complete);
        $txt = "<B>$chat:</B> sende SMS an $emp2[u_nick]: '$text'";
        if ($smszulang) {
            system_msg("", 0, $von_user_id, $system_farbe,
                "<B>Hinweis:</B> Die eingegebene SMS war zu lang. Sie wurde auf 160 Zeichen gekürzt!");
        }
        
        system_msg("", 0, $von_user_id, $system_farbe, $txt);
    }
    
    // In Session Timeout-Zeit auf jetzt setzen
    if ($von_user_id) {
        $query = "UPDATE online SET o_timeout_zeit=DATE_FORMAT(NOW(),\"%Y%m%d%H%i%s\"), o_timeout_warnung='N' "
            . "WHERE o_user=" . intval($von_user_id);
        $result = mysqli_query($conn, $query);
    }
    
    return ($back);
    
}

function sms_sende($von_user_id, $an_user, $nachricht)
{
    // verschickt eine SMS mit dem Text $nachricht an UserID von $an_user und
    // zieht ein Credit von $von_user_id ab
    global $sms, $chat, $dbase;
    $handynummer = hole_handynummer($an_user);
    $guthaben = hole_smsguthaben($von_user_id);
    
    if (pruefe_handynummer($handynummer) && $guthaben > 0) {
        $handynummer = urlencode($handynummer);
        $nachricht = htmlspecialchars(strip_tags($nachricht));
        $nachricht = str_replace("\n", " ", $nachricht);
        $nachricht = str_replace("'", "", $nachricht);
        $nachricht = str_replace("\"", "", $nachricht);
        $nachricht = urlencode($nachricht);
        $gw = $sms[gateway];
        
        $query = "SELECT COUNT(*) as zahl FROM sms WHERE s_an_user_id = " . intval($an_user);
        $result = mysqli_query($mysqli_link, $query);
        $num = mysqli_fetch_array($result);
        if ($num[zahl] == 0) {
            $nachricht2 = "Du erhälst gleich eine SMS aus dem $chat - Um dem User zu antworten schreibe einfach eine SMS an $sms[shortid] mit $sms[keyword] <nick> <nachricht> (0,19EUR/SMS)";
            $nachricht2 = urlencode($nachricht2);
            $url = $sms[gateway_url][$gw];
            $url = str_replace("%sender%", urlencode(substr($chat, 0, 11)),
                $url);
            $url = str_replace("%nummer%", $handynummer, $url);
            $url = str_replace("%message%", $nachricht2, $url);
            if ($dbase == "mainchat")
                @fopen($url, "r");
            // Hier schicken wir die SMS über den Gateway raus
        }
        
        $url = $sms[gateway_url][$gw];
        $url = str_replace("%nummer%", $handynummer, $url);
        $url = str_replace("%message%", $nachricht, $url);
        if ($dbase != "mainchat") {
            $url = str_replace("%sender%", urlencode(substr($chat, 0, 11)),
                $url);
        } else {
            $url = str_replace("%sender%", $sms[shortid], $url);
        }
        @fopen($url, "r"); // Hier schicken wir die SMS über den Gateway raus
        
        // Ein Credit abziehen
        $f[u_sms_guthaben] = $guthaben - 1;
        
        // Änderungen in DB schreiben
        $f[ui_id] = schreibe_db("user", $f, $von_user_id, "u_id");
        unset($f);
        
        $f[s_zeit] = date("YmdHis");
        $f[s_von_user_id] = $von_user_id;
        $f[s_an_user_id] = $an_user;
        $f[s_status] = "S";
        $f[s_text] = urldecode($nachricht);
        $back = schreibe_db("sms", $f, "", "s_id");
        
    }
}

function sms_sende2($nummer, $nachricht)
{
    // verschickt eine SMS mit dem Text $nachricht an $nummer
    global $sms, $dbase, $chat;
    $handynummer = urlencode($nummer);
    $nachricht = htmlspecialchars(strip_tags($nachricht));
    $nachricht = str_replace("\n", " ", $nachricht);
    $nachricht = str_replace("'", "", $nachricht);
    $nachricht = str_replace("\"", "", $nachricht);
    $nachricht = urlencode($nachricht);
    
    $gw = $sms[gateway];
    $url = $sms[gateway_url][$gw];
    $url = str_replace("%nummer%", $handynummer, $url);
    $url = str_replace("%message%", $nachricht, $url);
    if ($dbase != "mainchat") {
        $url = str_replace("%sender%", urlencode(substr($chat, 0, 11)), $url);
    } else {
        $url = str_replace("%sender%", $sms[shortid], $url);
    }
    @fopen($url, "r"); // Hier schicken wir die SMS über den Gateway raus
    
}

function hole_smsguthaben($von_user_id)
{
    $query = "SELECT u_sms_guthaben FROM user WHERE u_id = " . intval($von_user_id);
    $result = mysqli_query($mysqli_link, $query);
    $a = mysqli_fetch_array($result);
    return ($a['u_sms_guthaben']);
}

function hole_handynummer($user_id)
{
    $query = "SELECT ui_handy FROM userinfo WHERE ui_userid = " . intval($user_id);
    $result = mysqli_query($mysqli_link, $query);
    $a = mysqli_fetch_array($result);
    $handynr = $a['ui_handy'];
    return ($handynr);
}

function pruefe_handynummer($number)
{
    $number = str_replace(' ', '', $number);
    $number = str_replace('-', '', $number);
    $number = str_replace('/', '', $number);
    $number = str_replace('\+', '', $number);
    $number = str_replace('^0049', '49', $number);
    $number = str_replace('^0', '49', $number);
    $land = substr($number, 0, 2);
    $netz = substr($number, 2, 3);
    $nummer = substr($number, 5, strlen($number) - 5);
    
    $num[land] = $land;
    $num[netz] = $netz;
    $num[nummer] = $nummer;
    
    $nummer_ok = true;
    
    $netze = array("0151", "0160", "0170", "0171", "0175", "0152", "0162",
        "0172", "0173", "0174", "0155", "0157", "0163", "0177", "0178", "0159",
        "0176", "0179", "0150", "0156");
    
    if (!in_array($num[netz], $netze)) {
        $nummer_ok = false;
    } // Prüfung ob Netz bekannt
    if (!preg_match("/^([0-9]{7,10})$/i", $num[nummer])) {
        $nummer_ok = false;
    } // Prüfung ob Nummer lang genug
    return ($nummer_ok);
}

?>
