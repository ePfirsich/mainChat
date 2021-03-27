<?php

function SplitMobile($number)
{
    $number = urldecode($number);
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
    return ($num);
}

function CheckMobile($num)
{
    $nummer_ok = true;
    
    $netze = array("0151", "0160", "0170", "0171", "0175", "0152", "0162",
        "0172", "0173", "0174", "0155", "0157", "0163", "0177", "0178", "0159",
        "0176", "0179", "0150", "0156");
    
    if (!in_array($num[netz], $netze)) {
        $nummer_ok = false;
    } // Prüfung ob Netz bekannt
    if (!preg_match("/^([0-9]{7,12})$/i", $num[nummer])) {
        $nummer_ok = false;
    } // Prüfung ob Mobilnummer mindests 7 max 10 Ziffern
    if ($num[nummer] == "") {
        $nummer_ok = false;
    }
    return ($nummer_ok);
}

function NumberOK($number)
{
    $num = SplitMobile($number);
    $ok = false;
    $ok = CheckMobile($num);
    return ($ok);
}

function FormatNumber($number)
{
    if (NumberOK($number)) {
        $num = SplitMobile($number);
        $nummer = "0" . $num[netz] . $num[nummer];
    }
    return ($nummer);
}

function CheckNetz($number)
{
    $d1 = array("151", "160", "170", "175");
    $d2 = array("152", "162", "172", "173", "174");
    $eplus = array("163", "177", "178");
    $o2 = array("159", "176", "179");
    
    $num = SplitMobile($number);
    if (in_array($num[netz], $d1))
        $netz = "D1";
    if (in_array($num[netz], $d2))
        $netz = "D2";
    if (in_array($num[netz], $eplus))
        $netz = "E+";
    if (in_array($num[netz], $o2))
        $netz = "O2";
    
    return ($netz);
}

?>
