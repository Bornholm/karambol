$(function() {

  var adminRulesOpts = $(document.currentScript).data('admin-rules');

  // User rule manipulation
  var $rulesHolder = $('#rules-holder');
  var $addRule = $('#add-rule');

  var $newRuleHolder = $('#new-rule');
  var $newRuleCondition = $('#new-rule-condition');
  var $newRuleAction = $('#new-rule-action');
  var $newRuleWeight = $('#new-rule-weight');
  var newRuleUnsaved = false;

  var newRuleTemplate = $('#new-rule-template').prop('content');

  $rulesHolder.data('index', Math.floor($rulesHolder.find(':input').length/2));

  // Alert user of unsaved rule on exit
  $newRuleHolder.on('change', function(evt) {
    newRuleUnsaved = $newRuleWeight.val() != 0 ||
      $newRuleCondition.val() != '' ||
      $newRuleCondition.val() != ''
    ;
  });

  $('form[name="ruleset"]').on('submit', function(evt) {
    if(!newRuleUnsaved) return;
    var confirmSave = confirm(adminRulesOpts.confirmNewRuleUnsaved);
    if(confirmSave) return;
    evt.preventDefault();
    return false;
  });

  // Add the new rule to set
  $addRule.on('click', function(evt) {

    evt.preventDefault();

    var index = $rulesHolder.data('index') || 0;
    var $clone = $(document.importNode(newRuleTemplate, true));

    var $conditionTextarea = $clone.find('td:nth-child(2) textarea');
    $conditionTextarea.attr('name', 'ruleset[rules]['+index+'][condition]')
      .val($newRuleCondition.val())
    ;

    var $actionTextarea = $clone.find('td:nth-child(3) textarea');
    $actionTextarea.attr('name', 'ruleset[rules]['+index+'][action]')
      .val($newRuleAction.val())
    ;

    var $weightInput = $clone.find('td:nth-child(1) input[type="number"]');
    $weightInput.attr('name', 'ruleset[rules]['+index+'][weight]')
      .val($newRuleWeight.val())
    ;

    $newRuleCondition.val('').trigger('change');
    $newRuleAction.val('').trigger('change');
    $newRuleWeight.val(0).trigger('change');

    $rulesHolder.append($clone);
    $rulesHolder.data('index', index+1);

    transformToCodeMirror($conditionTextarea[0]);
    transformToCodeMirror($actionTextarea[0]);

  });

  // Remove rule when user click on "Trash" button
  $rulesHolder.on('click', '.remove-rule', function(evt) {
    evt.preventDefault();
    $(this).parents('tr').remove();
  });

  // Automated sort of rules based on weight
  $rulesHolder.on('change', '.karambol-rule-weight', function(evt) {
    var $row = $(this).parents('tr');
    var weight = parseInt($(this).val());
    var moved = false;
    do { moved = sortRuleRowByWeight($row, weight); } while(moved)
  });

  function sortRuleRowByWeight($row, weight) {

    var $previousRow = $row.prev();
    var $nextRow = $row.next();

    var previousWeight = parseInt($previousRow.find('input.karambol-rule-weight').val());
    if(previousWeight < weight) {
      $previousRow.before($row);
      return true;
    }

    var nextWeight = parseInt($nextRow.find('input.karambol-rule-weight').val());
    if(nextWeight > weight) {
      $nextRow.after($row);
      return true;
    }

    return false;

  }

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
