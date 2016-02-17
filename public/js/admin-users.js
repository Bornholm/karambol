$(function() {

  // User attribute manipulation
  var $attributesHolder = $('#attributes-holder');
  var $addAttribute = $('#add-attribute');
  var $newAttrName = $('#new-attribute-name');
  var $newAttrValue = $('#new-attribute-value');
  var newAttributeTemplate = $('#new-attribute-template').prop('content');

  $attributesHolder.data('index', $attributesHolder.find(':input').length/2);

  $addAttribute.on('click', function(evt) {
    evt.preventDefault();
    var index = $attributesHolder.data('index') || 0;
    var $clone = $(document.importNode(newAttributeTemplate, true));
    $clone.find('td:nth-child(1) input')
      .attr('name', 'form[attributes]['+index+'][name]')
      .val($newAttrName.val())
    ;
    $clone.find('td:nth-child(2) input')
      .attr('name', 'form[attributes]['+index+'][value]')
      .val($newAttrValue.val())
    ;
    $newAttrName.val('');
    $newAttrValue.val('');
    $attributesHolder.append($clone);
    $attributesHolder.data('index', index+1);
  });

  $attributesHolder.on('click', '.remove-attribute', function(evt) {
    evt.preventDefault();
    $(this).parents('tr').remove();
  });

}());
