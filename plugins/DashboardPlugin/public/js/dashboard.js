(function($, window, JSONRPC2) {

  'use strict';

  var $window = $(window);
  var rpcServer = new JSONRPC2.Server();

  rpcServer.expose({
    'setPreferredHeight': setPreferredHeight,
    'setTitle': setTitle
  });

  // Récupération des widgets présents dans la page
  $('.karambol-widget[data-url]').each(function(i, widget) {

    var $widget = $(widget);
    var url = $widget.data('url');

    $window.on('message', onWidgetMessage.bind($widget));

    if(url) $widget.attr('src', url);

  });

  function onWidgetMessage(event) {
    var data;
    try {
      message = JSON.parse(event.data);
    } catch(err) {
      console.error(err);
      return;
    }
    if(message) {
      var isNotification = !(id in message);
      rpcServer.handleMessage(
        message,
        isNotification ? onRPCServerResult.bind(this) : null
      );
    }
  }

  function onRPCServerResult(err, result) {

  }

  function setPreferredHeight(args, cb) {

  }

  function setTitle(args, cb) {

  }

}($, window, JSONRPC2));
