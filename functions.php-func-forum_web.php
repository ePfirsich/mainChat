<?php

//Kopf fuer das Forum
function kopf_forum($admin) {
	global $id, $u_nick, $menue;
	global $chat, $body_titel;
	global $aktion;
	global $t;
	
	// Fenstername
	$fenster = str_replace("+", "", $u_nick);
	$fenster = str_replace("-", "", $fenster);
	$fenster = str_replace("ä", "", $fenster);
	$fenster = str_replace("ö", "", $fenster);
	$fenster = str_replace("ü", "", $fenster);
	$fenster = str_replace("Ä", "", $fenster);
	$fenster = str_replace("Ö", "", $fenster);
	$fenster = str_replace("Ü", "", $fenster);
	$fenster = str_replace("ß", "", $fenster);
	
	$title = $body_titel;
	zeige_header_anfang($title, 'chatausgabe');
	?>
	<script>
	function ask(text) {
			return(confirm(text));
	}
	function neuesFenster(url) {
			hWnd=window.open(url,"<?php echo $fenster; ?>","resizable=yes,scrollbars=yes,width=300,height=580");
	}
	function neuesFenster2(url) {
			hWnd=window.open(url,"<?php echo "640_" . $fenster; ?>","resizable=yes,scrollbars=yes,width=780,height=580");
	}
	</script>
	<?php
	zeige_header_ende();
	?>
	<body>
	<br>
	<table class="tabelle_gerust2">
		<tr>
			<td>
				<b>
					<a href="forum.php?id=<?php echo $id; ?>"><?php echo $chat; ?>-Forum</a>
				</b>
				<?php if (($admin) && (!$aktion)) { ?>
					<a href="forum.php?id=<?php echo $id; ?>&aktion=forum_neu" class="button" title="<?php echo $t['kategorie_anlegen']; ?>"><span class="fa fa-plus icon16"></span> <span><?php echo $t['kategorie_anlegen']; ?></span></a>
				<?php } ?>
			</td>
		</tr>
	</table>
	<br>
	<?php
}

//Fuss für das Forum
function fuss_forum() {
	?>
	<br>
	<br>
	</body>
	</html>
	<?php
}

//Zeigt fehlende Eingaben an
function show_missing($missing) {
	global $farbe_hervorhebung_forum;
	
	echo "<p style=\"color:$farbe_hervorhebung_forum; font-weight:bold; text-align:center;\">$missing</p>";
}

//Eingabemaske für neues Forum
function maske_forum($fo_id = 0) {
	global $id, $mysqli_link;
	global $f1, $f2;
	global $t;
	
	if ($fo_id > 0) {
		$fo_id = intval($fo_id);
		$sql = "select fo_name, fo_admin from forum where fo_id=$fo_id";
		$query = mysqli_query($mysqli_link, $sql);
		$fo_name = htmlspecialchars(mysqli_result($query, 0, "fo_name"));
		$fo_admin = mysqli_result($query, 0, "fo_admin");
		mysqli_free_result($query);
		
		$kopfzeile = str_replace("xxx", $fo_name, $t['kategorie_editieren_mit_Name']);
		$button = $t['kategorie_editieren'];
		
	} else {
		$kopfzeile = $t['kategorie_anlegen'];
		$button = $t['kategorie_anlegen'];
		
	}
	?>
	<form action="forum.php" method="post">
		<table class="tabelle_gerust">
			<tr>
				<td class="tabelle_kopfzeile" colspan="2"><?php echo $kopfzeile; ?></td>
			</tr>
			<tr>
				<td style="width:260px; font-weight:bold;" class="tabelle_koerper_login"><?php echo $f1; ?><?php echo $t['kategorie_name']; ?><?php echo $f2; ?></td>
				<td class="tabelle_koerper_login"><input type="text" size="50" name="fo_name" value="<?php echo $fo_name; ?>"></td>
			</tr>
			<?php
			// Forumsrechte für Gast einstellen
			if (($fo_admin & 8) == 8) {
				$selg1 = "SELECTED";
				$selg2 = "";
			}
			if (($fo_admin & 16) == 16) {
				$selg1 = "";
				$selg2 = "SELECTED";
			}
			?>
			<tr>
				<td style="font-weight:bold;" class="tabelle_koerper_login"><?php echo $f1; ?><?php echo $t['forum_msg3']; ?><?php echo $f2; ?></td>
				<td class="tabelle_koerper_login">
					<select size="1" name="fo_gast">
						<?php
						echo "<option value=\"0\">$t[forum_msg7]\n";
						echo "<option value=\"8\" $selg1>$t[forum_msg5]\n";
						echo "<option value=\"24\" $selg2>$t[forum_msg6]\n";
						?>
					</select>
				</td>
			</tr>
			<?php
			// Forumsrechte für einen Benutzer einstellen
			if (($fo_admin & 2) == 2) {
				$selu1 = "selected";
				$selu2 = "";
			}
			if (($fo_admin & 4) == 4) {
				$selu1 = "";
				$selu2 = "selected";
			}
			?>
			<tr>
				<td style="font-weight:bold;" class="tabelle_koerper_login"><?php echo $f1; ?><?php echo $t['forum_msg4']; ?><?php echo $f2; ?></td>
				<td class="tabelle_koerper_login">
					<select size="1" name="fo_user">
						<?php
						echo "<option value=\"0\">$t[forum_msg7]\n";
						echo "<option value=\"2\" $selu1>$t[forum_msg5]\n";
						echo "<option value=\"6\" $selu2>$t[forum_msg6]\n";
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:right;" class="tabelle_koerper_login"><input type="submit" value="<?php echo $button; ?>"></td>
			</tr>
		</table>
		<input type="hidden" name="id" value="<?php echo $id; ?>">
		<?php
		if ($fo_id > 0) {
			echo "<input type=\"hidden\" name=\"fo_id\" value=\"$fo_id\">\n";
			echo "<input type=\"hidden\" name=\"aktion\" value=\"forum_editieren\">";
		} else {
			echo "<input type=\"hidden\" name=\"aktion\" value=\"forum_anlegen\">";
		}
		?>
	</form>
	<?php
}

//gibt Liste aller Foren mit Themen aus
function forum_liste() {
	global $mysqli_link;
	global $id, $forum_admin, $chat_grafik;
	global $t, $f1, $f2, $f3, $f4;
	global $u_level;
	
	$sql = "SELECT fo_id, fo_name, fo_order, fo_admin,
				th_id, th_fo_id, th_name, th_desc, th_anzthreads, th_anzreplys, th_order, th_postings
				FROM forum, thema WHERE fo_id = th_fo_id ";
	if ($u_level == "G") {
		$sql .= "and ( ((fo_admin & 8) = 8) or fo_admin = 0) ";
	}
	if ($u_level == "U" || $u_level == "A" || $u_level == "M" || $u_level == "Z") {
		$sql .= "and ( ((fo_admin & 2) = 2) or fo_admin = 0) ";
	}
	
	$sql .= "ORDER BY fo_order, th_order";
	
	$query = mysqli_query($mysqli_link, $sql);
	//fo_id merken zur Darstellnug des Kopfes
	$fo_id_last = 0;
	$zeile = 0;
	if ($query && mysqli_num_rows($query) > 0) {
		while ($thema = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
			//Neues Forum?
			if ($fo_id_last != $thema['fo_id']) {
				if ($zeile > 0) {
					echo "</table></td></tr></table><br>";
				}
				?>
				<table class="tabelle_gerust">
					<tr>
						<td class="tabelle_kopfzeile" colspan="2">
							<table style="width:100%">
								<tr>
								<td style="font-weight:bold;">&nbsp;&nbsp;&nbsp;
								<?php
								echo htmlspecialchars($thema['fo_name']) . "<a name=\"" . $thema['fo_id'] . "\"></a></td>";
								if ($forum_admin) {
									?>
									<td style="text-align:right; vertical-align:middle;">
										<a href="forum.php?id=<?php echo $id; ?>&aktion=forum_edit&fo_id=<?php echo $thema['fo_id']; ?>" class="button" title="<?php echo $t['button_editieren']; ?>"><span class="fa fa-pencil icon16"></span> <span><?php echo $t['button_editieren']; ?></span></a>
										<a href="forum.php?id=<?php echo $id; ?>&aktion=forum_delete&fo_id=<?php echo $thema['fo_id']; ?>" onClick="return ask('<?php echo $t['kategorie_loeschen']; ?>')" class="button" title="<?php echo $t['button_loeschen']; ?>"><span class="fa fa-trash icon16"></span> <span><?php echo $t['button_loeschen']; ?></span></a>
										<a href="forum.php?id=<?php echo $id; ?>&fo_id=<?php echo $thema['fo_id']; ?>&aktion=thema_neu" class="button" title="<?php echo $t['button_neues_forum']; ?>"><span class="fa fa-plus icon16"></span> <span><?php echo $t['button_neues_forum']; ?></span></a>
									</td>
									<td style="width:17px;">
										<a href="forum.php?id=<?php echo $id; ?>&fo_id=<?php echo $thema['fo_id']; ?>&fo_order=<?php echo $thema['fo_order']; ?>&aktion=forum_up" class="button"><span class="fa fa-arrow-up icon16"></span></a><br>
										<br>
										<a href="forum.php?id=<?php echo $id; ?>&fo_id=<?php echo $thema['fo_id']; ?>&fo_order=<?php echo $thema['fo_order']; ?>&aktion=forum_down" class="button"><span class="fa fa-arrow-down icon16"></span></a>
									</td>
									<?php
								}
								?>
								</tr>
							</table>
						</td>
						<td class="tabelle_kopfzeile" style="width:66px; text-align:center;"> </td>
						<td class="tabelle_kopfzeile" style="width:66px; text-align:center;"> </td>
					</tr>
				<?php
			}
			if ($zeile % 2) {
				$farbe = 'class="tabelle_zeile1"';
			} else {
				$farbe = 'class="tabelle_zeile2"';
			}
			if ($thema['th_name'] != "dummy-thema") {
				if ($thema['th_postings']) {
					$arr_posting = explode(",", $thema['th_postings']);
				} else {
					$arr_posting = array();
				}
				$ungelesene = anzahl_ungelesene3($arr_posting, $thema['th_id']);
				
				echo "<tr>";
				
				if ($ungelesene == 0) {
					$folder = $chat_grafik['forum_ordnerneu'];
				} else if ($ungelesene < 11) {
					$folder = $chat_grafik['forum_ordnerblau'];
				} else {
					$folder = $chat_grafik['forum_ordnervoll'];
				}
				
				echo "<td style=\"width:30px; text-align:center;\" $farbe>$folder</td>";
				if ($forum_admin) {
					?>
					<td style="width:550px;" <?php echo $farbe; ?>>
						<table style="width:100%;">
							<tr>
								<td style="width:385px; font-weight:bold;">
									<?php echo $f1; ?>
									<a href="forum.php?id=<?php echo $id; ?>&th_id=<?php echo $thema['th_id']; ?>&aktion=show_thema">
									<?php echo htmlspecialchars($thema['th_name']) . "</a>$f2<br>" . $f3 . " " . htmlspecialchars($thema['th_desc']) . $f4; ?>
								</td>
								<td style="width:170px; text-align:center; vertical-align:middle;\">
									<a href="forum.php?id=<?php echo $id; ?>&th_id=<?php echo $thema['th_id']; ?>&aktion=thema_edit" class="button" title="<?php echo $t['button_editieren']; ?>"><span class="fa fa-pencil icon16"></span> <span><?php echo $t['button_editieren']; ?></span></a>
									<a href="forum.php?id=<?php echo $id; ?>&aktion=thema_delete&th_id=<?php echo $thema['th_id']; ?>" onClick="return ask('<?php echo $t['forum_loeschen']; ?>')" class="button" title="<?php echo $t['button_loeschen']; ?>"><span class="fa fa-trash icon16"></span> <span><?php echo $t['button_loeschen']; ?></span></a>
								</td>
								<td style="width:20px; text-align:center;">
									<a href="forum.php?id=<?php echo $id; ?>&th_id=<?php echo $thema['th_id']; ?>&fo_id=<?php echo $thema['fo_id']; ?>&th_order=<?php echo $thema['th_order']; ?>&aktion=thema_up" class="button"><span class="fa fa-arrow-up icon16"></span></a><br>
									<br>
									<a href="forum.php?id=<?php echo $id; ?>&th_id=<?php echo $thema['th_id']; ?>&fo_id=<?php echo $thema['fo_id']; ?>&th_order=<?php echo $thema['th_order']; ?>&aktion=thema_down" class="button"><span class="fa fa-arrow-down icon16"></span></a>
								</td>
							</tr>
						</table>
					</td>
					<?php
				} else {
					?>
					<td style="width:550px; font-weight:bold;" <?php echo $farbe; ?>>
						<?php echo $f1; ?>
						<a href="forum.php?id=<?php echo $id; ?>&th_id=<?php echo $thema['th_id']; ?>&aktion=show_thema"><?php echo htmlspecialchars($thema['th_name']) . $f2; ?></a><br>
						<?php echo $f3 . " " . htmlspecialchars($thema['th_desc']) . $f4; ?></td>
					<?php
				}
				?>
				<td style="text-align:center;" <?php echo $farbe; ?>><?php echo $f3 . $thema['th_anzthreads'] . ' ' . $t['kategorie_themen'] . $f4; ?></td>
				<td style="text-align:center;" <?php echo $farbe; ?>><?php echo $f3 . ( $thema['th_anzreplys'] + $thema['th_anzthreads']) . ' ' . $t['kategorie_beitraege'] . $f4; ?></td>
				</tr>
				<?php
			}
			$fo_id_last = $thema['fo_id'];
			$zeile++;
		}
	}
	?>
	</table>
	<?php
	show_icon_description("forum");
	mysqli_free_result($query);
}

//Zeigt Erklärung der verschiedenen Folder an
function show_icon_description($mode) {
	global $t, $f3, $f4, $chat_grafik;
	?>
	<br>
	<table class="tabelle_gerust2">
		<tr>
			<td style="width:20px; text-align:center;"><?php echo $chat_grafik['forum_ordnerneu']; ?></td>
			<td><?php echo "$f3 = $t[desc_folder] $f4"; ?></td>
		</tr>
		<tr>
			<td style="text-align:center;"><?php echo $chat_grafik['forum_ordnerblau']; ?></td>
			<td><?php echo "$f3 = $t[desc_redfolder] ($chat_grafik[forum_ordnervoll] = $t[desc_burningredfolder])$f4"; ?></td>
		</tr>
		<tr>
			<td style="text-align:center;"><?php echo $chat_grafik['forum_topthema']; ?></td>
			<td><?php echo "$f3 = $t[desc_topposting] $f4"; ?></td>
		</tr>
		<tr>
			<td style="width:20px; text-align:center;"><?php echo $chat_grafik['forum_threadgeschlossen']; ?></td>
			<td><?php echo "$f3 = $t[desc_threadgeschlossen] $f4"; ?></td>
		</tr>
	</table>
	<?php
}

//Eingabemaske für Thema
function maske_thema($th_id = 0) {
	global $id, $fo_id, $mysqli_link;
	global $f1, $f2;
	global $t;
	
	if ($th_id > 0) {
		$sql = "select th_name, th_desc from thema where th_id=" . intval($th_id);
		$query = mysqli_query($mysqli_link, $sql);
		$th_name = htmlspecialchars(mysqli_result($query, 0, "th_name"));
		$th_desc = htmlspecialchars(mysqli_result($query, 0, "th_desc"));
		mysqli_free_result($query);
		
		$button = $t['forum_speichern'];
	} else {
		$button = $t['forum_anlegen'];
	}
	
	if (!isset($th_name)) {
		$th_name = "";
		$kopfzeile = $t['forum_anlegen'];
	} else {
		$kopfzeile = str_replace("xxx", $th_name, $t['forum_editieren_mit_Name']);
	}
	
	if (!isset($th_desc)) {
		$th_desc = "";
	}
	?>
	<form action="forum.php" method="post">
		<table class="tabelle_gerust">
			<tr>
				<td class="tabelle_kopfzeile" colspan="2"><?php echo $kopfzeile; ?></td>
			</tr>
			</tr>
			<tr>
				<td style="width:300px; font-weight:bold;" class="tabelle_koerper_login"><?php echo $f1; ?><?php echo $t['forum_name']; ?><?php echo $f2; ?></td>
				<td class="tabelle_koerper_login"><input type="text" size="40" name="th_name" value="<?php echo $th_name; ?>"></td>
			</tr>
			<tr>
				<td style="font-weight:bold;" class="tabelle_koerper_login"><?php echo $f1; ?><?php echo $t['forum_beschreibung']; ?><?php echo $f2; ?></td>
				<td class="tabelle_koerper_login"><textarea name="th_desc" rows="3" cols="90"><?php echo $th_desc; ?></textarea></td></tr>
			<?php
			if ($th_id > 0) {
				?>
				<tr>
					<td style="font-weight:bold;" class="tabelle_koerper_login"><?php echo $f1; ?><?php echo $t['forum_verschieben']; ?><?php echo $f2; ?></td>
					<td class="tabelle_koerper_login">
						<input type="checkbox" name="th_forumwechsel" value="Y">
						<?php 
						$sql = "SELECT fo_id, fo_name FROM forum ORDER BY fo_order ";
						$query = mysqli_query($mysqli_link, $sql);
						?>
						<select name="th_verschiebe_nach" size="1">
							<?php
							while ($row = mysqli_fetch_object($query)) {
								echo "<option ";
								if ($row->fo_id == $fo_id) {
									echo "selected ";
								}
								echo "value=\"$row->fo_id\">$row->fo_name </option>";
							}
							?>
						</select>
					</td>
				</tr>
				<?php
				mysqli_free_result($query);
			}
			?>
			<tr>
				<td colspan="2" align="right" class="tabelle_koerper_login"><input type="submit" value="<?php echo $button; ?>"></td>
			</tr>
		</table>
		<input type="hidden" name="id" value="<?php echo $id; ?>">
		<input type="hidden" name="fo_id" value="<?php echo $fo_id; ?>">
		<?php
		if ($th_id > 0) {
			echo "<input type=\"hidden\" name=\"th_id\" value=\"$th_id\">\n";
			echo "<input type=\"hidden\" name=\"aktion\" value=\"thema_editieren\">";
		} else {
			echo "<input type=\"hidden\" name=\"aktion\" value=\"thema_anlegen\">";
		}
		?>
	</form>
	<?php
}

//Zeigt Pfad und Seiten in Themaliste an
function show_pfad($th_id) {
	global $mysqli_link, $f3, $f4, $id, $thread, $anzahl_po_seite;
	global $seite, $t, $farbe_hervorhebung_forum;
	
	//Infos über Forum und Thema holen
	$sql = "SELECT fo_id, fo_name, th_name, th_anzthreads
				FROM forum, thema
				WHERE th_id = " . intval($th_id) . "
				AND fo_id = th_fo_id";
	$query = mysqli_query($mysqli_link, $sql);
	$fo_id = htmlspecialchars(mysqli_result($query, 0, "fo_id"));
	$fo_name = htmlspecialchars(mysqli_result($query, 0, "fo_name"));
	$th_name = htmlspecialchars(mysqli_result($query, 0, "th_name"));
	$th_anzthreads = mysqli_result($query, 0, "th_anzthreads");
	mysqli_free_result($query);
	?>
	<table class="tabelle_gerust2">
		<tr>
			<td><?php echo $f3; ?><a href="forum.php?id=<?php echo $id; ?>#<?php echo $fo_id; ?>"><?php echo $fo_name; ?></a> > <a href="forum.php?id=<?php echo $id; ?>&th_id=<?php echo $th_id; ?>&show_tree=<?php echo $thread; ?>&aktion=show_thema&seite=<?php echo $seite; ?>"><?php echo $th_name; ?></a><?php echo $f4; ?></td>
		<?php
		if (!$anzahl_po_seite || $anzahl_po_seite == 0)
			$anzahl_po_seite = 20;
		$anz_seiten = ceil(($th_anzthreads / $anzahl_po_seite));
		if ($anz_seiten > 1) {
			echo "<td style=\"text-align:right;\">$f3 $t[page] ";
			for ($page = 1; $page <= $anz_seiten; $page++) {
				
				if ($page == $seite) {
					$col = "color:$farbe_hervorhebung_forum;";
				} else {
					$col = '';
				}
				echo "<a href=\"forum.php?id=$id&th_id=$th_id&aktion=show_thema&seite=$page\"><span style=\"$col;\">$page</span></a> ";
			}
			echo "$f4</td></tr>\n";
		} else {
			echo "</tr>\n";
		}
		?>
	</table>
	<?php
	
	return $th_name;
}

//Zeigt ein Thema mit allen Beiträgen an
function show_thema() {
	global $mysqli_link;
	global $id, $o_js, $forum_admin, $th_id, $show_tree, $seite;
	global $t, $f1, $f2, $f3, $f4;
	global $anzahl_po_seite, $chat_grafik;
	global $admin, $anzahl_po_seite2, $u_id;
	
	if ($anzahl_po_seite2) {
		$anzahl_po_seite2 = preg_replace("/[^0-9]/", "", $anzahl_po_seite2);
		$anzahl_po_seite = $anzahl_po_seite2;
		
		$f[u_forum_postingproseite] = $anzahl_po_seite2;
		
		if (!schreibe_db("user", $f, $u_id, "u_id")) {
			echo "Fehler beim Schreiben in DB!";
		}
		
	} else {
		$query = "SELECT `u_forum_postingproseite` FROM `user` WHERE `u_id` = '$u_id'";
		$result = mysqli_query($mysqli_link, $query);
		$a = mysqli_fetch_array($result);
		$anzahl_po_seite2 = $a['u_forum_postingproseite'];
		$anzahl_po_seite = $anzahl_po_seite2;
	}
	
	$leserechte = pruefe_leserechte($th_id);
	
	if (!$leserechte) {
		echo $t['leserechte'];
		exit;
	}
	if (!$seite)
		$seite = 1;
	
	$offset = ($seite - 1) * $anzahl_po_seite;
	
	$sql = "select po_id, po_u_id, date_format(from_unixtime(po_ts), '%d.%m.%y') as po_date,
				date_format(from_unixtime(po_threadts), '%d.%m.%y') as po_date2,
				po_titel, po_threadorder, po_topposting, po_threadgesperrt, po_gesperrt, u_nick,
		u_level, u_punkte_gesamt, u_punkte_gruppe, u_chathomepage
				from posting
				left join user on po_u_id = u_id
				where po_vater_id = 0
				and po_th_id = " . intval($th_id) . "
				order by po_topposting desc, po_threadts desc, po_ts desc
				limit $offset, $anzahl_po_seite";
	
	$query = mysqli_query($mysqli_link, $sql);
	
	$th_name = show_pfad($th_id);
	?>
	<table class="tabelle_gerust">
		<tr>
			<td colspan="5" class="tabelle_kopfzeile"><?php echo $th_name; ?></td>
			<td style="text-align:right;" class="tabelle_kopfzeile">
				<?php
				$schreibrechte = pruefe_schreibrechte($th_id);
				if ($schreibrechte) {
					?>
					<a href="forum.php?id=<?php echo $id; ?>&th_id=<?php echo $th_id; ?>&po_vater_id=0&aktion=thread_neu" class="button" title="<?php echo $t['thema_erstellen']; ?>"><span class="fa fa-plus icon16"></span> <span><?php echo $t['thema_erstellen']; ?></span></a>
					<?php
				} else {
					echo $f3 . $t[nur_leserechte] . $f4; ?><br>
					<?php
				}
				?> <a href="forum.php?id=<?php echo $id; ?>&th_id=<?php echo $th_id; ?>&aktion=thema_alles_gelesen" class="button" title="<?php echo $t['forum_alle_themen_als_gelesen_markieren']; ?>"><span class="fa fa-check icon16"></span> <span><?php echo $t['forum_alle_themen_als_gelesen_markieren']; ?></span></a>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="tabelle_kopfzeile">&nbsp;</td>
			<td style="text-align:center;" class="tabelle_kopfzeile"><?php echo $f3 . $t['autor'] . $f4; ?></td>
			<td style="text-align:center;" class="tabelle_kopfzeile"><?php echo $f3 . $t['forum_thema_erstellt_am'] . $f4; ?></td>
			<td style="text-align:center;" class="tabelle_kopfzeile"><?php echo $f3 . $t['forum_anzahl_antworten'] . $f4; ?></td>
			<td style="text-align:center;" class="tabelle_kopfzeile"><?php echo $f3 . $t['forum_letzte_Antwort'] . $f4; ?></td>
		</tr>
		<?php
		$zeile = 0;
		while ($posting = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
			set_time_limit(0);
			
			if ($zeile % 2) {
				$farbe = 'class="tabelle_zeile1"';
			} else {
				$farbe = 'class="tabelle_zeile2"';
			}
			
			if ($posting['po_threadorder'] == "0") {
				$anzreplys = 0;
				$arr_postings = array($posting['po_id']);
			} else {
				$arr_postings = explode(",", $posting['po_threadorder']);
				$anzreplys = count($arr_postings);
				//Ersten Beitrag mit beruecksichtigen
				$arr_postings[] = $posting['po_id'];
			}
			
			$ungelesene = anzahl_ungelesene($arr_postings, $th_id);
			
			array_pop($arr_postings);
			
			if ($ungelesene === 0) {
				if ($posting['po_topposting'] == 'Y') { // Topposting 
					$folder = $chat_grafik['forum_topthema'];
				} elseif ($posting['po_threadgesperrt'] == 'Y') { // Geschlossenes Thema
					$folder = $chat_grafik['forum_threadgeschlossen'];
				} else {
					$folder = $chat_grafik['forum_ordnerneu'];
				}
			} else if ($ungelesene < 11) {
				$folder = $chat_grafik['forum_ordnerblau'];
			} else {
				$folder = $chat_grafik['forum_ordnervoll'];
			}
			
			if ($ungelesene != 0) {
				$coli = "<span style=\"#ff0000\">";
				$colo = "</span>";
			} else {
				$coli = "";
				$colo = "";
			}
			
			echo "<tr><td style=\"text-align:center;\" $farbe>$folder</nobr></td>\n";
			
			
			if ($posting['po_gesperrt'] == 'Y' and !$forum_admin) {
				echo "<td $farbe style=\"font-weight:bold; font-size: smaller;\">&nbsp;"
					. substr($posting['po_titel'], 0, 40)
					. " <span style=\"color:#ff0000; \">(gesperrt)</span></td>\n";
			} elseif ($posting['po_gesperrt'] == 'Y' and $forum_admin) {
				echo "<td $farbe style=\"font-weight:bold; font-size: smaller;\">&nbsp;$f1<a href=\"forum.php?id=$id&th_id=$th_id&po_id=$posting[po_id]&thread=$posting[po_id]&aktion=show_posting&seite=$seite\">"
					. substr($posting['po_titel'], 0, 40)
					. "</a>$f2 <span style=\"color:#ff0000; \">(gesperrt)</span></td>\n";
			} else {
				echo "<td $farbe>&nbsp;$f1<b><a href=\"forum.php?id=$id&th_id=$th_id&po_id=$posting[po_id]&thread=$posting[po_id]&aktion=show_posting&seite=$seite\">"
					. substr($posting['po_titel'], 0, 40)
					. "</a></b>$f2</td>\n";
			}
			
			if (!$posting['u_nick']) {
				echo "<td $farbe>$f3<b>Nobody</b>$f4</td>\n";
			} else {
				
				$userdata = array();
				$userdata['u_id'] = $posting['po_u_id'];
				$userdata['u_nick'] = $posting['u_nick'];
				$userdata['u_level'] = $posting['u_level'];
				$userdata['u_punkte_gesamt'] = $posting['u_punkte_gesamt'];
				$userdata['u_punkte_gruppe'] = $posting['u_punkte_gruppe'];
				$userdata['u_chathomepage'] = $posting['u_chathomepage'];
				$userlink = user($posting['po_u_id'], $userdata, $o_js, FALSE,
					"&nbsp;", "", "", TRUE, FALSE, 29);
				if ($posting['u_level'] == 'Z') {
					echo "<td $farbe>$f1 $userdata[u_nick] $f2</td>\n";
				} else {
					echo "<td $farbe>$f1 $userlink $f2</td>\n";
				}
			}
			
			if ($posting['po_date2'] == '01.01.70' || $posting['po_date'] == $posting['po_date2']) {
				$antworten = "";
			} else {
				$antworten = "$f3" . substr($posting['po_date2'], 0, 5) . " von " . $posting['u_nick'] . "$f4";
			}
			echo "<td style=\"text-align:center;\" $farbe>$f3$posting[po_date]$f4</td>\n"; // Wann wurde das Thema erstellt
			echo "<td style=\"text-align:center;\" $farbe>$f3".$anzreplys."$f4</td>\n"; // Wie viele Antworten hat das Thema
			echo "<td style=\"text-align:center;\" $farbe>$antworten</td>\n"; // Letzte Anwort von wem und wann
			
			$zeile++;
			
		}
		?>
	
	</table>
	<?php
	show_pfad($th_id);
	show_icon_description("thema");
	?>
	<br>
	<table class="tabelle_gerust2">
		<tr>
			<td>
				<form action="forum.php">
				<?php echo $t['forum_postingsproseite']; ?> <input name="anzahl_po_seite2" size="3" maxlength="4" value="<?php echo $anzahl_po_seite; ?>">
				<input type="hidden" name="id" value="<?php echo $id; ?>">
				<input type="hidden" name="aktion" value="show_thema">
				<input type="hidden" name="th_id" value="<?php echo $th_id; ?>">
				<input type="submit" value="<?php echo $t['speichern']; ?>">
				</form>
			</td>
		</tr>
	</table>
	<?php
}

//Maske zum Eingeben/Editieren/Quoten von Beiträgen
function maske_posting($mode) {
	global $id, $u_id, $th_id, $po_id, $po_vater_id, $po_tiefe, $mysqli_link, $po_titel, $po_text, $thread, $seite;
	global $f1, $f2, $f3, $f4;
	global $t, $mysqli_link;
	global $forum_admin, $u_nick, $smilies_datei;
	
	$smilies_datei = "forum-" . $smilies_datei;
	
	switch ($mode) {
		
		case "neuer_thread":
			//$kopfzeile = $t['thema_erstellen'];
			$button = $t['neuer_thread_button'];
			$titel = $t['thema_erstellen'];
			
			if (!$po_text) {
				$po_text = erzeuge_fuss("");
			}
			
			break;
		case "reply": // zitieren
		//Daten des Vaters holen
			$sql = "SELECT date_format(from_unixtime(po_ts), '%d.%m.%Y, %H:%i:%s') AS po_date, po_tiefe,
								po_titel, po_text, ifnull(u_nick, 'unknown') AS u_nick
								FROM posting
								LEFT JOIN user ON po_u_id = u_id
								WHERE po_id = " . intval($po_vater_id);
			$query = mysqli_query($mysqli_link, $sql);
			
			$autor = mysqli_result($query, 0, "u_nick");
			$po_date = mysqli_result($query, 0, "po_date");
			$po_titel = (mysqli_result($query, 0, "po_titel"));
			if (substr($po_titel, 0, 3) != $t['reply'])
				$po_titel = $t['reply'] . " " . $po_titel;
			$titel = $po_titel;
			$po_text = mysqli_result($query, 0, "po_text");
			$po_text = erzeuge_quoting($po_text, $autor, $po_date);
			$po_text = erzeuge_fuss($po_text);
			
			$po_tiefe = mysqli_result($query, 0, "po_tiefe");
			
			//$kopfzeile = $po_titel;
			$button = $t['neuer_thread_button'];
			break;
		case "answer": // antworten
		//Daten des Vaters holen
			$sql = "SELECT po_tiefe, po_titel
								FROM posting
								WHERE po_id = " . intval($po_vater_id);
			$query = mysqli_query($mysqli_link, $sql);
			
			$po_titel = mysqli_result($query, 0, "po_titel");
			if (substr($po_titel, 0, 3) != $t['reply'])
				$po_titel = $t['reply'] . " " . $po_titel;
			$titel = $po_titel;
			$po_text = erzeuge_fuss("");
			
			$po_tiefe = mysqli_result($query, 0, "po_tiefe");
			
			//$kopfzeile = $po_titel;
			$button = $t['neuer_thread_button'];
			break;
		
		case "edit":
		//Daten holen
			$sql = "SELECT date_format(from_unixtime(po_ts), '%d.%m.%Y, %H:%i:%s') AS po_date, po_tiefe,
								po_titel, po_text, ifnull(u_nick, 'unknown') AS u_nick, u_id, po_threadgesperrt, po_topposting
								FROM posting
								LEFT JOIN user ON po_u_id = u_id
								WHERE po_id = " . intval($po_id);
			$query = mysqli_query($mysqli_link, $sql);
			
			$autor = mysqli_result($query, 0, "u_nick");
			$user_id = mysqli_result($query, 0, "u_id");
			$po_date = mysqli_result($query, 0, "po_date");
			$po_topposting = mysqli_result($query, 0, "po_topposting");
			$po_threadgesperrt = mysqli_result($query, 0, "po_threadgesperrt");
			$po_titel = mysqli_result($query, 0, "po_titel");
			
			$titel = $po_titel;
			// Entfernt alle "Re: ", falls diese vorhanden sind.
			$titel = str_replace("Re: ", "", $titel);
			
			$po_text = mysqli_result($query, 0, "po_text");
			$po_tiefe = mysqli_result($query, 0, "po_tiefe");
			
			//Testen ob Benutzer mogelt, indem er den Edit-Link mit anderer po_id benutzt
			if ((!$forum_admin) && ($user_id != $u_id)) {
				echo "wanna cheat eh? bad boy!";
				exit;
			}
			
			//$kopfzeile = $po_titel;
			$button = $t['edit_button'];
			
			break;
		
	}
	echo "<form name=\"form\" action=\"forum.php\" method=\"post\">";
	show_pfad_posting($th_id, $titel);
	
	
	// Titel kann nur beim Editieren des 1. Beitrags geändert werden
	if ( (($mode == "edit" || $mode == "answer" || $mode == "reply") && $po_id == $thread) || $mode == "neuer_thread" ) {
		// Weiter unten wird das Formularfeld angezeigt
	} else {
		?>
		<input type="hidden" name="po_titel" value="<?php echo $po_titel; ?>">
		<?php
	}
	?>
	<table class="tabelle_gerust">
		<tr>
			<td class="tabelle_kopfzeile" colspan="2"><?php echo $titel; ?></td>
		</tr>
		<?php
		if ( (($mode == "edit" || $mode == "answer" || $mode == "reply") && $po_id == $thread) || $mode == "neuer_thread" ) {
			?>
			<tr>
				<td style="width:200px; font-weight:bold;" class="tabelle_koerper_login"><?php echo $f1; ?><?php echo $t['posting_msg1']; ?><?php echo $f2; ?></td>
				<td style="width:560px;" class="tabelle_koerper_login"><input type="text" size="50" name="po_titel" value="<?php echo $po_titel; ?>"></td>
			</tr>
			<?php
		}
		?>
		<tr>
			<td colspan="2" style="font-weight:bold;" class="tabelle_koerper_login"><?php echo $f1; ?><?php echo $t['posting_msg2']; ?><?php echo $f2; ?><br>
				<?php
				echo $f3 . "($t[desc_posting])$f4\n";
				
				$link_smilies = "$smilies_datei?id=$id";
				
				$fenster = str_replace("+", "", $u_nick);
				$fenster = str_replace("-", "", $fenster);
				$fenster = str_replace("ä", "", $fenster);
				$fenster = str_replace("ö", "", $fenster);
				$fenster = str_replace("ü", "", $fenster);
				$fenster = str_replace("Ä", "", $fenster);
				$fenster = str_replace("Ö", "", $fenster);
				$fenster = str_replace("Ü", "", $fenster);
				$fenster = str_replace("ß", "", $fenster);
				?><br>
				<a href="<?php echo $link_smilies; ?>" target="640_<?php echo $fenster; ?>" onMouseOver="return(true)" onClick="neuesFenster('<?php echo $link_smilies; ?>');return(false)"><?php echo $f3 . $t['smilies'] . $f4; ?></a>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;" class="tabelle_koerper_login"><textarea name="po_text" rows="15" cols="95" wrap="physical"><?php echo $po_text; ?></textarea></td>
		</tr>
			<?php
	// Nur im Obersten Vater die TOP und gesperrt Einstellungen ändern lassen
	if ($forum_admin && ($mode == "edit") && $po_id == $thread) {
		echo "<tr><td style=\"font-weight:bold;\" class=\"tabelle_koerper_login\">$f1 $t[posting_thema_gesperrt] $f2</td>\n";
		echo "<td class=\"tabelle_koerper_login\"><SELECT NAME=\"po_threadgesperrt\"><OPTION ";
		if ($po_threadgesperrt == 'Y')
			echo "SELECTED ";
		echo "VALUE=\"Y\">Ja</OPTION><OPTION ";
		if ($po_threadgesperrt == 'N')
			echo "SELECTED ";
		echo "VALUE=\"N\">Nein</SELECT></td></tr>\n";
		
		echo "<tr><td style=\"font-weight:bold;\" class=\"tabelle_koerper_login\">$f1 $t[posting_thema_anpinnen] $f2</td>\n";
		echo "<td class=\"tabelle_koerper_login\"><SELECT NAME=\"po_topposting\"><OPTION ";
		if ($po_topposting == 'Y')
			echo "SELECTED ";
		echo "VALUE=\"Y\">Ja</OPTION><OPTION ";
		if ($po_topposting == 'N')
			echo "SELECTED ";
		echo "VALUE=\"N\">Nein</SELECT></td></tr>\n";
	}
	?>
	<tr>
		<td colspan="2" style="text-align:right;" class="tabelle_koerper_login"><input type="submit" value="<?php echo $button; ?>"></td>
	</tr>
	</table>
	<?php
	show_pfad_posting($th_id, $titel);
	echo "<input type=\"hidden\" name=\"id\" value=\"$id\">";
	echo "<input type=\"hidden\" name=\"th_id\" value=\"$th_id\">\n";
	
	if ($mode == "neuer_thread") {
		echo "<input type=\"hidden\" name=\"po_tiefe\" value=\"0\">\n";
		echo "<input type=\"hidden\" name=\"po_vater_id\" value=\"0\">\n";
	} else if (($mode == "reply") || ($mode == "answer")) {
		$tiefe = 1; // Die Tiefe beim antworten und zitieren immer auf 1 setzen, um eine falche Struktur zu erzeugen
		//$tiefe = $po_tiefe + 1;
		echo "<input type=\"hidden\" name=\"thread\" value=\"$thread\">\n";
		echo "<input type=\"hidden\" name=\"po_tiefe\" value=\"$tiefe\">\n";
		echo "<input type=\"hidden\" name=\"po_vater_id\" value=\"$po_vater_id\">\n";
		echo "<input type=\"hidden\" name=\"show_tree\" value=\"$thread\">\n";
	} else {
		$tiefe = $po_tiefe;
		echo "<input type=\"hidden\" name=\"thread\" value=\"$thread\">\n";
		echo "<input type=\"hidden\" name=\"po_tiefe\" value=\"$tiefe\">\n";
		echo "<input type=\"hidden\" name=\"po_id\" value=\"$po_id\">\n";
		echo "<input type=\"hidden\" name=\"user_id\" value=\"$user_id\">\n";
	}
	
	echo "<input type=\"hidden\" name=\"aktion\" value=\"posting_anlegen\">";
	echo "<input type=\"hidden\" name=\"seite\" value=\"$seite\">";
	echo "<input type=\"hidden\" name=\"mode\" value=\"$mode\">";
	echo "</form>";
}

//Zeigt die gutgeschriebenen Punkte an
function verbuche_punkte($u_id) {
	global $t, $punkte_pro_posting;
	global $punktefeatures;
	
	if ($punktefeatures) {
		?>
		<table class="tabelle_gerust">
			<tr>
				<td class="tabelle_koerper_login" style="font-weight:bold;"><?php echo $t['forum_punkte1'] . punkte_offline($punkte_pro_posting, $u_id); ?></td>
			</tr>
		</table>
		<br>
		<?php
	}
}

//Zeigt Pfad in Beiträgen an
function show_pfad_posting($th_id, $po_titel)
{
	
	global $mysqli_link, $f3, $f4, $id, $thread, $seite;
	//Infos über Forum und Thema holen
	$sql = "select fo_id, fo_name, th_name
				from forum, thema
				where th_id = " . intval($th_id) . "
				and fo_id = th_fo_id";
	$query = mysqli_query($mysqli_link, $sql);
	$fo_id = htmlspecialchars(mysqli_result($query, 0, "fo_id"));
	$fo_name = htmlspecialchars(mysqli_result($query, 0, "fo_name"));
	$th_name = htmlspecialchars(mysqli_result($query, 0, "th_name"));
	mysqli_free_result($query);
	?>
	<table class="tabelle_gerust2">
		<tr>
			<td><?php echo $f3; ?><a href="forum.php?id=<?php echo $id; ?>#<?php echo $fo_id; ?>"><?php echo $fo_name; ?></a> > <a href="forum.php?id=<?php echo $id; ?>&th_id=<?php echo $th_id; ?>&show_tree=<?php echo $thread; ?>&aktion=show_thema&seite=<?php echo $seite; ?>"><?php echo $th_name; ?></a> > <?php echo $po_titel . $f4; ?></td>
			</tr>
	</table>
	<?php
	
}

// Gibt Navigation für Beiträge aus
function navigation_beitrag(
	$po_id,
	$po_u_id,
	$th_id,
	$user_nick = "") {
	global $t, $seite;
	global $id, $u_id, $thread, $forum_admin;
	?>
	<tr>
		<td class="tabelle_kopfzeile" style="text-align:right;">
		<?php
		$threadgesperrt = ist_thread_gesperrt($thread);
		$schreibrechte = pruefe_schreibrechte($th_id);
		//darf user posting bearbeiten
		//entweder eigenes posting oder forum_admin
		if ((($u_id == $po_u_id && !$threadgesperrt) || ($forum_admin)) && ($schreibrechte)) {
			?>
			<a href="forum.php?id=<?php echo $id; ?>&th_id=<?php echo $th_id; ?>&po_id=<?php echo $po_id; ?>&thread=<?php echo $thread; ?>&aktion=edit&seite=<?php echo $seite; ?>" class="button" title="<?php echo $t['thema_editieren']; ?>"><span class="fa fa-pencil icon16"></span> <span><?php echo $t['thema_editieren']; ?></span></a>
			<?php
		}
		
		// Privat antworten
		if ($user_nick && $schreibrechte) {
			
			// Beim Target die Sonderzeichen rausamachen
			$pfenster = str_replace("+", "", $user_nick);
			$pfenster = str_replace("-", "", $pfenster);
			$pfenster = str_replace("ä", "", $pfenster);
			$pfenster = str_replace("ö", "", $pfenster);
			$pfenster = str_replace("ü", "", $pfenster);
			$pfenster = str_replace("Ä", "", $pfenster);
			$pfenster = str_replace("Ö", "", $pfenster);
			$pfenster = str_replace("Ü", "", $pfenster);
			$pfenster = str_replace("ß", "", $pfenster);
			
			$mailurl = "mail.php?aktion=antworten_forum&id=$id&th_id=$th_id&po_vater_id=$po_id&thread=$thread";
			?>
			<a href="<?php echo $mailurl; ?>" target="640_<?php echo $pfenster; ?>" onMouseOver="return(true)" onClick="neuesFenster2('<?php echo $mailurl; ?>'); return(false)" class="button" title="<?php echo $t['thema_privat']; ?>"><span class="fa fa-envelope icon16"></span> <span><?php echo $t['thema_privat']; ?></span></a>
		<?php
		}
		
		if ($schreibrechte && !$threadgesperrt) {
			?>
			<a href="forum.php?id=<?php echo $id; ?>&th_id=<?php echo $th_id; ?>&po_vater_id=<?php echo $po_id; ?>&thread=<?php echo $thread; ?>&aktion=reply&seite=<?php echo $seite; ?>" class="button" title="<?php echo $t['thema_zitieren']; ?>"><span class="fa fa-quote-right icon16"></span> <span><?php echo $t['thema_zitieren']; ?></span></a>
			<?php
		}
	
		//Nur Forum-Admins dürfen Beiträge loeschen
		if ($forum_admin) {
			?>
			<a href="forum.php?id=<?php echo $id; ?>&th_id=<?php echo $th_id; ?>&po_id=<?php echo $po_id; ?>&thread=<?php echo $thread; ?>&aktion=sperre_posting&seite=<?php echo $seite; ?>" class="button" title="<?php echo $t['thema_sperren']; ?>"><span class="fa fa-lock icon16"></span> <span><?php echo $t['thema_sperren']; ?></span></a>
			<a href="forum.php?id=<?php echo $id; ?>&th_id=<?php echo $th_id; ?>&po_id=<?php echo $po_id; ?>&thread=<?php echo $thread; ?>&aktion=delete_posting&seite=<?php echo $seite; ?>" onClick="return ask('<?php echo $t['thema_loeschen2']; ?>')" class="button" title="<?php echo $t['thema_loeschen']; ?>"><span class="fa fa-trash icon16"></span> <span><?php echo $t['thema_loeschen']; ?></span></a>
			<?php
		}
		?>
		</td>
	</tr>
	<?php
}

//gibt Navigation für Beiträge aus
function navigation_posting(
	$po_titel,
	$po_u_id,
	$th_id,
	$user_nick = "",
	$ist_navigation_top = FALSE) {
	global $t, $seite;
	global $id, $po_id, $u_id, $thread, $forum_admin;
	?>
	<table class="tabelle_gerust">
		<tr>
			<td class="tabelle_kopfzeile">
		<?php
		if($ist_navigation_top) {
			echo "<b>" . $po_titel . "</b>";
		}
		?>
			</td>
		<tr>
		<td class="tabelle_kopfzeile" style="text-align:right;">
		<?php
		$threadgesperrt = ist_thread_gesperrt($thread);
		$schreibrechte = pruefe_schreibrechte($th_id);
		//darf user posting bearbeiten
		//entweder eigenes posting oder forum_admin
		if ( ((($u_id == $po_u_id && !$threadgesperrt) || ($forum_admin)) && ($schreibrechte)) && $ist_navigation_top ) {
			?>
			<a href="forum.php?id=<?php echo $id; ?>&th_id=<?php echo $th_id; ?>&po_id=<?php echo $po_id; ?>&thread=<?php echo $thread; ?>&aktion=edit&seite=<?php echo $seite; ?>" class="button" title="<?php echo $t['thema_editieren']; ?>"><span class="fa fa-pencil icon16"></span> <span><?php echo $t['thema_editieren']; ?></span></a>
			<?php
		}
		
		if ($schreibrechte && !$threadgesperrt && !$ist_navigation_top) {
			?>
			<a href="forum.php?id=<?php echo $id; ?>&th_id=<?php echo $th_id; ?>&po_vater_id=<?php echo $po_id; ?>&thread=<?php echo $thread; ?>&aktion=answer&seite=<?php echo $seite; ?>" class="button" title="<?php echo $t['thema_antworten']; ?>"><span class="fa fa-reply icon16"></span> <span><?php echo $t['thema_antworten']; ?></span></a>
			<?php
		}
	
		//Nur Forum-Admins dürfen Beiträge loeschen
		if ($forum_admin && $ist_navigation_top) {
			?>
				<a href="forum.php?id=<?php echo $id; ?>&th_id=<?php echo $th_id; ?>&po_id=<?php echo $po_id; ?>&thread=<?php echo $thread; ?>&aktion=sperre_posting&seite=<?php echo $seite; ?>" class="button" title="<?php echo $t['thema_sperren']; ?>"><span class="fa fa-lock icon16"></span> <span><?php echo $t['thema_sperren']; ?></span></a>
				<a href="forum.php?id=<?php echo $id; ?>&th_id=<?php echo $th_id; ?>&po_id=<?php echo $po_id; ?>&thread=<?php echo $thread; ?>&aktion=delete_posting&seite=<?php echo $seite; ?>" onClick="return ask('<?php echo $t['thema_loeschen2']; ?>')" class="button" title="<?php echo $t['thema_loeschen']; ?>"><span class="fa fa-trash icon16"></span> <span><?php echo $t['thema_loeschen']; ?></span></a>
			<?php
			if ($po_id == $thread) {
				?>
				<a href="forum.php?id=<?php echo $id; ?>&th_id=<?php echo $th_id; ?>&thread=<?php echo $thread; ?>&aktion=verschiebe_posting&seite=<?php echo $seite; ?>" class="button" title="<?php echo $t['thema_verschieben']; ?>"><span class="fa fa-arrows icon16"></span> <span><?php echo $t['thema_verschieben']; ?></span></a>
				<?php
			}
		}
		?>
		</td>
	</tr>
	</table>
	<?php
	// Alle Beiträge automatisch als gelesen markieren, wenn man das Thema aufgerufen hat
	thread_alles_gelesen($th_id, $thread, $u_id);
}

// Verschiebe Beitrag
function verschiebe_posting() {
	global $id, $mysqli_link, $po_id, $thread, $seite;
	global $f1, $f2;
	global $t, $o_js, $th_id, $fo_id;
	
	$sql = "select po_th_id, date_format(from_unixtime(po_ts), '%d.%m.%Y, %H:%i:%s') as po_date, po_tiefe,
				po_titel, po_text, po_u_id, ifnull(u_nick, 'Nobody') as u_nick,
		u_email, u_id, u_level,u_punkte_gesamt,u_punkte_gruppe,u_chathomepage
				from posting
				left join user on po_u_id = u_id
				where po_id = " . intval($thread);
	
	$query = mysqli_query($mysqli_link, $sql);
	if ($query)
		$row = mysqli_fetch_object($query);
	mysqli_free_result($query);
	
	$sql = "SELECT fo_name, th_name FROM forum left join thema on fo_id = th_fo_id WHERE th_id = " . intval($th_id);
	$query = mysqli_query($mysqli_link, $sql);
	if ($query)
		$row2 = mysqli_fetch_object($query);
	mysqli_free_result($query);
	
	$sql = "SELECT fo_name, th_id, th_name FROM forum left join thema on fo_id = th_fo_id "
		. "WHERE th_name <> 'dummy-thema' " . "ORDER BY fo_order, th_order ";
	$query = mysqli_query($mysqli_link, $sql);
	?>
	<form action="forum.php" method="post">
		<table class="tabelle_gerust">
			<tr>
				<td class="tabelle_kopfzeile" style="font-weight:bold;" colspan="2"><?php echo $t['verschieben1']; ?> <?php echo $row->po_titel; ?></td>
			</tr>
			<tr>
				<td class="tabelle_koerper_login"><?php echo $f1 . $t['verschieben2'] . $f2; ?></td>
				<td class="tabelle_koerper_login"><?php echo $row2->fo_name . " > " . $row2->th_name; ?></td>
			</tr>
			<tr>
				<td class="tabelle_koerper_login"><?php echo $f1 . $t['verschieben3'] . $f2; ?></td>
				<td class="tabelle_koerper_login">
					<select name="verschiebe_nach" size="1">
						<?php
						while ($row3 = mysqli_fetch_object($query)) {
							echo "<option ";
							if ($row3->th_id == $th_id) {
								echo "selected ";
							}
							echo "value=\"$row3->th_id\">$row3->fo_name > $row3->th_name </option>";
						}
						?>
					</select>
				</td>
			</tr>
			<?php
			mysqli_free_result($query);
			?>
			<tr>
				<td style="text-align:center;" colspan="2" class="tabelle_koerper_login"><input type="submit" name="los" value="<?php echo $t['verschieben4']; ?>"></td>
			</tr>
		</table>
		
		<input type="hidden" name="id" value="<?php echo $id; ?>">
		<input type="hidden" name="thread_verschiebe" value="<?php echo $thread; ?>">
		<input type="hidden" name="seite" value="<?php echo $seite; ?>">
		<input type="hidden" name="verschiebe_von" value="<?php echo $th_id; ?>">
		<input type="hidden" name="fo_id" value="<?php $fo_id; ?>">
		<input type="hidden" name="aktion" value="verschiebe_posting_ausfuehren">
	</form>
	<?php
}

// Zeigt das Thema an
function show_posting() {
	global $id, $mysqli_link, $po_id, $thread, $seite;
	global $t, $o_js, $forum_admin;
	
	$sql = "SELECT po_th_id, date_format(from_unixtime(po_ts), '%d.%m.%Y, %H:%i:%s') AS po_date, po_tiefe,
				po_titel, po_text, po_u_id, po_gesperrt, ifnull(u_nick, 'Nobody') AS u_nick,
		u_email, u_id, u_level,u_punkte_gesamt,u_punkte_gruppe,u_chathomepage
				FROM posting
				LEFT JOIN user ON po_u_id = u_id
				WHERE po_id = " . intval($po_id);
	
	$query = mysqli_query($mysqli_link, $sql);
	if ($query)
		$row = mysqli_fetch_object($query);
	$th_id = $row->po_th_id;
	$po_u_id = $row->po_u_id;
	$po_date = $row->po_date;
	$po_tiefe = $row->po_tiefe;
	$po_titel = $row->po_titel;
	$po_gesperrt = $row->po_gesperrt;
	
	if ($po_gesperrt == 'Y' and !$forum_admin) {
		echo ('Beitrag gesperrt');
		return;
	}
	
	$po_text = ersetzte_smilies(chat_parse(nl2br($row->po_text)));
	if (($row->u_nick != "Nobody") && ($row->u_level <> "Z")) {
		$autor = user($po_u_id, $row, $o_js, FALSE, "&nbsp;", "", "", TRUE,
			TRUE);
	} else {
		$autor = $row->u_nick;
	}
	
	mysqli_free_result($query);
	
	$sql = "SELECT po_threadorder FROM posting WHERE po_th_id= $th_id";
	$query = mysqli_query($mysqli_link, $sql);
	$po_threadorder = mysqli_result($query, 0, "po_threadorder");
	
	mysqli_free_result($query);
	
	show_pfad_posting($th_id, $po_titel);
	navigation_posting($po_titel, $po_u_id, $th_id, $row->u_nick, TRUE);
	?>
	<table class="tabelle_gerust">
		<tr>
			<td colspan="2" class="tabelle_zeile1"></td>
		</tr>
		<?php
		
		zeige_beitraege($thread);
		?>
	</table>
	<?php
	navigation_posting($po_titel, $po_u_id, $th_id, $row->u_nick, FALSE);
	show_pfad_posting($th_id, $po_titel);
}

// Zeigt alle Beiträge aus einem Thema
function zeige_beitraege($thread) {
	global $mysqli_link, $f1, $f2, $seite, $th_id;
	global $t, $id, $u_id, $o_js, $forum_admin;
	
	// Vom Benutzer gelesene Beiträge holen
	$sql = "SELECT `u_gelesene_postings` FROM `user` WHERE `u_id`=$u_id";
	$query = mysqli_query($mysqli_link, $sql);
	if (mysqli_num_rows($query) > 0) {
		$gelesene = mysqli_result($query, 0, "u_gelesene_postings");
	}
	$u_gelesene = unserialize($gelesene);
	
	$sql = "SELECT po_id, po_th_id, date_format(from_unixtime(po_ts), '%d.%m.%Y, %H:%i:%s') AS po_date, po_tiefe,
				po_titel, po_text, po_u_id, u_nick, u_level, u_punkte_gesamt, u_punkte_gruppe, u_chathomepage, po_threadorder, po_gesperrt
				FROM posting
				LEFT JOIN user ON po_u_id = u_id
				WHERE po_id = $thread OR po_vater_id = $thread";
	
	$query = mysqli_query($mysqli_link, $sql);
	$alle_beitraege = mysqli_query($mysqli_link, $sql);
	
	mysqli_free_result($query);
	
	//echo "<table style=\"width:100%;\">\n";
	
	// Beiträge in der richtigen Reihenfolge durchlaufen
	$letztes = array();
	
	foreach($alle_beitraege as $zaehler => $beitrag) {
		$span = 0;
		$tiefe = $beitrag['po_tiefe'];
		
		$po_text = ersetzte_smilies(chat_parse(nl2br( $beitrag['po_text'] )));
		$po_date = $beitrag['po_date'];
		$po_gesperrt = $beitrag['po_gesperrt'];
		$po_u_id = $beitrag['po_u_id'];
		$po_th_id = $beitrag['po_th_id'];
		$po_id = $beitrag['po_id'];
		$po_u_nick = $beitrag['u_nick'];
		
		$po_threadorder = $beitrag['po_threadorder'];
		$po_u_level = $beitrag['u_level'];
		$po_u_punkte_gesamt = $beitrag['u_punkte_gesamt'];
		$po_u_punkte_gruppe = $beitrag['u_punkte_gruppe'];
		$po_u_chathomepage = $beitrag['u_chathomepage'];
		
		if ($tiefe >= 1) { // Tiefe immer auf 1 setzen, um eine flache Struktur zu erzeugen
			$tiefe = 1;
		}
		
		
		$letztes[$tiefe] = $po_threadorder;
		
		if (!@in_array($po_id, $u_gelesene[$th_id])) {
			$col = ' <span class="fa fa-star icon16" alt="ungelesener Beitrag" title="ungelesener Beitrag"></span>';
		} else {
			$col = '';
		}
		
		if ($po_gesperrt == 'Y') {
			$besonderer_status = " <span style=\"color:#ff0000;\">(gesperrt)</span>" . $col;
		} else {
			$besonderer_status = $col;
		}
		
		if (!($po_th_id)) {
			$userdetails = "gelöschter Benutzer";
		} else {
			
			$userdata = array();
			$userdata['u_id'] = $po_u_id;
			$userdata['u_nick'] = $po_u_nick;
			$userdata['u_level'] = $po_u_level;
			$userdata['u_punkte_gesamt'] = $po_u_punkte_gesamt;
			$userdata['u_punkte_gruppe'] = $po_u_punkte_gruppe;
			$userdata['u_chathomepage'] = $po_u_chathomepage;
			$userlink = user($po_u_id, $userdata, $o_js, FALSE, "&nbsp;", "", "", TRUE, FALSE, 29);
			if ($po_u_level == 'Z') {
				$userdetails = "$userdata[u_nick]";
			} else {
				$userdetails = "$userlink";
			}
		}
		
		
		// Start des Avatars
		$query2 = "SELECT * FROM user WHERE u_nick = '$userdata[u_nick]'";
		$result2 = mysqli_query($mysqli_link, $query2);
		
		if ($result2 && mysqli_num_rows($result2) == 1) {
			$row2 = mysqli_fetch_object($result2);
			
			$uu_id = $row2->u_id;
			$ui_avatar = $row2->ui_avatar;
			
		}
		
		$query1 = "SELECT * FROM userinfo WHERE ui_userid = '$uu_id'";
		$result1 = mysqli_query($mysqli_link, $query1);
		
		if ($result1 && mysqli_num_rows($result1) == 1) {
			$row1 = mysqli_fetch_object($result1);
			
			$ui_gen = $row1->ui_geschlecht;
		} else {
			$ui_gen = 'leer';
		}
		
		if($result2 && mysqli_num_rows($result2) == 1) {
			if($ui_avatar) { // Benutzerdefinierter Avatar
				$ava = '<img src="./avatars/'.$ui_avatar.'" style="width:60px; height:60px;" alt="'.$ui_avatar.'" />';
			} else if ($ui_gen[0] == "m") { // Männlicher Standard-Avatar
				$ava = '<img src="./avatars/no_avatar_m.jpg" style="width:60px; height:60px;" alt="" />';
			} else if ($ui_gen[0] == "w") { // Weiblicher Standard-Avatar
				$ava = '<img src="./avatars/no_avatar_w.jpg" style="width:60px; height:60px;" alt="" />';
			} else { // Neutraler Standard-Avatar
				$ava = '<img src="./avatars/no_avatar_es.jpg" style="width:60px; height:60px;" alt="" />';
			}
		} else {
			$ava = '<img src="./avatars/no_avatar_es.jpg" style="width:60px; height:60px;" alt="" />';
		}
		// Ende des Avatars
		
		
		?>
		<tr>
			<td rowspan="3" class="tabelle_kopfzeile" style="width:150px; vertical-align:top;">
				<center>
				<?php echo $ava; ?><br>
				<?php echo $f1 . $userdetails . $besonderer_status . $f2; ?>
				</center>
			</td>
			<td class="tabelle_kopfzeile"><?php echo $f1 . $t['datum'] . $po_date . " " . $t['autor'] . " " . $userdetails . $besonderer_status . $f2; ?></td>
		</tr>
		<tr>
			<td class="tabelle_zeile1"><?php echo $f1 . $po_text . $f2; ?></td>
		</tr>
		<?php
		
		navigation_beitrag($po_id, $po_u_id, $po_th_id, $po_u_nick);
		?>
		<tr>
		<td colspan="2" class="tabelle_zeile1"></td>
		</tr>
		<?php
	}
	//echo "</table>";
}
?>