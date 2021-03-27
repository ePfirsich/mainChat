/* mainChat Javascript Function Library, fidion GmbH */

wins = new Array;
user = new Array;

function sendtext(text) {
	parent.frames['schreibe'].location = 'schreibe.php' + stdparm2 + '&text='
			+ text;
	parent.frames['eingabe'].document.forms['form'].elements['text2'].focus();
}

function sendtext_opener(text) {
	opener.parent.frames['schreibe'].location = 'schreibe.php' + stdparm2
			+ '&text=' + text;
	opener.parent.frames['eingabe'].document.forms['form'].elements['text2']
			.focus();
}

function appendtext(text) {
	parent.frames['eingabe'].document.forms['form'].elements['text2'].value += text;
	parent.frames['eingabe'].document.forms['form'].elements['text2'].focus();
}

function appendtext_opener(text) {
	opener.parent.frames['eingabe'].document.forms['form'].elements['text2'].value += text;
	opener.parent.frames['eingabe'].document.forms['form'].elements['text2']
			.focus();
}

function einladung(nick) {
	nickneu = escape(nick);
	nickneu = nickneu.replace(/\+/, "%2B");
	sendtext_opener('/einlad%20' + nickneu);
}

function kickuser(nick) {
	nickneu = escape(nick);
	nickneu = nickneu.replace(/\+/, "%2B");
	sendtext('/kick%20' + nickneu);
}

function gaguser(nick) {
	nickneu = escape(nick);
	nickneu = nickneu.replace(/\+/, "%2B");
	sendtext('/gag%20' + nickneu);
}

function ignoriereuser(nick) {
	nickneu = escape(nick);
	nickneu = nickneu.replace(/\+/, "%2B");
	sendtext('/ignoriere%20' + nickneu);
}

function refresh() {
	this.location.href = this.location.href;
}

function genlist(liste, aktion) {
	document
			.write("<TABLE WIDTH=\"100%\" BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\""
					+ padd + "\">\n");

	var interval = 9;
	var color_index = 1;
	if (show_geschlecht == true)
		interval = 10;

	for ( var i = 0; i < liste.length; i += interval) {

		if (liste[i + 9] == "M")
			var tgegrafik = gegrafik[0];
		else if (liste[i + 9] == "W")
			var tgegrafik = gegrafik[1];
		else
			var tgegrafik = "";

		if ((liste[i])
				&& (inaktiv_userfunktionen != "1" || aktion != "chatuserliste")) {
			var dlink = "<A HREF=\"#\" onMouseOver=\"return(true)\" onClick=\"userdetails("
					+ liste[i]
					+ ")\">"
					+ (liste[i + 5] ? "(" + liste[i + 2] + ")" : liste[i + 2])
					+ "</A>" + tgegrafik;
		} else {
			var dlink = (liste[i + 5] ? "(" + liste[i + 2] + ")" : liste[i + 2])
					+ tgegrafik;
		}

		var leveltxt = leveltext[liste[i + 6]];

		var type = (leveltxt ? "(" + leveltxt + ")" : "");
		var nlink = (liste[i + 5] ? fett[4] + dlink + fett[5] + "&nbsp;"
				+ fett[2] + type + fett[3] : fett[0] + dlink + fett[1]
				+ "&nbsp;" + fett[2] + type + fett[3]);

		if ((liste[i + 7] != 0) && communityfeatures == 1) {
			var url = "hilfe.php" + stdparm2 + "&aktion=legende";
			if ((liste[i + 6] == "C") || (liste[i + 6] == "S")) {
				nlink += "&nbsp;<A HREF=\"#\" onMouseOver=\"return(true)\" onClick=\"window.open('"
						+ url
						+ "','640_"
						+ u_nick
						+ "','resizable=yes,scrollbars=yes,width=780,height=580'); return(false)\">"
						+ ggrafik[0] + liste[i + 7] + ggrafik[1] + "</A>";
			} else {
				nlink += "&nbsp;<A HREF=\"#\" onMouseOver=\"return(true)\" onClick=\"window.open('"
						+ url
						+ "','640_"
						+ u_nick
						+ "','resizable=yes,scrollbars=yes,width=780,height=580'); return(false)\">"
						+ ggrafik[2] + liste[i + 7] + ggrafik[3] + "</A>";
			}
		}

		if ((homep_ext_link != "") && (liste[i + 6] != "G")) {
			var url = homep_ext_link + liste[i + 2];
			nlink += "&nbsp;<A HREF=\"#\" onMouseOver=\"return(true)\" onClick=\"window.open('"
					+ url
					+ "','640_"
					+ u_nick
					+ "','resizable=yes,scrollbars=yes,width=780,height=580'); return(false)\">"
					+ hgrafik + "</A>";
		} else if ((liste[i + 1] == "J") && communityfeatures == 1) {
			var url = "home.php" + stdparm2 + "&ui_userid=" + liste[i];
			nlink += "&nbsp;<A HREF=\"#\" onMouseOver=\"return(true)\" onClick=\"window.open('"
					+ url
					+ "','640_"
					+ u_nick
					+ "','resizable=yes,scrollbars=yes,width=780,height=580'); return(false)\">"
					+ hgrafik + "</A>";
		}

		if ((communityfeatures == 1) && (liste[i + 6] != "G")
				&& (inaktiv_mailsymbol != "1" || aktion != "chatuserliste")) {
			var nick = liste[i + 2].replace('/+/', "%2b");
			var url = "mail.php" + stdparm2
					+ "&aktion=neu2&neue_email[an_nick]=" + nick;
			nlink += "&nbsp;<A HREF=\"#\" onMouseOver=\"return(true)\" onClick=\"window.open('"
					+ url
					+ "','640_"
					+ u_nick
					+ "','resizable=yes,scrollbars=yes,width=780,height=580'); return(false)\">"
					+ mgrafik + "</A>";
		}

		var rowdef = "";

		if (aktion == "chatuserliste") {
			if ((level == "admin"))
				rowdef += "<TD>"
						+ fett[0]
						+ "<A HREF=\"#\" onMouseOver=\"return(true)\" onClick=\"gaguser('"
						+ liste[i + 2] + "'); return(false)\">G</A>" + fett[1]
						+ "</TD>";
			if ((level == "admin") || (level == "owner"))
				rowdef += "<TD>"
						+ fett[0]
						+ "<A HREF=\"#\" onMouseOver=\"return(true)\" onClick=\"kickuser('"
						+ liste[i + 2] + "'); return(false)\">K</A>" + fett[1]
						+ "</TD>";
			if ((level == "admin")
					&& (liste[i + 3] != "" || liste[i + 4] != ""))
				rowdef += "<TD>"
						+ fett[0]
						+ "<A HREF=\"#\" onMouseOver=\"return(true)\" onClick=\"sperren('"
						+ liste[i + 3] + "','" + liste[i + 4] + "','"
						+ liste[i + 2] + "'); return(false)\">S</A>" + fett[1]
						+ "</TD><TD>&nbsp;</TD>";
			if (inaktiv_ansprechen != "1") {
				rowdef += "<TD>"
						+ fett[0]
						+ "<A HREF=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext(' @"
						+ liste[i + 2] + " '); return(false)\">@</A>" + fett[1]
						+ "</TD>";
				rowdef += "<TD>"
						+ fett[0]
						+ "<A HREF=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext('/msg "
						+ liste[i + 2] + " '); return(false)\">&gt;</A>"
						+ fett[1] + "</TD>";
			}
		} else {
			if ((level == "admin") || (level == "owner"))
				rowdef += "<TD>"
						+ fett[0]
						+ "<A HREF=\"#\" onMouseOver=\"return(true)\" onClick=\"einladung('"
						+ liste[i + 2] + "'); return(false)\">E</A>" + fett[1]
						+ "</TD><TD>&nbsp;</TD>";
		}

		rowdef += "<TD WIDTH=\"90%\">" + nlink + "</TD>";

		if (color_index == "0")
			color_index = 1;
		else
			color_index = 0;

		document.write("<TR BGCOLOR=\"" + color[color_index] + "\">" + rowdef
				+ "</TR>\n");

	}
	document.write("</TABLE>\n");
}

function showsmilies(liste) {
	for ( var i = 0; i < liste.length; i += 2) {
		var rowdef = "<TD>&nbsp;"
				+ fett[0]
				+ "<A HREF=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext(' "
				+ liste[i] + " '); return(false)\">" + liste[i] + "</A>"
				+ fett[1] + "&nbsp;</TD>";
		rowdef += "<TD>" + fett[4] + liste[i + 1] + fett[5] + "</TD>";
		document.write("<TR BGCOLOR=\"" + color[i / 2 & 1] + "\">" + rowdef
				+ "</TR>\n");
	}
}

function showsmiliegrafiken(liste) {
	for ( var i = 0; i < liste.length; i += 3) {
		var rowdef = "<TD ALIGN=CENTER><A HREF=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext(' "
				+ liste[i]
				+ " '); return(false)\"><IMG SRC=\""
				+ smilies_pfad
				+ liste[i + 1]
				+ "\" BORDER=0 ALT=\""
				+ liste[i]
				+ "\"></A></TD>";
		rowdef += "<TD>" + fett[4] + liste[i + 2] + fett[5] + "</TD>";
		document.write("<TR BGCOLOR=\"" + color[i / 2 & 1] + "\">" + rowdef
				+ "</TR>\n");
	}
}

function userdetails(id) {
	var url = 'user.php' + stdparm + '&aktion=zeig&user=' + id;
	neuesFenster(url, id);
}

function sperren(host, ip, user) {
	var url = 'sperre.php' + stdparm2 + '&aktion=neu';
	if (host)
		url = url + '&hname=' + host;
	if (ip)
		url = url + '&ipaddr=' + ip;
	if (user)
		url = url + '&uname=' + user;
	openwindow('sperrfenster', url,
			'resizable=yes,scrollbars=yes,width=780,height=580');
}

function neuesFenster(url, name) {
	hWnd = window.open(url, name,
			'resizable=yes,scrollbars=yes,width=300,height=580');
}

function openwindow(name, url, param) {
	wins = window.open(url, name, param);
}

function findObj(n, d) { // v3.0
	var p, i, x;
	if (!d)
		d = document;
	if ((p = n.indexOf("?")) > 0 && parent.frames.length) {
		d = parent.frames[n.substring(p + 1)].document;
		n = n.substring(0, p);
	}
	if (!(x = d[n]) && d.all)
		x = d.all[n];
	for (i = 0; !x && i < d.forms.length; i++)
		x = d.forms[i][n];
	for (i = 0; !x && d.layers && i < d.layers.length; i++)
		x = findObj(n, d.layers[i].document);
	return x;
}
