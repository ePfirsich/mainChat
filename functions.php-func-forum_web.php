<?php

//Kopf fuer das Forum
function kopf_forum($admin) {
	global $http_host, $id, $u_nick, $menue;
	global $f1, $f2, $farbe_chat_background1, $grafik_background1, $farbe_chat_text1, $farbe_chat_link1;
	global $farbe_chat_vlink1, $stylesheet, $chat, $body_titel;
	global $aktion;
	
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
	zeige_header_anfang($title, $farbe_chat_background1, $grafik_background1, $farbe_chat_link1, $farbe_chat_vlink1);
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
	<?php
	// zeige_kopf();
	
	if (($admin) && (!$aktion)) {
		$lnk[1] = $f1 . "&nbsp;[<a href=\"forum.php?http_host=$http_host&id=$id&aktion=forum_neu\">$menue[1]</a>]" . $f2;
	}
	
	if (!isset($lnk[1])) {
		$lnk[1] = "";
	}
	?>
	<table style="width:900px; margin:auto;">
		<tr>
			<td><b><a href="forum.php?id=<?php echo $id; ?>&http_host=<?php echo $http_host; ?>"><?php echo $chat; ?>-Forum</a><?php echo $lnk[1]; ?></b></td>
		</tr>
	</table>
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
	
	echo "<p style=\"color:$farbe_hervorhebung_forum; font-weight:bold;\">$missing</p>";
}

//Eingabemaske für neues Forum
function maske_forum($fo_id = 0) {
	global $id, $http_host, $mysqli_link;
	global $f1, $f2, $farbe_tabelle_kopf2;
	global $t;
	
	if ($fo_id > 0) {
		$fo_id = intval($fo_id);
		$sql = "select fo_name, fo_admin from forum where fo_id=$fo_id";
		$query = mysqli_query($mysqli_link, $sql);
		$fo_name = htmlspecialchars(mysqli_result($query, 0, "fo_name"));
		$fo_admin = mysqli_result($query, 0, "fo_admin");
		@mysqli_free_result($query);
		
		$kopfzeile = str_replace("xxx", $fo_name, $t['forum_edit']);
		$button = $t['forum_edit_button'];
		
	} else {
		$kopfzeile = $t['forum_neu'];
		$button = $t['forum_button'];
		
	}
	?>
	<form action="forum.php" method="post">
		<table class="tabelle_gerust">
			<tr>
				<td class="tabelle_kopfzeile" colspan="2"><?php echo $kopfzeile; ?></td>
			</tr>
			<tr>
				<td style="width:260px; font-weight:bold;" class="tabelle_koerper_login"><?php echo $f1; ?><?php echo $t['forum_msg1']; ?><?php echo $f2; ?></td>
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
			// Forumsrechte für einen User einstellen
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
		<input type="hidden" name="http_host" value="<?php echo $http_host; ?>">
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
	global $id, $http_host, $forum_admin, $chat_grafik;
	global $t, $f1, $f2, $f3, $f4, $farbe_tabelle_kopf2;
	global $u_level;
	
	$sql = "select fo_id, fo_name, fo_order, fo_admin,
				th_id, th_fo_id, th_name, th_desc, th_anzthreads, th_anzreplys, th_order, th_postings
				from forum, thema
				where fo_id = th_fo_id ";
	if ($u_level == "G")
		$sql .= "and ( ((fo_admin & 8) = 8) or fo_admin = 0) ";
	if ($u_level == "U" || $u_level == "A" || $u_level == "M"
		|| $u_level == "Z")
		$sql .= "and ( ((fo_admin & 2) = 2) or fo_admin = 0) ";
	
	$sql .= "order by fo_order, th_order";
	
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
									<td style="width:85px; text-align:right; vertical-align:middle;">
										<a href="forum.php?id=<?php echo $id; ?>&http_host=<?php echo $http_host; ?>&aktion=forum_delete&fo_id=<?php echo $thema['fo_id']; ?>" onClick="return ask('<?php echo $t['conf_delete_forum']; ?>')"><?php echo $chat_grafik['forum_loeschen']; ?></a>&nbsp;
									</td>
									<td style="width:85px; text-align:right; vertical-align:middle;">
										<a href="forum.php?id=<?php echo $id; ?>&http_host=<?php echo $http_host; ?>&aktion=forum_edit&fo_id=<?php echo $thema['fo_id']; ?>"><?php echo $chat_grafik['forum_editieren']; ?></a>&nbsp;
									</td>
									<td style="width:90px; text-align:right; vertical-align:middle;">
										&nbsp;<a href="forum.php?id=<?php echo $id; ?>&http_host=<?php echo $http_host; ?>&fo_id=<?php echo $thema['fo_id']; ?>&aktion=thema_neu"><?php echo $chat_grafik['forum_neuesthema']; ?></a>&nbsp;
									</td>
									<td style="width:17px;">
										<a href="forum.php?id=<?php echo $id; ?>&http_host=<?php echo $http_host; ?>&fo_id=<?php echo $thema['fo_id']; ?>&fo_order=<?php echo $thema['fo_order']; ?>&aktion=forum_up"><?php echo $chat_grafik['forum_pfeil_oben']; ?></a><br>
										<img src="pics/fuell.gif" style="width:1px; height:1px; border:0px;"><br>
										<a href="forum.php?id=<?php echo $id; ?>&http_host=<?php echo $http_host; ?>&fo_id=<?php echo $thema['fo_id']; ?>&fo_order=<?php echo $thema['fo_order']; ?>&aktion=forum_down"><?php echo $chat_grafik['forum_pfeil_unten']; ?></a>
									</td>
									<?php
								}
								?>
								</tr>
							</table>
						</td>
						<td class="tabelle_kopfzeile" style="width:67px; text-align:center;"><?php echo $f1 . $t['anzbeitraege'] . $f2; ?></td>
						<td class="tabelle_kopfzeile" style="width:66px; text-align:center;"><?php echo $f1 . $t['anzthreads'] . $f2; ?></td>
						<td class="tabelle_kopfzeile" style="width:66px; text-align:center;"><?php echo $f1 . $t['anzreplys'] . $f2; ?></td>
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
				
				echo "<td width=\"30\" style=\"text-align:center;\" $farbe>$folder</td>";
				if ($forum_admin) {
					?>
					<td style="width:550px;" <?php echo $farbe; ?>>
						<table style="width:100%;">
							<tr>
								<td style="width:385px; font-weight:bold;">
									<?php echo $f1; ?>
									<a href="forum.php?id=<?php echo $id; ?>&http_host=<?php echo $http_host; ?>&th_id=<?php echo $thema['th_id']; ?>&aktion=show_thema">
									<?php echo htmlspecialchars($thema['th_name']) . "</a>$f2<br>" . $f3 . " " . htmlspecialchars($thema['th_desc']) . $f4; ?>
								</td>
								<td style="width:85px; text-align:center; vertical-align:middle;\">
									<a href="forum.php?id=<?php echo $id; ?>&http_host=<?php echo $http_host; ?>&aktion=thema_delete&th_id=<?php echo $thema['th_id']; ?>" onClick="return ask('<?php echo $t['conf_delete_thema']; ?>')"><?php echo $chat_grafik['forum_loeschen']; ?></a>
								</td>
								<td style="width:85px; text-align:center; vertical-align:middle;">
									<a href="forum.php?id=<?php echo $id; ?>&http_host=<?php echo $http_host; ?>&th_id=<?php echo $thema['th_id']; ?>&aktion=thema_edit"><?php echo $chat_grafik['forum_themabearbeiten']; ?></a>
								</td>
								<td style="width:20px; text-align:center;">
									<a href="forum.php?id=<?php echo $id; ?>&http_host=<?php echo $http_host; ?>&th_id=<?php echo $thema['th_id']; ?>&fo_id=<?php echo $thema['fo_id']; ?>&th_order=<?php echo $thema['th_order']; ?>&aktion=thema_up"><?php echo $chat_grafik['forum_pfeil_oben']; ?></a><br>
									<img src="pics/fuell.gif" style="width:1px; height:1px; border:0px;"><br>
									<a href="forum.php?id=<?php echo $id; ?>&http_host=<?php echo $http_host; ?>&th_id=<?php echo $thema['th_id']; ?>&fo_id=<?php echo $thema['fo_id']; ?>&th_order=<?php echo $thema['th_order']; ?>&aktion=thema_down"><?php echo $chat_grafik['forum_pfeil_unten']; ?></a>
								</td>
							</tr>
						</table>
					</td>
					<?php
				} else {
					?>
					<td style="width:550px; font-weight:bold;" <?php echo $farbe; ?>>
						<?php echo $f1; ?>
						<a href="forum.php?id=<?php echo $id; ?>&http_host=<?php echo $http_host; ?>&th_id=<?php echo $thema['th_id']; ?>&aktion=show_thema"><?php echo htmlspecialchars($thema['th_name']) . $f2; ?></a><br>
						<?php echo $f3 . " " . htmlspecialchars($thema['th_desc']) . $f4; ?></td>
					<?php
				}
				?>
				<td style="text-align:center;" <?php echo $farbe; ?>><?php echo $f1 . $ungelesene . $f2; ?></td>
				<td style="text-align:center;" <?php echo $farbe; ?>><?php echo $f1 . $thema['th_anzthreads'] . $f2; ?></td>
				<td style="text-align:center;" <?php echo $farbe; ?>><?php echo $f1 . $thema['th_anzreplys'] . $f2; ?></td>
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
	@mysqli_free_result($query);
}

//Zeigt Erklärung der verschiedenen Folder an
function show_icon_description($mode) {
	global $t, $f3, $f4, $chat_grafik;
	?>
	<br>
	<table style="width:900px; margin:auto;">
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
	global $id, $http_host, $fo_id, $mysqli_link;
	global $f1, $f2, $farbe_tabelle_kopf2;
	global $t, $chat_grafik;
	
	if ($th_id > 0) {
		$sql = "select th_name, th_desc from thema where th_id=" . intval($th_id);
		$query = mysqli_query($mysqli_link, $sql);
		$th_name = htmlspecialchars(mysqli_result($query, 0, "th_name"));
		$th_desc = htmlspecialchars(mysqli_result($query, 0, "th_desc"));
		@mysqli_free_result($query);
		
		$kopfzeile = $chat_grafik['forum_themabearbeiten'];
		$button = $t['thema_button_edit'];
	} else {
		$kopfzeile = $chat_grafik['forum_neuesthema'];
		$button = $t['thema_button'];
	}
	
	if (!isset($th_name)) {
		$th_name = "";
	}
	if (!isset($th_desc)) {
		$th_desc = "";
	}
	?>
	<form action="forum.php" method="post">
		<table class="tabelle_gerust">
			<tr>
				<td class="tabelle_kopfzeile" colspan="2"><?php echo $t['thema_neu']; ?></td>
			</tr>
			</tr>
			<tr>
				<td style="width:300px; font-weight:bold;" class="tabelle_koerper_login"><?php echo $f1; ?><?php echo $t['thema_msg1']; ?><?php echo $f2; ?></td>
				<td class="tabelle_koerper_login"><input type="text" size="40" name="th_name" value="<?php echo $th_name; ?>"></td>
			</tr>
			<tr>
				<td style="font-weight:bold;" class="tabelle_koerper_login"><?php echo $f1; ?><?php echo $t['thema_msg2']; ?><?php echo $f2; ?></td>
				<td class="tabelle_koerper_login"><textarea name="th_desc" rows="3" cols="90"><?php echo $th_desc; ?></textarea></td></tr>
			<?php
			if ($th_id > 0) {
				?>
				<tr>
					<td style="font-weigh:bold;" class="tabelle_koerper_login"><?php echo $f1; ?><?php echo $t['thema_msg3']; ?><?php echo $f2; ?></td>
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
				@mysqli_free_result($query);
			}
			?>
			<tr>
				<td colspan="2" align="right" class="tabelle_koerper_login"><input type="submit" value="<?php echo $button; ?>"></td>
			</tr>
		</table>
		<input type="hidden" name="id" value="<?php echo $id; ?>">
		<input type="hidden" name="http_host" value="<?php echo $http_host; ?>">
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
	global $mysqli_link, $f3, $f4, $id, $http_host, $thread, $anzahl_po_seite;
	global $seite, $t, $farbe_vlink, $farbe_hervorhebung_forum;
	
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
	@mysqli_free_result($query);
	?>
	<table style="width:900px; margin:auto;">
		<tr>
			<td><?php echo $f3; ?><a href="forum.php?id=<?php echo $id; ?>&http_host=<?php echo $http_host; ?>#<?php echo $fo_id; ?>"><?php echo $fo_name; ?></a> > <a href="forum.php?id=<?php echo $id; ?>&http_host=<?php echo $http_host; ?>&th_id=<?php echo $th_id; ?>&show_tree=<?php echo $thread; ?>&aktion=show_thema&seite=<?php echo $seite; ?>"><?php echo $th_name; ?></a><?php echo $f4; ?></td>
		<?php
		if (!$anzahl_po_seite || $anzahl_po_seite == 0)
			$anzahl_po_seite = 20;
		$anz_seiten = ceil(($th_anzthreads / $anzahl_po_seite));
		if ($anz_seiten > 1) {
			echo "<td style=\"text-align:right;\">$f3 $t[page] ";
			for ($page = 1; $page <= $anz_seiten; $page++) {
				
				if ($page == $seite) {
					$col = $farbe_hervorhebung_forum;
				} else {
					$col = $farbe_vlink;
				}
				echo "<a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&aktion=show_thema&seite=$page\"><span style=\"color:$col;\">$page</span></a> ";
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
	global $id, $http_host, $o_js, $forum_admin, $th_id, $show_tree, $seite, $farbe_link;
	global $t, $f1, $f2, $f3, $f4, $farbe_tabelle_kopf2;
	global $anzahl_po_seite, $chat_grafik;
	global $admin, $anzahl_po_seite2, $u_id, $u_level;
	
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
			<td colspan="6" class="tabelle_kopfzeile"><?php echo $th_name; ?></td>
			<td style="text-align:center;" class="tabelle_kopfzeile">
				<?php
				$schreibrechte = pruefe_schreibrechte($th_id);
				if ($schreibrechte) {
					?>
					<a href="forum.php?id=<?php echo $id; ?>&http_host=<?php echo $http_host; ?>&th_id=<?php echo $th_id; ?>&po_vater_id=0&aktion=thread_neu"><?php echo $t['neuer_thread']; ?></a><br>
					<?php
				} else {
					echo $f3 . $t[nur_leserechte] . $f4; ?><br>
					<?php
				}
				?>
			</td>
		</tr>
		<tr>
			<td colspan="3" class="tabelle_kopfzeile">
				<?php echo $f3; ?><a href="forum.php?id=<?php echo $id; ?>&http_host=<?php echo $http_host; ?>&th_id=<?php echo $th_id; ?>&aktion=thema_alles_gelesen"><?php echo $t['alles_gelesen']; ?></a><?php echo $f4; ?>
			</td>
			<td style="text-align:center;" class="tabelle_kopfzeile"><?php echo $f3 . $t['autor'] . $f4; ?></td>
			<td style="text-align:center;" class="tabelle_kopfzeile"><?php echo $f3 . $t['datum']; ?><br><?php echo $t['letztes_posting'] . $f4; ?></td>
			<td style="text-align:center;" class="tabelle_kopfzeile"><?php echo $f3 . $t['anzreplys'] . $f4; ?></td>
			<td style="text-align:center;" class="tabelle_kopfzeile"><?php echo $f3 . $t['anzneue'] . $f4; ?></td>
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
				$icon = "<img src=\"pics/forum/o.gif\" width=\"20\" height=\"25\" border=\"0\">";
				$arr_postings = array($posting['po_id']);
			} else {
				$arr_postings = explode(",", $posting['po_threadorder']);
				$anzreplys = count($arr_postings);
				//Ersten Beitrag mit beruecksichtigen
				$arr_postings[] = $posting['po_id'];
				if ($show_tree == $posting['po_id'])
					$icon = "<a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&aktion=show_thema&seite=$seite\"><img src=\"pics/forum/m.gif\" width=\"20\" height=\"25\" border=\"0\"></a>";
				else $icon = "<a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&show_tree=$posting[po_id]&aktion=show_thema&seite=$seite\"><img src=\"pics/forum/p.gif\" width=\"20\" height=\"25\" border=\"0\"></a>";
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
			} elseif ($ungelesene < 11)
				$folder = $chat_grafik['forum_ordnerblau'];
			else $folder = $chat_grafik['forum_ordnervoll'];
			
			if ($ungelesene != 0) {
				$coli = "<span style=\"#ff0000\">";
				$colo = "</span>";
			} else {
				$coli = "";
				$colo = "";
			}
			
			echo "<tr><td style=\"text-align:center;\" $farbe>$folder</nobr></td>\n";
			echo "<td style=\"text-align:center;\" $farbe>$icon</td>\n";
			
			if ($posting['po_gesperrt'] == 'Y' and !$forum_admin) {
				echo "<td $farbe style=\"font-weight:bold; font-size: smaller;\">&nbsp;<span style=\"color:$farbe_link; \">"
					. substr($posting['po_titel'], 0, 40)
					. "</span> <span style=\"color:#ff0000; \">(gesperrt)</span></td>\n";
			} elseif ($posting['po_gesperrt'] == 'Y' and $forum_admin) {
				echo "<td $farbe style=\"font-weight:bold; font-size: smaller;\">&nbsp;$f1<a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_id=$posting[po_id]&thread=$posting[po_id]&aktion=show_posting&seite=$seite\">"
					. substr($posting['po_titel'], 0, 40)
					. "</a>$f2 <span style=\"color:#ff0000; \">(gesperrt)</span></td>\n";
			} else {
				echo "<td $farbe>&nbsp;$f1<b><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_id=$posting[po_id]&thread=$posting[po_id]&aktion=show_posting&seite=$seite\">"
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
			
			if ($posting['po_date2'] == '01.01.70'
				|| $posting['po_date'] == $posting['po_date2']) {
				$date2 = "";
			} else {
				$date2 = "$f3; " . substr($posting['po_date2'], 0, 5) . "$f4";
			}
			echo "<td style=\"text-align:center;\" $farbe>$f3$posting[po_date]$f4$date2</td>\n";
			echo "<td style=\"text-align:center;\" $farbe>$f3$anzreplys$f4</td>\n";
			echo "<td style=\"text-align:center;\" $farbe>$f3$coli$ungelesene$colo$f4</td></tr>\n";
			
			if (($show_tree == $posting['po_id'])
				&& ($posting['po_threadorder'] != "0")) {
				echo "<tr><td $farbe>&nbsp;</td><td colspan=\"6\" $farbe>\n";
				zeige_baum($arr_postings, $posting['po_threadorder'],
					$posting['po_id']);
				echo "</td></tr>\n";
			}
			
			$zeile++;
			
		}
		?>
	
	</table>
	<?php
	show_pfad($th_id);
	show_icon_description("thema");
	?>
	<br>
	<table style="width:900px; margin:auto;">
		<tr>
			<td>
				<form action="forum.php">
				<?php echo $t['forum_postingsproseite']; ?> <input name="anzahl_po_seite2" size="3" maxlength="4" value="<?php echo $anzahl_po_seite; ?>">
				<input type="hidden" name="http_host" value="<?php echo $http_host; ?>">
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
	global $id, $u_id, $http_host, $th_id, $po_id, $po_vater_id, $po_tiefe, $mysqli_link, $po_titel, $po_text, $thread, $seite;
	global $f1, $f2, $f3, $f4, $farbe_tabelle_kopf2;
	global $t, $mysqli_link;
	global $forum_admin, $u_nick, $smilies_datei;
	
	$smilies_datei = "forum-" . $smilies_datei;
	
	switch ($mode) {
		
		case "neuer_thread":
			$kopfzeile = $t['neuer_thread'];
			$button = $t['neuer_thread_button'];
			$titel = $t['neuer_thread'];
			
			if (!$po_text)
				$po_text = erzeuge_fuss("");
			
			break;
		case "reply":
		//Daten des Vaters holen
			$sql = "select date_format(from_unixtime(po_ts), '%d.%m.%Y, %H:%i:%s') as po_date, po_tiefe,
								po_titel, po_text, ifnull(u_nick, 'unknown') as u_nick
								from posting
								left join user on po_u_id = u_id
								where po_id = " . intval($po_vater_id);
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
			
			$kopfzeile = $po_titel;
			$button = $t['neuer_thread_button'];
			break;
		case "answer":
		//Daten des Vaters holen
			$sql = "select po_tiefe, po_titel
								from posting
								where po_id = " . intval($po_vater_id);
			$query = mysqli_query($mysqli_link, $sql);
			
			$po_titel = mysqli_result($query, 0, "po_titel");
			if (substr($po_titel, 0, 3) != $t['reply'])
				$po_titel = $t['reply'] . " " . $po_titel;
			$titel = $po_titel;
			$po_text = erzeuge_fuss("");
			
			$po_tiefe = mysqli_result($query, 0, "po_tiefe");
			
			$kopfzeile = $po_titel;
			$button = $t['neuer_thread_button'];
			break;
		
		case "edit":
		//Daten holen
			$sql = "select date_format(from_unixtime(po_ts), '%d.%m.%Y, %H:%i:%s') as po_date, po_tiefe,
								po_titel, po_text, ifnull(u_nick, 'unknown') as u_nick, u_id, po_threadgesperrt, po_topposting
								from posting
								left join user on po_u_id = u_id
								where po_id = " . intval($po_id);
			$query = mysqli_query($mysqli_link, $sql);
			
			$autor = mysqli_result($query, 0, "u_nick");
			$user_id = mysqli_result($query, 0, "u_id");
			$po_date = mysqli_result($query, 0, "po_date");
			$po_topposting = mysqli_result($query, 0, "po_topposting");
			$po_threadgesperrt = mysqli_result($query, 0, "po_threadgesperrt");
			$po_titel = mysqli_result($query, 0, "po_titel");
			$titel = $po_titel;
			$po_text = mysqli_result($query, 0, "po_text");
			$po_tiefe = mysqli_result($query, 0, "po_tiefe");
			
			//Testen ob User mogelt, indem er den Edit-Link mit anderer po_id benutzt
			if ((!$forum_admin) && ($user_id != $u_id)) {
				echo "wanna cheat eh? bad boy!";
				exit;
			}
			
			$kopfzeile = $po_titel;
			$button = $t['edit_button'];
			
			break;
		
	}
	echo "<form name=\"form\" action=\"forum.php\" method=\"post\">";
	show_pfad_posting($th_id, $titel);
	?>
	<table class="tabelle_gerust">
		<tr>
			<td class="tabelle_kopfzeile" colspan="2"><?php echo $kopfzeile; ?></td>
		</tr>
		<tr>
			<td style="width:200px; font-weight:bold;" class="tabelle_koerper_login"><?php echo $f1; ?><?php echo $t['posting_msg1']; ?><?php echo $f2; ?></td>
			<td style="width:560px;" class="tabelle_koerper_login"><input type="text" size="50" name="po_titel" value="<?php echo $po_titel; ?>"></td>
		</tr>
		<tr>
			<td colspan="2" style="font-weight:bold;" class="tabelle_koerper_login"><?php echo $f1; ?><?php echo $t['posting_msg2']; ?><?php echo $f2; ?><br>
				<?php
				echo $f3 . "($t[desc_posting])$f4\n";
				
				$link_smilies = "$smilies_datei?http_host=$http_host&id=$id";
				
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
	if ($forum_admin && ($mode == "edit")) {
		
		echo "<tr><td style=\"font-weight:bold;\" class=\"tabelle_koerper_login\">$f1 $t[posting_msg3] $f2<br>$f3 $t[posting_msg4] $f4</td>\n";
		echo "<td class=\"tabelle_koerper_login\"><input type=\"text\" size=\"20\" name=\"autor\" value=\"$autor\"></td></tr>\n";
		
		// Nur im Obersten Vater die TOP und gesperrt einstellungen ändern lassen
		if ($po_id == $thread) {
			echo "<tr><td style=\"font-weight:bold;\" class=\"tabelle_koerper_login\">$f1 Thema gesperrt$f2</td>\n";
			echo "<td class=\"tabelle_koerper_login\"><SELECT NAME=\"po_threadgesperrt\"><OPTION ";
			if ($po_threadgesperrt == 'Y')
				echo "SELECTED ";
			echo "VALUE=\"Y\">Ja</OPTION><OPTION ";
			if ($po_threadgesperrt == 'N')
				echo "SELECTED ";
			echo "VALUE=\"N\">Nein</SELECT></td></tr>\n";
			
			echo "<tr><td style=\"font-weight:bold;\" class=\"tabelle_koerper_login\">$f1 TOP Beiträge$f2</td>\n";
			echo "<td class=\"tabelle_koerper_login\"><SELECT NAME=\"po_topposting\"><OPTION ";
			if ($po_topposting == 'Y')
				echo "SELECTED ";
			echo "VALUE=\"Y\">Ja</OPTION><OPTION ";
			if ($po_topposting == 'N')
				echo "SELECTED ";
			echo "VALUE=\"N\">Nein</SELECT></td></tr>\n";
		}
		
	}
	?>
	<tr>
		<td colspan="2" style="text-align:right;" class="tabelle_koerper_login"><input type="submit" value="<?php echo $button; ?>"></td>
	</tr>
	</table>
	<?php
	show_pfad_posting($th_id, $titel);
	echo "<input type=\"hidden\" name=\"id\" value=\"$id\">";
	echo "<input type=\"hidden\" name=\"http_host\" value=\"$http_host\">";
	echo "<input type=\"hidden\" name=\"th_id\" value=\"$th_id\">\n";
	
	if ($mode == "neuer_thread") {
		echo "<input type=\"hidden\" name=\"po_tiefe\" value=\"0\">\n";
		echo "<input type=\"hidden\" name=\"po_vater_id\" value=\"0\">\n";
	} else if (($mode == "reply") || ($mode == "answer")) {
		$tiefe = $po_tiefe + 1;
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
	global $t, $punkte_pro_posting, $farbe_tabelle_kopf2;
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
	
	global $mysqli_link, $f3, $f4, $id, $http_host, $thread, $seite;
	//Infos über Forum und Thema holen
	$sql = "select fo_id, fo_name, th_name
				from forum, thema
				where th_id = " . intval($th_id) . "
				and fo_id = th_fo_id";
	$query = mysqli_query($mysqli_link, $sql);
	$fo_id = htmlspecialchars(mysqli_result($query, 0, "fo_id"));
	$fo_name = htmlspecialchars(mysqli_result($query, 0, "fo_name"));
	$th_name = htmlspecialchars(mysqli_result($query, 0, "th_name"));
	@mysqli_free_result($query);
	?>
	<table style="width:900px; margin:auto;">
		<tr>
			<td><?php echo $f3; ?><a href="forum.php?id=<?php echo $id; ?>&http_host=<?php echo $http_host; ?>#<?php echo $fo_id; ?>"><?php echo $fo_name; ?></a> > <a href="forum.php?id=<?php echo $id; ?>&http_host=<?php echo $http_host; ?>&th_id=<?php echo $th_id; ?>&show_tree=<?php echo $thread; ?>&aktion=show_thema&seite=<?php echo $seite; ?>"><?php echo $th_name; ?></a> > <?php echo $po_titel . $f4; ?></td>
			</tr>
	</table>
	<?php
	
}

//gibt Navigation für Beiträge aus
function navigation_posting(
	$last,
	$next,
	$po_u_id,
	$th_id,
	$user_nick = "",
	$thread_gelesen_zeigen = FALSE) {
	global $f1, $f2, $f3, $f4, $farbe_tabelle_kopf2, $t, $seite;
	global $id, $http_host, $po_id, $u_id, $thread, $forum_admin, $chat_grafik;
	global $u_level;
	?>
	<table class="tabelle_gerust">
		<tr>
		<?php
		if ($last) {
			echo "<td style=\"width:50px; vertical-align:middle;\" class=\"tabelle_kopfzeile\"><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_id=$last&thread=$thread&aktion=show_posting&seite=$seite\">"
				. $chat_grafik['forum_pfeil_links'] . "</a></td>\n";
		} else {
			echo "<td style=\"width:50px;\" class=\"tabelle_kopfzeile\">&nbsp;</td>\n";
		}
	
		if ($next) {
			echo "<td style=\"width:50px; vertical-align:middle;\" class=\"tabelle_kopfzeile\"><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_id=$next&thread=$thread&aktion=show_posting&seite=$seite\">"
			. $chat_grafik['forum_pfeil_rechts'] . "</a></td>\n";
		} else {
			echo "<td style=\"width:50px;\" class=\"tabelle_kopfzeile\">&nbsp;</td>\n";
		}
	
		if ($thread_gelesen_zeigen) {
			echo "<td style=\"width:170px; text-align:center;\" class=\"tabelle_kopfzeile\">$f3<a href=\"forum.php?id=$id&http_host=$http_host" . "&th_id=$th_id&thread=$thread&aktion=thread_alles_gelesen&seite=$seite\">$t[thread_alles_gelesen]</a>$f4</td>";
		} else {
			echo "<td style=\"width:170px;\" class=\"tabelle_kopfzeile\">&nbsp;</td>\n";
		}
		
		echo "<td style=\"width:210px;\" class=\"tabelle_kopfzeile\">&nbsp;</td>\n";
	
		$threadgesperrt = ist_thread_gesperrt($thread);
		$schreibrechte = pruefe_schreibrechte($th_id);
		//darf user posting bearbeiten
		//entweder eigenes posting oder forum_admin
		if ((($u_id == $po_u_id && !$threadgesperrt) || ($forum_admin)) && ($schreibrechte)) {
			echo "<td style=\"width:50px;\" class=\"tabelle_kopfzeile\"><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_id=$po_id&thread=$thread&aktion=edit&seite=$seite\">"
			. $chat_grafik['forum_editieren'] . "</a></td>";
		} else {
			echo "<td style=\"width:50px;\" class=\"tabelle_kopfzeile\">&nbsp;</td>\n";
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
			
			$mailurl = "mail.php?aktion=antworten_forum&id=$id&http_host=$http_host&th_id=$th_id&po_vater_id=$po_id&thread=$thread";
			echo "<td style=\"width:50px;\" class=\"tabelle_kopfzeile\"><a href=\"$mailurl\" target=\"640_$pfenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster2('$mailurl'); return(false)\">" . $chat_grafik['forum_privat'] . "</a></td>";
		} else {
			echo "<td style=\"width:50px;\" class=\"tabelle_kopfzeile\">&nbsp;</td>\n";
		}
	
		if ($schreibrechte && !$threadgesperrt) {
			echo "<td style=\"width:50px;\" class=\"tabelle_kopfzeile\"><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_vater_id=$po_id&thread=$thread&aktion=answer&seite=$seite\">" . $chat_grafik['forum_antworten'] . "</a></td>";
			echo "<td style=\"width:50px;\" class=\"tabelle_kopfzeile\"><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_vater_id=$po_id&thread=$thread&aktion=reply&seite=$seite\">" . $chat_grafik['forum_zitieren'] . "</a></td>";
		} else {
			echo "<td style=\"width:50px;\" class=\"tabelle_kopfzeile\">&nbsp;</td>\n";
			echo "<td style=\"width:50px;\" class=\"tabelle_kopfzeile\">&nbsp;</td>\n";
		}
	
		//nur forum-admins duerfen postings loeschen
		if ($forum_admin) {
			echo "</tr>";
			
			echo "<tr>";
			echo "<td class=\"tabelle_kopfzeile\">&nbsp;</td>\n";
			echo "<td class=\"tabelle_kopfzeile\">&nbsp;</td>\n";
			echo "<td class=\"tabelle_kopfzeile\">&nbsp;</td>\n";
			echo "<td class=\"tabelle_kopfzeile\">&nbsp;</td>\n";
			
			echo "<td class=\"tabelle_kopfzeile\"><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_id=$po_id&thread=$thread&aktion=sperre_posting&seite=$seite\">" . $chat_grafik['forum_sperren'] . "</a></td>";
			echo "<td class=\"tabelle_kopfzeile\"><a onClick=\"return ask('$t[conf_delete]')\" href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_id=$po_id&thread=$thread&aktion=delete_posting&seite=$seite\">" . $chat_grafik['forum_loeschen'] . "</a></td>";
			if ($po_id == $thread) {
				echo "<td class=\"tabelle_kopfzeile\"><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&thread=$thread&aktion=verschiebe_posting&seite=$seite\">"
					. $chat_grafik['forum_verschieben'] . "</a></td>";
			} else {
				echo "<td class=\"tabelle_kopfzeile\">&nbsp;</td>\n";
			}
			echo "<td class=\"tabelle_kopfzeile\"></td>";
		}
		?>
	</tr>
	</table>
	<?php
	
}

// Verschiebe Beitrag
function verschiebe_posting() {
	global $id, $http_host, $mysqli_link, $po_id, $thread, $seite;
	global $f1, $f2, $f3, $f4;
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
			@mysqli_free_result($query);
			?>
			<tr>
				<td style="text-align:center;" colspan="2" class="tabelle_koerper_login"><INPUT TYPE="SUBMIT" NAME="los" VALUE="<?php echo $t['verschieben4']; ?>"></td>
			</tr>
		</table>
		
		<input type="hidden" name="id" value="<?php echo $id; ?>">
		<input type="hidden" name="thread_verschiebe" value="<?php echo $thread; ?>">
		<input type="hidden" name="seite" value="<?php echo $seite; ?>">
		<input type="hidden" name="verschiebe_von" value="<?php echo $th_id; ?>">
		<input type="hidden" name="http_host" value="<?php $http_host; ?>">
		<input type="hidden" name="fo_id" value="<?php $fo_id; ?>">
		<input type="hidden" name="aktion" value="verschiebe_posting_ausfuehren">
	</form>
	<?php
}

//zeigt Beitrag an
function show_posting() {
	global $id, $http_host, $mysqli_link, $po_id, $thread, $seite;
	global $f1, $f2, $f3, $f4;
	global $t, $o_js, $forum_admin;
	
	$sql = "select po_th_id, date_format(from_unixtime(po_ts), '%d.%m.%Y, %H:%i:%s') as po_date, po_tiefe,
				po_titel, po_text, po_u_id, po_gesperrt, ifnull(u_nick, 'Nobody') as u_nick,
		u_email, u_id, u_level,u_punkte_gesamt,u_punkte_gruppe,u_chathomepage
				from posting
				left join user on po_u_id = u_id
				where po_id = " . intval($po_id);
	
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
	
	@mysqli_free_result($query);
	
	$sql = "select po_threadorder from posting where po_id=" . intval($thread);
	$query = mysqli_query($mysqli_link, $sql);
	$po_threadorder = mysqli_result($query, 0, "po_threadorder");
	
	@mysqli_free_result($query);
	
	//vorheriges und naechstes posting bestimmen
	if ($po_threadorder == "0") { //keine Antwort
		$last = 0;
		$next = 0;
	} else if ($po_id == $thread) { //root des Themas wird angezeigt
		$last = 0;
		$postingorder = explode(",", $po_threadorder);
		$next = $postingorder[0];
	} else { //muddu guggen...
		$postingorder = explode(",", $po_threadorder);
		$k = 0;
		while (list($k, $v) = each($postingorder)) {
			
			if ($v == $po_id)
				break;
		}
		if ($k > 0)
			$last = $postingorder[$k - 1];
		else $last = $thread;
		if (($k + 1) < count($postingorder))
			$next = $postingorder[$k + 1];
		else $next = 0;
	}
	
	if (!$forum_admin) {
		if (ist_posting_gesperrt($next))
			$next = 0;
		if (ist_posting_gesperrt($last))
			$last = 0;
	}
	
	show_pfad_posting($th_id, $po_titel);
	navigation_posting($last, $next, $po_u_id, $th_id, $row->u_nick, TRUE);
	?>
	<table class="tabelle_gerust">
		<tr>
			<td class="tabelle_zeile1"><b><?php echo $po_titel; ?></b>
			<?php
			if ($po_gesperrt == 'Y') {
				echo " <span style=\"color:#ff0000; font-weight:bold;\">(Beitrag gesperrt)</span>";
			}
			?>
			</td>
		</tr>
		<tr>
			<td class="tabelle_zeile1"><?php echo $f1 . $t['datum'] . $po_date . " " . $t['autor'] . $autor . $f2; ?></td>
		</tr>
		<tr>
			<td class="tabelle_zeile1"><?php echo $f1 . $po_text . $f2; ?></td>
		</tr>
		<?php
		if ($po_threadorder == "0") {
			// nothing
		} else {
			?>
			<tr>
				<td>
				<?php
				reset($postingorder);
				zeige_baum($postingorder, $po_threadorder, $thread, $po_id, TRUE);
				?>
				</td>
			</tr>
			<?php
		}
		?>
	</table>
	<?php
	navigation_posting($last, $next, $po_u_id, $th_id, $row->u_nick, TRUE);
	show_pfad_posting($th_id, $po_titel);
}

//Zeigt Themabaum 
function zeige_baum(
	&$postings_array,
	&$postings,
	$thread,
	$highlight = 0,
	$zeige_top = FALSE) {
	global $mysqli_link, $f1, $f2, $f3, $f4, $seite, $th_id;
	global $farbe_hervorhebung_forum, $farbe_neuesposting_forum, $farbe_link;
	global $id, $u_id, $http_host, $o_js, $forum_admin;
	
	if (!$postings_array)
		return;
	
	if ($zeige_top) {
		$postings = mysqli_real_escape_string($mysqli_link, "$thread,$postings");
	}
	
	//vom user gelesene postings holen
	$sql = "SELECT `u_gelesene_postings` FROM `user` WHERE `u_id`=$u_id";
	$query = mysqli_query($mysqli_link, $sql);
	if (mysqli_num_rows($query) > 0)
		$gelesene = mysqli_result($query, 0, "u_gelesene_postings");
	$u_gelesene = unserialize($gelesene);
	
	$sql = "select po_id, date_format(from_unixtime(po_ts), '%d.%m %H:%i') as po_date, po_tiefe,
				po_titel, po_u_id, u_nick, u_level, u_punkte_gesamt, u_punkte_gruppe, u_chathomepage, po_threadorder, po_gesperrt
				from posting
				left join user on po_u_id = u_id
				where po_id in ($postings)";
	
	$query = mysqli_query($mysqli_link, $sql);
	
	//array mit po_id als index anlegen, um wahlfreien zugriff auf postings zu ermoeglichen
	$po_wahlfrei = array();
	
	while ($post = mysqli_fetch_object($query)) {
		$po_wahlfrei[$post->po_id] = $post;
	}
	@mysqli_free_result($query);
	
	echo "<table width=\"730\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
	
	if ($zeige_top) {
		
		if ($thread == $highlight)
			$col = $farbe_hervorhebung_forum;
		else $col = $farbe_link;
		
		if ($po_wahlfrei[$thread]->po_gesperrt == 'Y' and !$forum_admin) {
			echo "<td colspan=\"16\" style=\"font-weight:bold; font-size: smaller;\">&nbsp;<span style=\"color:$col; \">"
				. substr($po_wahlfrei[$thread]->po_titel, 0, 60)
				. "</span> <span style=\"color:#ff0000; \">(gesperrt)</span></td>\n";
		} else if ($po_wahlfrei[$thread]->po_gesperrt == 'Y' and $forum_admin) {
			echo "<td colspan=\"16\" style=\"font-weight:bold; font-size: smaller;\">&nbsp;<a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_id="
				. $po_wahlfrei[$thread]->po_id
				. "&thread=$thread&aktion=show_posting&seite=$seite\"><span style=\"color:$col; \">"
				. substr($po_wahlfrei[$thread]->po_titel, 0, 60)
				. "$f2</span></a> <span style=\"color:#ff0000; \">(gesperrt)</span></td>\n";
		} else {
			echo "<td colspan=\"16\" style=\"font-weight:bold; font-size: smaller;\">&nbsp;<a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_id="
				. $po_wahlfrei[$thread]->po_id
				. "&thread=$thread&aktion=show_posting&seite=$seite\"><span style=\"color:$col; \">"
				. substr($po_wahlfrei[$thread]->po_titel, 0, 60)
				. "$f2</span></a></td>\n";
		}
		
		if (isset($po_wahlfrei[$thread]->u_email)
			&& $po_wahlfrei[$thread]->u_email)
			echo "<td style=\"text-align:center;\"><b>$f3<a href=\"mailto:"
				. $po_wahlfrei[$thread]->u_email . "\">"
				. $po_wahlfrei[$thread]->autor . "$f4</b></a></td>\n";
		else if (isset($po_wahlfrei[$thread]->autor)
			&& $po_wahlfrei[$thread]->autor)
			echo "<td style=\"text-align:center;\"><b>$f3" . $po_wahlfrei[$thread]->autor
				. "$f4</b></td>\n";
		else echo "<td style=\"text-align:center;\"><b>$f3" . "$f4</b></td>\n";
		echo "<td style=\"text-align:center;\"><b>$f3" . $po_wahlfrei[$thread]->po_date
			. "$f4</b></td>\n";
		echo "<td>&nbsp;</td></tr>\n";
		
	}
	
	echo "<tr><td width=\"20\"><img src=\"pics/forum/linie.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
	echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
	echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
	echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
	echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
	echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
	echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
	echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
	echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
	echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
	echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
	echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
	echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
	echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
	echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
	echo "<td width=\"160\"><img src=\"pics/fuell.gif\" width=\"160\" height=\"1\" border=\"0\"></td>\n";
	echo "<td width=\"150\"><img src=\"pics/fuell.gif\" width=\"150\" height=\"1\" border=\"0\"></td>\n";
	echo "<td width=\"110\"><img src=\"pics/fuell.gif\" width=\"110\" height=\"1\" border=\"0\"></td>\n";
	echo "<td width=\"5\"><img src=\"pics/fuell.gif\" width=\"5\" height=\"1\" border=\"0\"></td>\n";
	echo "<td width=\"5\"><img src=\"pics/fuell.gif\" width=\"5\" height=\"1\" border=\"0\"></td></tr>\n";
	
	//in der richtigen reihenfolge postings durchlaufen
	
	$letztes = array();
	while (list($k, $v) = @each($postings_array)) {
		
		$span = 0;
		$tiefe = $po_wahlfrei[$v]->po_tiefe;
		if ($tiefe >= 15)
			$tiefe = 15;
		
		$letztes[$tiefe] = $po_wahlfrei[$v]->po_threadorder;
		
		echo "<tr>";
		for ($i = 1; $i <= $tiefe; $i++) {
			
			if (($i == $tiefe) && ($letztes[$i]))
				echo "<td><img src=\"pics/forum/ecke2.gif\" width=\"20\" height=\"20\" border=\"0\"></td>\n";
			else if (($i == $tiefe) && (!$letztes[$i]))
				echo "<td><img src=\"pics/forum/ecke1.gif\" width=\"20\" height=\"20\" border=\"0\"></td>\n";
			else if (!($i == $tiefe) && ($letztes[$i]))
				echo "<td><img src=\"pics/fuell.gif\" width=\"20\" height=\"20\" border=\"0\"></td>\n";
			else echo "<td><img src=\"pics/forum/linie.gif\" width=\"20\" height=\"20\" border=\"0\"></td>\n";
			
			$span++;
			
		}
		if ($v == $highlight)
			$col = $farbe_hervorhebung_forum;
		else if (!@in_array($v, $u_gelesene[$th_id]))
			$col = $farbe_neuesposting_forum;
		else $col = $farbe_link;
		
		if ($po_wahlfrei[$v]->po_gesperrt == 'Y' and !$forum_admin) {
			echo "<td colspan=\"" . (16 - $span) . " style=\"font-weight:bold; font-size: smaller;\" \">&nbsp;<span style=\"color:$col;\">"
				. substr($po_wahlfrei[$v]->po_titel, 0,
					(60 - round($span * 2.5)))
				. "</span> <span style=\"color:#ff0000;\">(gesperrt)</span></td>\n";
		} else if ($po_wahlfrei[$v]->po_gesperrt == 'Y' and $forum_admin) {
			echo "<td colspan=\"" . (16 - $span) . " style=\"font-weight:bold; font-size: smaller;\" \">&nbsp;<a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_id="
				. $po_wahlfrei[$v]->po_id
				. "&thread=$thread&aktion=show_posting&seite=$seite\"><span style=\"color:$col;\">"
				. substr($po_wahlfrei[$v]->po_titel, 0,
					(60 - round($span * 2.5)))
				. " $f2</span></a> <span style=\"color:#ff0000;\">(gesperrt)</span></td>\n";
		} else {
			echo "<td colspan=\"" . (16 - $span) . " style=\"font-weight:bold; font-size: smaller;\" \">&nbsp;<a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_id="
				. $po_wahlfrei[$v]->po_id
				. "&thread=$thread&aktion=show_posting&seite=$seite\"><span style=\"color:$col;\">"
				. substr($po_wahlfrei[$v]->po_titel, 0,
					(60 - round($span * 2.5))) . " $f2</span></a></td>\n";
		}
		
		if (!($po_wahlfrei[$v]->u_nick)) {
			echo "<td>$f3<b>unknown</b>$f4</td>\n";
		} else {
			
			$userdata = array();
			$userdata['u_id'] = $po_wahlfrei[$v]->po_u_id;
			$userdata['u_nick'] = $po_wahlfrei[$v]->u_nick;
			$userdata['u_level'] = $po_wahlfrei[$v]->u_level;
			$userdata['u_punkte_gesamt'] = $po_wahlfrei[$v]->u_punkte_gesamt;
			$userdata['u_punkte_gruppe'] = $po_wahlfrei[$v]->u_punkte_gruppe;
			$userdata['u_chathomepage'] = $po_wahlfrei[$v]->u_chathomepage;
			$userlink = user($po_wahlfrei[$v]->po_u_id, $userdata, $o_js,
				FALSE, "&nbsp;", "", "", TRUE, FALSE, 29);
			if ($po_wahlfrei[$v]->u_level == 'Z') {
				echo "<td>$f1 $userdata[u_nick] $f2</td>\n";
			} else {
				echo "<td>$f1 $userlink $f2</td>\n";
			}
		}
		
		echo "<td style=\"text-align:center;\"><b>$f3" . $po_wahlfrei[$v]->po_date
			. "$f4</b></td>\n";
		echo "<td colspan=\"2\">&nbsp;</td></tr>\n";
	}
	echo "</table>";
}
?>