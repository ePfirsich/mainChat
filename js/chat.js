var lastTimeID = 0;

$(document).ready(function() {
	$('#btnSend').click( function() {
		sendChatText();
		$('#chatInput').val("");
	});
	startChat();
});

function startChat(){
	setInterval( function() { getChatText(); }, 500);
}

function getChatText() {
	$.ajax({
		type: "GET",
		url: "/chat/refresh.php?lastTimeID=" + lastTimeID
	}).done( function( data )
	{
		var jsonData = JSON.parse(data);
		var jsonLength = jsonData.results.length;
		var html = "";
		var level = "";
		var status = "";
		for (var i = 0; i < jsonLength; i++) {
		var result = jsonData.results[i];
		
			html += "<div style=\"color:#" + result.c_farbe + "\">"+ result.vonuserid +"" + result.c_text + " " + result.level + "</div>";
			lastTimeID = result.c_id;
		
		}
		$('#view_ajax').append(html);
	});
}


function sendChatText(){
	var chatInput = $('#chatInput').val();
	if(chatInput != ""){
		$.ajax({
			type: "GET",
			url: "/submit.php?chattext=" + encodeURIComponent( chatInput )
		});
	}
}
