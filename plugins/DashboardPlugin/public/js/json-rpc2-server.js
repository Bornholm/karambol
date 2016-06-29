(function(JSONRPC2) {

  'use strict';

  var Server = JSONRPC2.Server = function() {
    this._methods = {};
  };

  Server.prototype.expose = function(methods, cb) {
    if(typeof methods === 'object') {
      for(var key in methods) {
        if(methods.hasOwnProperty(key)) {
          this.expose(key, methods[key]);
        }
      }
    } else {
      this._methods[methods] = cb;
      return this;
    }
  };

  Server.prototype.handleMessage = function(message) {

  };

}(this.JSONRPC2 = this.JSONRPC2 = {}))
