$(function() {

  // User rule manipulation
  var $rulesHolder = $('#rules-holder');
  var $addRule = $('#add-rule');
  var $newRuleCondition = $('#new-rule-condition');
  var $newRuleAction = $('#new-rule-action');
  var newRuleTemplate = $('#new-rule-template').prop('content');

  $rulesHolder.data('index', Math.floor($rulesHolder.find(':input').length/2));

  $addRule.on('click', function(evt) {
    evt.preventDefault();
    var index = $rulesHolder.data('index') || 0;
    var $clone = $(document.importNode(newRuleTemplate, true));
    $clone.find('td:nth-child(1) input')
      .attr('name', 'user[rules]['+index+'][name]')
      .val($newRuleCondition.val())
    ;
    $clone.find('td:nth-child(2) input')
      .attr('name', 'user[rules]['+index+'][value]')
      .val($newRuleAction.val())
    ;
    $newRuleCondition.val('');
    $newRuleAction.val('');
    $rulesHolder.append($clone);
    $rulesHolder.data('index', index+1);
  });

  $rulesHolder.on('click', '.remove-rule', function(evt) {
    evt.preventDefault();
    $(this).parents('tr').remove();
  });

}());
