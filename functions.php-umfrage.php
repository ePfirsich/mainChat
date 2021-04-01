<?php

function umfrage_aendern($umfrage)
{
    global $id, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $mysqli_link, $dbase;
    global $farbe_text, $farbe_tabelle_kopf2, $farbe_tabelle_zeile1, $farbe_tabelle_zeile2, $PHP_SELF;
    
    if ($umfrage == 0)
        $todo = "neu";
    else $todo = "aendern";
    
    echo "$todo " . $umfrage;
    
}

function umfrage($umfrage)
{
    global $id, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $mysqli_link, $dbase;
    global $farbe_text, $farbe_tabelle_kopf2, $farbe_tabelle_zeile1, $farbe_tabelle_zeile2, $PHP_SELF;
    
    echo "Umfrage: " . $umfrage;
}

function anzeige_umfragen_aktuell($adm = 0)
{
    global $id, $mysqli_link, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $mysqli_link, $dbase;
    global $farbe_text, $farbe_tabelle_kopf2, $farbe_tabelle_zeile1, $farbe_tabelle_zeile2, $PHP_SELF;
    
    echo "<BR /><TABLE WIDTH=100% BORDER=0 CELLPADDING=3 CELLSPACING=0>";
    if ($adm) {
        $link = "<A HREF=\"umfrage.php?http_host=$http_host&id=$id&adminuebersicht=1&aktion=neu\">Neue Umfrage</A>";
        echo "<TR><TD COLSPAN=3>" . $f1 . "[$link]<BR/><BR/>" . $f2
            . "</TD></TR>";
    }
    
    $query = "SELECT *, date_format(um_ende_umfrage,'%d.%m.%Y %H Uhr') as ende_umfrage "
        . "FROM umfrage WHERE um_start_umfrage <= '" . date('Y-m-d H:i:s')
        . "'" . " and um_ende_umfrage >= '" . date('Y-m-d H:i:s') . "'"
        . " and um_start <= '" . date('Y-m-d H:i:s') . "'"
        . " and um_ende >= '" . date('Y-m-d H:i:s') . "'"
        . " order by um_bereich, um_start_umfrage, um_id ";
    $result = mysqli_query($mysqli_link, $query);
    if ($result) {
        $anzahl = mysqli_num_rows($result);
        if ($anzahl == 0) {
            echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=3><DIV style=\"color:$farbe_text;\"><b>Aktuelle Umfragen</b>"
                . "<TR BGCOLOR=\"$farbe_tabelle_zeile1\"><TD COLSPAN=3 align=\"left\">Keine Umfrage vorhanden.</TD></TR>";
        } else {
            $bereich = '';
            $i = 0;
            echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=3><b>Aktuelle Umfragen</b>";
            while ($row = mysqli_fetch_object($result)) {
                $i++;
                if ($bereich <> $row->um_bereich) {
                    if ($i <> 1) {
                        echo "<TR><TD COLSPAN=3>" . $f1 . "&nbsp;" . $f2
                            . "</TR></TD>";
                    }
                    
                    echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=3><b>zum Thema: $row->um_bereich</b>"
                        . "<TR><TD WIDTH=\"70%\">" . $f1 . "<b>Frage</b>" . $f2
                        . "</TD><TD WIDTH=\"15%\">" . $f1 . "<b>Status</b>"
                        . $f2 . "</TD><TD WIDTH=\"15%\">" . $f1
                        . "<b>endet</b>" . $f2 . "</TD></TR>";
                    $bereich = $row->um_bereich;
                }
                
                if (($i % 2) > 0) {
                    $bgcolor = $farbe_tabelle_zeile1;
                } else {
                    $bgcolor = $farbe_tabelle_zeile2;
                }
                
                $link = "<A HREF=\"umfrage.php?http_host=$http_host&id=$id&aktion=umfrage&umfrage=$row->um_id\">Zwischenstand</A>";
                $link = "<A HREF=\"umfrage.php?http_host=$http_host&id=$id&aktion=umfrage&umfrage=$row->um_id\">Teilnehmen</A>";
                
                echo "<TR BGCOLOR=\"$bgcolor\"><TD>" . $f1 . $row->um_frage
                    . $f2 . "</TD><TD>" . $f1 . $link . $f2 . "</TD><TD>" . $f1
                    . $row->ende_umfrage . $f2 . "</TD></TR>\n";
                
            }
        }
    }
    mysqli_free_result($result);
    
    echo "</TABLE>";
    
}

function anzeige_umfragen_zukuenftig($adm = 0)
{
    global $id, $mysqli_link, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $mysqli_link, $dbase;
    global $farbe_text, $farbe_tabelle_kopf2, $farbe_tabelle_zeile1, $farbe_tabelle_zeile2, $PHP_SELF;
    
    echo "<BR /><TABLE WIDTH=100% BORDER=0 CELLPADDING=3 CELLSPACING=0>";
    
    $query = "SELECT *, date_format(um_start_umfrage,'%d.%m.%Y %H Uhr') as start_umfrage "
        . "FROM umfrage WHERE um_start_umfrage >= '" . date('Y-m-d H:i:s')
        . "'" . " and um_start <= '" . date('Y-m-d H:i:s') . "'"
        . " order by um_bereich, um_start_umfrage, um_id ";
    $result = mysqli_query($mysqli_link, $query);
    if ($result) {
        $anzahl = mysqli_num_rows($result);
        if ($anzahl == 0) {
            echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=3><DIV style=\"color:$farbe_text;\"><b>Zukünftige Umfragen</b>"
                . "<TR BGCOLOR=\"$farbe_tabelle_zeile1\"><TD COLSPAN=3 align=\"left\">Keine Umfrage vorhanden.</TD></TR>";
        } else {
            $bereich = '';
            $i = 0;
            echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=3><b>Zukünftige Umfragen</b>";
            while ($row = mysqli_fetch_object($result)) {
                $i++;
                if ($bereich <> $row->um_bereich) {
                    if ($i <> 1) {
                        echo "<TR><TD COLSPAN=3>" . $f1 . "&nbsp;" . $f2
                            . "</TR></TD>";
                    }
                    
                    echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=3><b>zum Thema: $row->um_bereich</b>"
                        . "<TR><TD WIDTH=\"70%\">" . $f1 . "<b>Frage</b>" . $f2
                        . "</TD><TD WIDTH=\"15%\">" . $f1 . "<b>Status</b>"
                        . $f2 . "</TD><TD WIDTH=\"15%\">" . $f1
                        . "<b>startet</b>" . $f2 . "</TD></TR>";
                    $bereich = $row->um_bereich;
                }
                
                if (($i % 2) > 0) {
                    $bgcolor = $farbe_tabelle_zeile1;
                } else {
                    $bgcolor = $farbe_tabelle_zeile2;
                }
                
                echo "<TR BGCOLOR=\"$bgcolor\"><TD>" . $f1 . $row->um_frage
                    . $f2 . "</TD><TD>" . $f1 . "geplant" . $f2 . "</TD><TD>"
                    . $f1 . $row->start_umfrage . $f2 . "</TD></TR>\n";
                
            }
        }
    }
    mysqli_free_result($result);
    
    echo "</TABLE>";
    
}

function anzeige_umfragen_abgeschlossen($adm = 0)
{
    global $id, $mysqli_link, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $mysqli_link, $dbase;
    global $farbe_text, $farbe_tabelle_kopf2, $farbe_tabelle_zeile1, $farbe_tabelle_zeile2, $PHP_SELF;
    
    echo "<BR /><TABLE WIDTH=100% BORDER=0 CELLPADDING=3 CELLSPACING=0>";
    
    $query = "SELECT *, date_format(um_ende_umfrage,'%d.%m.%Y %H Uhr') as ende_umfrage "
        . "FROM umfrage WHERE um_ende_umfrage <= '" . date('Y-m-d H:i:s') . "'"
        . " and um_ende <= '" . date('Y-m-d H:i:s') . "'"
        . " order by um_bereich, um_start_umfrage, um_id ";
    $result = mysqli_query($mysqli_link, $query);
    if ($result) {
        $anzahl = mysqli_num_rows($result);
        if ($anzahl == 0) {
            echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=3><DIV style=\"color:$farbe_text;\"><b>Abgeschlossene Umfragen</b>"
                . "<TR BGCOLOR=\"$farbe_tabelle_zeile1\"><TD COLSPAN=3 align=\"left\">Keine Umfrage vorhanden.</TD></TR>";
        } else {
            $bereich = '';
            $i = 0;
            echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=3><b>Abgeschlossene Umfragen</b>";
            while ($row = mysqli_fetch_object($result)) {
                $i++;
                if ($bereich <> $row->um_bereich) {
                    if ($i <> 1) {
                        echo "<TR><TD COLSPAN=3>" . $f1 . "&nbsp;" . $f2
                            . "</TR></TD>";
                    }
                    
                    echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=3><b>zum Thema: $row->um_bereich</b>"
                        . "<TR><TD WIDTH=\"70%\">" . $f1 . "<b>Frage</b>" . $f2
                        . "</TD><TD WIDTH=\"15%\">" . $f1 . "<b>Status</b>"
                        . $f2 . "</TD><TD WIDTH=\"15%\">" . $f1
                        . "<b>endete</b>" . $f2 . "</TD></TR>";
                    $bereich = $row->um_bereich;
                }
                
                if (($i % 2) > 0) {
                    $bgcolor = $farbe_tabelle_zeile1;
                } else {
                    $bgcolor = $farbe_tabelle_zeile2;
                }
                
                $link = "<A HREF=\"umfrage.php?http_host=$http_host&id=$id&aktion=umfrage&umfrage=$row->um_id\">abgeschlossen</A>";
                
                echo "<TR BGCOLOR=\"$bgcolor\"><TD>" . $f1 . $row->um_frage
                    . $f2 . "</TD><TD>" . $f1 . $link . $f2 . "</TD><TD>" . $f1
                    . $row->ende_umfrage . $f2 . "</TD></TR>\n";
                
            }
        }
    }
    mysqli_free_result($result);
    
    echo "</TABLE>";
    
}

?>