var app = new Vue({
  mixins: [mdMixin],
  el: '#app',
  data: {
    plain: '',
    converted: '',
    base_tags: [],
    inputed_tags: [],
    is_open_candidate_tags: false,
    candidate_tags: [],
    tag_kw: '',
  },
  created: function() {
    this._get_tags();
    this._get_inputed_tags();
  },
  methods: {
    _get_tags: function() {
      axios.post('/teratail/public/ajax_q/get_tags')
        .then(response => {
          // console.log(response.data)
          this.base_tags = response.data.tags;
        })
        .catch(e => {
          console.log(e)
        })
    },
    _get_inputed_tags: function() {
      axios.post('/teratail/public/ajax_q/get_inputed_tags')
        .then(response => {
          console.log(response.data)
          this.inputed_tags = response.data.tags;
        })
        .catch(e => {
          console.log(e)
        })
    },
    addToMyTag: function() {
      this.$refs.myTagForm.submit();
    },
    search_candidate_tags: function() {
      if (this.tag_kw === '') {
        this.is_open_candidate_tags = false;
        return;
      }

      this.filter_candidate_tags();
      if (this.candidate_tags.length !== 0) {
        this.is_open_candidate_tags = true
      }
    },
    remove_tag: function(tag_idx) {
      this.inputed_tags.splice(tag_idx, 1)
    },
    filter_candidate_tags: function() {
      this.candidate_tags = this.base_tags.filter(tag => tag.name.startsWith(this.tag_kw.toLowerCase()));
    },
    set_result: function(result, event) {
      event.preventDefault()
      this.inputed_tags.push(result)
      this.is_open_candidate_tags = false;
      this.tag_kw = '';
    },
    init: function() {
      this.plain = "```php\necho('hello');\n```";
    },
  }
})

$('#linkModal').on('shown.bs.modal', function(e) {
  const hyperlink_input = app.$refs.hyperlink_input
  hyperlink_input.focus();
  hyperlink_input.setSelectionRange(0, hyperlink_input.value.length);
});

// app.init();
app.convert();
