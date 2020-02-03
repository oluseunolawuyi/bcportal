<!--

if (self!=top)
{
top.location.href=self.location.href;
}

///////

$.fn.animateBG = function(x, y, speed, def) {
    var pos = this.css('background-position').split(' ');
    this.x = 0,
    this.y = def;
    $.Animation( this, {
        x: x,
        y: y
      }, { 
        duration: speed
      }).progress(function(e) {
          this.css('background-position', e.tweens[0].now+'px '+e.tweens[1].now+'px');
    });
    return this;
}

$(document).ready(function(){
						   
$("html, body").animate({scrollTop:0}, "slow");

$(window).resize(function (){						
if($("button.collapse").css("display") == "none"){
done = 0
$(".header2 ul").show();
}
if($("button.collapse").css("display") == "block"){
if(done == 0){
done = 1;
$(".header2 ul").hide();
}
}
});

//////////////////////////////////////////////////////////
$(".only-no").keyup(function(){
var this_val = this.value;
if(isNaN(this_val)){
this.value = this_val.replace(/[^0-9.]/gi, "");
}	
}).change(function(){
var this_val = this.value;
if(isNaN(this_val)){
this.value = this_val.replace(/[^0-9.]/gi, "");
}	
});

////////////////////////////////////////////////////
$(".header2 .collapse").click(function(){
$(".header2 ul").slideToggle();
});

    //smoothscroll
    $('a.nav_top').on('click', function (e) {
        e.preventDefault();
        $(document).off("scroll");
		
        var target = this.hash,
            menu = target;
        $target = $(target);
        $('html, body').stop().animate({
            'scrollTop': $target.offset().top+2
        }, 500, 'swing', function () {
            window.location.hash = target;
            $(document).on("scroll", onScroll);
        });
    });

////////////////////////////////////////////

//////////////////////////////////////////////////
$(".newsletter").submit(function(e){
e.preventDefault();  
var formdata = new FormData(this);
$(".general-fade").show();
var page_url = $(this).attr("action");

$.ajax({
url: page_url,
type: "POST",
data: formdata,
mimeTypes:"multipart/form-data",
contentType: false,
cache: false,
processData: false,
success: function(data){
$(".general-result").html(data);
$(".general-fade").hide();
},error: function(){
sweetAlert("Notice", "Error occured!", "error");
}
});

});
///////////////////////////////////////////////

//Zoom effect
$('.event-img img').hover(function() {
	$(this).addClass('transition');
}, function() {
	$(this).removeClass('transition');
});
/////////////////////////////////////

});

function comma_separator(x) {
return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function my_confirm(conf_title,conf_text,conf_link){
swal({
  title: conf_title,
  text: conf_text,
  type: "warning",
  showCancelButton: true,
  confirmButtonColor: "#DD6B55",
  confirmButtonText: "Yes",
  closeOnConfirm: false
},
function(isConfirm){
  if (isConfirm) {
location.href = conf_link;
  } else {
return false;
  }
});
}

//////////////////////////////////////////////

//Fly in effect
var timer = 0;
function recheck() {
    var window_top = $(this).scrollTop();
    var window_height = $(this).height();
    var view_port_s = window_top;
    var view_port_e = window_top + window_height;
     
    if ( timer ) {
      clearTimeout( timer );
    }
     
    $('.fly').each(function(){
      var block = $(this);
      var block_top = block.offset().top;
      var block_height = block.height();
       
      if ( block_top < view_port_e ) {
        timer = setTimeout(function(){
          block.addClass('show-block');
        },100);      
      } else {
        timer = setTimeout(function(){
          block.removeClass('show-block');
        },100);         
      }
    });
}
 
$(function(){
  $(window).scroll(function(){
    recheck();
  });
   
  $(window).resize(function(){
     recheck();  
  });

  recheck();
});
///////////////////////////////////////

//Jarallax
        $('.jarallax').jarallax({
            speed: 0.5,
            imgWidth: 1366,
            imgHeight: 768
        });
//-->