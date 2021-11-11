<?php

function genhash($u_id)
{
    $zeit = floor(date("U") / 1000);
    $s = $u_id . " " . $zeit;
    $hash = md5($s);
    
    return $hash;
}

function checkhash($chash, $u_id)
{
    $hash_ok = false;
    $tzeit = floor(date("U") / 1000);
    
    for ($i = -3; $i < 4; $i++) {
        $zeit = $tzeit + $i;
        
        $s = $u_id . " " . $zeit;
        $hash = md5($s);
        
        if ($chash == $hash) {
            $hash_ok = true;
        }
    }
    return ($hash_ok);
}

?>