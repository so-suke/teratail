var app = new Vue({
  el: '#app',
  mixins: [mixin_app, mdMixin, mdConvertMixin],
})
app.convert();
