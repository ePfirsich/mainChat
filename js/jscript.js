/* mainChat Javascript Function Library, fidion GmbH */

wins = new Array;
user = new Array;

function appendtext_chat(text) {
	parent.frames['eingabe'].document.forms['form'].elements['text'].value += text;
	parent.frames['eingabe'].document.forms['form'].elements['text'].focus();
}

function insertAtCursor(myField, myValue) {
	if (document.selection) {
		//IE support
		myField.focus();
		sel = document.selection.createRange();
		sel.text = myValue;
	} else if (myField.selectionStart || myField.selectionStart == '0') {
		//MOZILLA and others
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