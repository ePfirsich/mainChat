<?php

require("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, u_level, o_js
id_lese($id);

$title = $body_titel . ' - Aktionen';
zeige_header_anfang($title, $farbe_mini_background, $grafik_mini_background, $farbe_mini_link, $farbe_mini_vlink);
?>
<script>
		window.focus()
		function win_reload(file,win_name) {
				win_name.location.href=file;
}
		function opener_reload(file,frame_number) {
				opener.parent.frames[frame_number].location.href=file;
}
</script>
<?php
zeige_header_ende();
?>
<body>
<?php
// Timestamp im Datensatz aktualisieren
aktualisiere_online($u_id, $o_raum);

$eingabe_breite = 70;

if ($u_id && $communityfeatures) {
	
	// Menü als erstes ausgeben
	$box = $ft0 . "Menü Aktionen" . $ft1;
	$text = "<A HREF=\"hilfe.php?http_host=$http_host&id=$id&aktion=community#home\">Hilfe</A>\n";
	show_box2($box, $text, "100%");
	?>
	<img src="pics/fuell.gif" alt="" style="width:4px; height:4px;"><br>
	<?php
	
	switch ($aktion) {
		
		case "eintragen":
		// Ab in die Datenbank mit dem Eintrag
			eintrag_aktionen($aktion_datensatz);
			zeige_aktionen("normal");
			break;
		
		default:
			zeige_aktionen("normal");
	}
	
	echo "<P><b>Tipps zur den Aktionen:</b></P>"
		. "<P>Mit den Aktionen steuern Sie, bei welchen Ereignissen Sie mit "
		. "welcher Nachricht informiert werden wollen. Ein Ereignis ist z.B. "
		. "der Logon oder Logout eines <b>Freundes</b>, oder auch der Eingang einer "
		. "neuen <b>Mail</b>.</P>"
		. "<P>Wann Sie die Nachricht erhalten wollen, wählen Sie aus der obersten Zeile aus. "
		. "Möglich ist der Empfang sofort wenn Sie online sind (<b>Sofort/Online</b>) "
		. "(z.B. <b>Freund</b> loggt ein/aus "
		. "oder zu dem Moment, in dem Sie eine <b>Neue Mail</b> erhalten), bei Ihrem "
		. "<b>Login</b> in den Chat oder regelmäßig "
		. "<b>alle 5 Minuten</b> (regelmäßige Information über die vorliegenden "
		. "<b>Neuen Mails</b> oder die Anwesendheit Ihrer <b>Freunde</b> im Chat)</P>"
		. "<P>Die Benachrichtungen, die Sie erhalten, wenn Sie nicht im Chat sind (offline), "
		. "wählen Sie unter <b>Sofort/Offline</b> aus.</P>"
		. "<P>Die Art der Nachricht ist einstellbar, so gibt es <b>keine</b> Benachrichtigung, "
		. "<b>Chat-Mail</b> (chat-interne Mail), eine <b>E-Mail</b> an Ihre nicht-öffentliche "
		. "E-Mail Adresse oder eine <b>OLM</b> (OnLineMessage, direkte Nachricht in Chat wie "
		. "/msg)."
		. "Zusätzlich sind auch Kombinationen von <b>E-Mail und OLM</b> sowie <b>Chat-Mail "
		. "und OLM</b> möglich, wobei Sie in diesem Fall zwei Nachrichten erhalten.</P>";
}

if ($o_js || !$u_id) {
	?>
	<div style="text-align:center;">[<a href="javascript:window.close();"><?php echo $t[sonst1]; ?></a>]</div>
	<br>
	<?php
}
?>
</body>
</html>