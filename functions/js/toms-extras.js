(function($) {

$('.button').first().addClass('active');

$('.button').click(function(){
  var $this = $(this);
  $siblings = $this.parent().children(),
  position = $siblings.index($this);
  
  $('.subcontent div').removeClass('active').eq(position).addClass('active');
  
  $siblings.removeClass('active');
  $this.addClass('active');
})

})( jQuery );
