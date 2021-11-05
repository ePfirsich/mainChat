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
			.write("<table style=\"width:100%;\" class=\"tabelle_gerust\">\n");

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
			var dlink = "<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"userdetails("
					+ liste[i]
					+ ")\">"
					+ (liste[i + 5] ? "(" + liste[i + 2] + ")" : liste[i + 2])
					+ "<a>" + tgegrafik;
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
			var url = "index.php?aktion=hilfe-community";
			if ((liste[i + 6] == "C") || (liste[i + 6] == "S")) {
				nlink += "&nbsp;<a href=\"index.php?aktion=hilfe-community\" target=\"_blank\">"
						+ ggrafik[0] + liste[i + 7] + ggrafik[1] + "<a>";
			} else {
				nlink += "&nbsp;<a href=\"index.php?aktion=hilfe-community\" target=\"_blank\">"
						+ ggrafik[2] + liste[i + 7] + ggrafik[3] + "<a>";
			}
		}

		if ((homep_ext_link != "") && (liste[i + 6] != "G")) {
			var url = homep_ext_link + liste[i + 2];
			nlink += "&nbsp;<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"window.open('"
					+ url
					+ "','640_"
					+ u_nick
					+ "','resizable=yes,scrollbars=yes,width=780,height=580'); return(false)\">"
					+ hgrafik + "<a>";
		} else if ((liste[i + 1] == "J") && communityfeatures == 1) {
			var url = "home.php" + stdparm2 + "&ui_userid=" + liste[i];
			nlink += "&nbsp;<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"window.open('"
					+ url
					+ "','640_"
					+ u_nick
					+ "','resizable=yes,scrollbars=yes,width=780,height=580'); return(false)\">"
					+ hgrafik + "<a>";
		}

		if ((communityfeatures == 1) && (liste[i + 6] != "G")
				&& (inaktiv_mailsymbol != "1" || aktion != "chatuserliste")) {
			var nick = liste[i + 2].replace('/+/', "%2b");
			var url = "mail.php" + stdparm2
					+ "&aktion=neu2&neue_email[an_nick]=" + nick;
			nlink += "&nbsp;<a href=\""
					+ url
					+ "\" target=\"_blank\">"
					+ mgrafik + "<a>";
		}

		var rowdef = "";

		if (aktion == "chatuserliste") {
			if ((level == "admin"))
				rowdef += "<td class=\"" + color[color_index] + "\">"
						+ fett[0]
						+ "<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"gaguser('"
						+ liste[i + 2] + "'); return(false)\">G<a>" + fett[1]
						+ "</td>";
			if ((level == "admin") || (level == "owner"))
				rowdef += "<td class=\"" + color[color_index] + "\">"
						+ fett[0]
						+ "<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"kickuser('"
						+ liste[i + 2] + "'); return(false)\">K<a>" + fett[1]
						+ "</td>";
			if ((level == "admin")
					&& (liste[i + 3] != "" || liste[i + 4] != ""))
				rowdef += "<td class=\"" + color[color_index] + "\">"
						+ fett[0]
						+ "<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"sperren('"
						+ liste[i + 3] + "','" + liste[i + 4] + "','"
						+ liste[i + 2] + "'); return(false)\">S<a>" + fett[1]
						+ "</td><td class=\"" + color[color_index] + "\">&nbsp;</td>";
			if (inaktiv_ansprechen != "1") {
				rowdef += "<td class=\"" + color[color_index] + "\">"
						+ fett[0]
						+ "<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext(' @"
						+ liste[i + 2] + " '); return(false)\">@<a>" + fett[1]
						+ "</td>";
				rowdef += "<td class=\"" + color[color_index] + "\">"
						+ fett[0]
						+ "<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext('/msg "
						+ liste[i + 2] + " '); return(false)\">&gt;<a>"
						+ fett[1] + "</td>";
			}
		} else {
			if ((level == "admin") || (level == "owner"))
				rowdef += "<td class=\"" + color[color_index] + "\">"
						+ fett[0]
						+ "<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"einladung('"
						+ liste[i + 2] + "'); return(false)\">E<a>" + fett[1]
						+ "</td><td class=\"" + color[color_index] + "\">&nbsp;</td>";
		}

		rowdef += "<td style=\"width:90%;\" class=\"" + color[color_index] + "\">" + nlink + "</td>";

		if (color_index == "0")
			color_index = 1;
		else
			color_index = 0;

		document.write("<tr>" + rowdef
				+ "</tr>\n");

	}
	document.write("</table>\n");
}

function showsmilies(liste) {
	for ( var i = 0; i < liste.length; i += 2) {
		var rowdef = "<td class=\"" + color[i / 2 & 1] + "\">&nbsp;"
				+ fett[0]
				+ "<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext(' "
				+ liste[i] + " '); return(false)\">" + liste[i] + "</a>"
				+ fett[1] + "&nbsp;</td>";
		rowdef += "<td class=\"" + color[i / 2 & 1] + "\">" + fett[4] + liste[i + 1] + fett[5] + "</td>";
		document.write("<tr>" + rowdef
				+ "</tr>\n");
	}
}

function showsmiliegrafiken(liste) {
	for ( var i = 0; i < liste.length; i += 3) {
		var rowdef = "<td style=\"text-align:center;\" class=\"" + color[i / 2 & 1] + "\"><a href=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext(' "
				+ liste[i]
				+ " '); return(false)\"><img src=\""
				+ smilies_pfad
				+ liste[i + 1]
				+ "\" border=\"0\" alt=\""
				+ liste[i]
				+ "\"></a></td>";
		rowdef += "<td class=\"" + color[i / 2 & 1] + "\">" + fett[4] + liste[i + 2] + fett[5] + "</td>";
		document.write("<tr>" + rowdef
				+ "</tr>\n");
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
			'resizable=yes,scrollbars=yes,width=340,height=580');
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
