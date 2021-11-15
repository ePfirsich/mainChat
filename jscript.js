/* mainChat Javascript Function Library, fidion GmbH */

wins = new Array;
user = new Array;

function sendtext(text) {
	parent.frames['schreibe'].location = 'schreibe.php' + stdparm2 + '&text=' + text;
	parent.frames['eingabe'].document.forms['form'].elements['text2'].focus();
}

function sendtext_opener(text) {
	opener.parent.frames['schreibe'].location = 'schreibe.php' + stdparm2 + '&text=' + text;
	opener.parent.frames['eingabe'].document.forms['form'].elements['text2'].focus();
}

function appendtext_chat(text) {
	parent.frames['eingabe'].document.forms['form'].elements['text2'].value += text;
	parent.frames['eingabe'].document.forms['form'].elements['text2'].focus();
}

function insertAtCursor(myField, myValue) {
	//IE support
	if (document.selection) {
		myField.focus();
		sel = document.selection.createRange();
		sel.text = myValue;
	}
	//MOZILLA and others
	else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		myField.value = myField.value.substring(0, startPos)
			+ myValue
			+ myField.value.substring(endPos, myField.value.length);
	} else {
		myField.value += myValue;
	}
}

function appendtext_forum(text) {
	insertAtCursor(document.forms['form'].elements['po_text'], text).focus();
	document.forms['form'].elements['po_text'].focus();
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

function refresh() {
	this.location.href = this.location.href;
}

function sperren(host, ip, user) {
	var url = 'inhalt.php?seite=sperren' + stdparm2 + '&aktion=neu';
	if (host)
		url = url + '&hname=' + host;
	if (ip)
		url = url + '&ipaddr=' + ip;
	if (user)
		url = url + '&uname=' + user;
	openwindow('sperrfenster', url,
			'resizable=yes,scrollbars=yes,width=780,height=580');
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
