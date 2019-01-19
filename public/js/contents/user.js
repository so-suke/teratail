var app = new Vue({
  el: '#app',
  data: {
    plain: '',
    converted: '',
    base_tags: [],
    inputed_tags: [],
    is_open_candidate_tags: false,
    candidate_tags: [],
    tag_kw: '',
    title: '',
    questions: [],
  },
  methods: {
		get_clip_questions: function() {
      axios.get('/teratail/public/ajax_q/get_clip_questions')
        .then(response => {
          // console.log(response.data)
          this.questions = response.data.questions;
        })
        .catch(e => {
          console.log(e)
        })
    },
    openBelowList: function(event) {
      const $target = event.target;
      const $list = $target.nextElementSibling;
      $list.classList.toggle("d-none");
    },
  }
});
