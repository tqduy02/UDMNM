(function($){
  $(document).on('click', '#loadMorePosts', function(){
    var $btn = $(this);
    var current = parseInt($btn.data('current'), 10) || 1;
    var max = parseInt($btn.data('max'), 10) || 1;

    if (current >= max) return;

    $btn.prop('disabled', true).text('Loading...');

    // MYT_LOADMORE.cats là mảng ID (được localize từ PHP)
    $.post(MYT_LOADMORE.ajaxurl, {
      action: 'myt_load_more',
      nonce:  MYT_LOADMORE.nonce,
      page:   current + 1,
      'cats[]': MYT_LOADMORE.cats   // gửi như array form-data
    }, function(res){
      if (res && res.success && res.data && res.data.html){
        $('#blogList').append(res.data.html);
        $btn.data('current', current + 1);
        if (current + 1 >= max) {
          $btn.remove();
        } else {
          $btn.prop('disabled', false).text('More posts');
        }
      } else {
        $btn.prop('disabled', false).text('More posts');
      }
    }).fail(function(){
      $btn.prop('disabled', false).text('More posts');
    });
  });
})(jQuery);
