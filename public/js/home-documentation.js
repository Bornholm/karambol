(function() {

  var docPageRegEx = /^#\/markdown\/(.*)$/;
  var absolutePathRegex = /^(\/|http)/i;
  var markdownExtensionRegex = /\.(markdown|md|commonmark)$/i;

  onHashChange();
  $(window).on('hashchange', onHashChange);

  function onHashChange() {
    var hash = window.location.hash;
    var docPage = getDocPage(hash);
    if(!docPage) return;
    navigateToDocPage(docPage);
  }

  function navigateToDocPage(docPage) {
    window.location = '/doc/'+docPage;
  }

  function getDocPage(hash) {
    var matches = docPageRegEx.exec(hash);
    if(!matches || !matches[1]) return null;
    var docPage = matches[1];
    if(absolutePathRegex.test(docPage)) return null;
    return docPage.replace(markdownExtensionRegex, '');
  }

}());
