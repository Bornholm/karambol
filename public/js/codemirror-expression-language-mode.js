CodeMirror.defineSimpleMode("expression-language", {
  // The start state contains the rules that are intially used
  start: [
    {regex: /"(?:[^\\]|\\.)*?"/, token: "string"},
    {regex: /'(?:[^\\]|\\.)*?'/, token: "string"},
    {regex: /true|false/, token: "atom"},
    {regex: /0x[a-f\d]+|[-+]?(?:\.\d+|\d+\.?\d*)(?:e[-+]?\d+)?/i,
     token: "number"},
    {regex: /\/(?:[^\\]|\\.)*?\//, token: "variable-3"},
    {regex: /[-+\/*=<>!~]+/, token: "operator"},
    {regex: /(?:in|not|and|or|matches)\b/, token: "keyword"},
    // indent and dedent properties guide autoindentation
    {regex: /[a-z$][\w$]*/, token: "variable"},
  ]
});
