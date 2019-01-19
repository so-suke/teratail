const marked = require('marked');

var lexer = new marked.Lexer();
// var tokens = lexer.lex("1. 番号リスト\n番号リスト");
// console.log(tokens);
console.log(lexer.rules);