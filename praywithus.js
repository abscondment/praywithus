// TODO
jQuery(document).ready(function($) {
    $('.praywithusButton').click(function() {
        var updateEl = $(this)
        $.post(PrayWithUs.ajaxurl,
               {'action': 'pray',
                'requestID': updateEl.attr('id') },
               function(d) {
                 updateEl.parent().find('.count').text(d.count)
               })
         .complete(function() { updateEl.parent().find('.hidePost').hide() })
        
      })
  })
