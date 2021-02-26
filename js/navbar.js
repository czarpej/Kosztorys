$("ol > li").attr("id", function(i) {
   return "li"+(i+1);
});
$("ol > li > ul").attr("id", function(i) {
	return "ul"+(i+1);
});

function mouseoverback( i ){
  return function(){
    $("#li"+i+" > ul").stop().show(300);
    stop();	
  }
}

function mouseleaveback( i ){
  return function(){
    $("#li"+i+" > ul").hide(300);
  }
}

$(document).ready(function(){
  for(var i = 1; i <= ($("ol > li").length); i++) {
    $('#li' + i).mouseover( mouseoverback( i ) );
    $('#li' + i).mouseleave( mouseleaveback( i ) );
  }
});