$(function() {
  $('[data-ratio]').each(function(i, el) { applySizeRatio(el); });
  function applySizeRatio(el) {
    var $el = $(el);
    var ratio = $el.data('ratio').split(':');
    var height = $el.width()*(parseInt(ratio[1])/parseInt(ratio[0]));
    $el.height(height)
      .css({'line-height': height+'px', opacity: 1})
    ;
  }
});
