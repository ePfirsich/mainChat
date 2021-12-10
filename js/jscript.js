/* mainChat Javascript Function Library, fidion GmbH */

wins = new Array;
user = new Array;

function sendtext(text) {
	parent.frames['schreibe'].location = 'schreibe.php' + stdparm2 + '&text=' + text;
	parent.frames['eingabe'].document.forms['form'].elements['text'].focus();
}

function sendtext_opener(text) {
	opener.parent.frames['schreibe'].location = 'schreibe.php' + stdparm2 + '&text=' + text;
	opener.parent.frames['eingabe'].document.forms['form'].elements['text'].focus();
}

function appendtext_chat(text) {
	parent.frames['eingabe'].document.forms['form'].elements['text'].value += text;
	parent.frames['eingabe'].document.forms['form'].elements['text'].focus();
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