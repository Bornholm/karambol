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
    var $conditionTextarea = $clone.find('td:nth-child(2) textarea');
    $conditionTextarea.attr('name', 'rule_set[rules]['+index+'][condition]')
      .val($newRuleCondition.val())
    ;
    var $actionTextarea = $clone.find('td:nth-child(3) textarea');
    $actionTextarea.attr('name', 'rule_set[rules]['+index+'][action]')
      .val($newRuleAction.val())
    ;
    $newRuleCondition.val('').trigger('change');
    $newRuleAction.val('').trigger('change');
    $rulesHolder.append($clone);
    $rulesHolder.data('index', index+1);
    transformToCodeMirror($conditionTextarea[0]);
    transformToCodeMirror($actionTextarea[0]);
  });

  $rulesHolder.on('click', '.remove-rule', function(evt) {
    evt.preventDefault();
    $(this).parents('tr').remove();
  });

  // CodeMirror bootstrapping

  $('textarea[data-codemirror]').each(function(i, textarea) {
    transformToCodeMirror(textarea);
  });

  function transformToCodeMirror(textarea) {
    var $textarea = $(textarea);
    var opts = $textarea.data('codemirror');
    opts.viewportMargin = Infinity;
    var cm = CodeMirror.fromTextArea(textarea, opts);
    cm.on('change', function() { cm.save(); });
    $textarea.on('change', function() { cm.setValue($textarea.val()); });
  }

}());
