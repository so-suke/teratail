marked.setOptions({breaks : true});
var renderer = new marked.Renderer();

renderer.code = function(code, lang, escaped) {
  if (this.options.highlight) {
    var out = this.options.highlight(code, lang);
    if (out != null && out !== code) {
      escaped = true;
      code = out;
    }
  }

  if (!lang) {
    return '<pre><code class="hljs">' +
      code +
      '</code></pre>';
  }

  return `<pre class="${this.options.langPrefix}"><code class="hljs ${this.options.langPrefix}${escape(lang, true)}" data-language="${lang}">${code}</code></pre>\n`;
};

renderer.strong = function(text) {
  return '<strong>' + text + '</strong>';
};

renderer.table = function(header, body) {
  if (body) body = '<tbody>' + body + '</tbody>';

  return '<table class="table w-50">\n' +
    '<thead>\n' +
    header +
    '</thead>\n' +
    body +
    '</table>\n';
};

renderer.list = function(body, ordered, start) {
  var type = ordered ? 'ol' : 'ul',
    startatt = (ordered && start !== 1) ? (' start="' + start + '"') : '';
  return '<' + type + startatt + '>\n' + body + '</' + type + '>\n';
};

renderer.listitem = function(text) {
  return '<li>' + text + '</li>\n';
};
