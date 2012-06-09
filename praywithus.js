// TODO
jQuery(document).ready(function($) {
    $('.praywithusButton').click(function() {
        var updateEl = $(this)
        $.post(PrayWithUs.ajaxurl,
               {'action': 'pray',
                'requestID': updateEl.attr('id') },
               function(d) {
                 updateEl.closest('.praying').text(d.contents)
               })
        
      })
  })
