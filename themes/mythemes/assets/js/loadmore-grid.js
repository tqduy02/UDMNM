(function($){
  $(document).on('click', '#loadMoreGrid', function(){
    var $btn = $(this);
    var current = parseInt($btn.data('current'), 10) || 1;
    var max = parseInt($btn.data('max'), 10) || 1;

    if (current >= max) return;

    $btn.prop('disabled', true).text('Loading...');

    $.post(MYT_GRID.ajaxurl, {
      action: 'myt_load_more_grid',
      nonce:  MYT_GRID.nonce,
      page:   current + 1,
      cats:   JSON.stringify(MYT_GRID.cats || [])
    })
    .done(function(res){
      if (res && res.success && res.data && res.data.html){
        $('#gridList').append(res.data.html);
        $btn.data('current', current + 1);
        if (current + 1 >= max) { $btn.remove(); }
        else { $btn.prop('disabled', false).text('More posts'); }
      } else {
        $btn.prop('disabled', false).text('More posts');
      }
    })
    .fail(function(){
      $btn.prop('disabled', false).text('More posts');
    });
  });
})(jQuery);
