$(window).resize( function() {
	if($("body").width()<=575)
		$("div.container > div.row > div.col-12 fieldset").css("width", "100%");
	else
		$("div.container > div.row > div.col-12 fieldset").css("width", "80%");
});

$(document).ready( function() {
	if($("body").width()<=575)
		$("div.container > div.row > div.col-12 fieldset").css("width", "100%");
	else
		$("div.container > div.row > div.col-12 fieldset").css("width", "80%");
});