const MY_TAG_MODE = {
  COMMON: 'common',
  EDITING: 'editing',
}
const MY_TAG_FILTER_MODE = {
  NONE: 'none',
  STRICT: 'strict',
}
const QUESTION_FILTER_MODE = {
  CREATED_DESC: 'created_desc',
  UNANSWERED: 'unanswered',
  UNRESOLVED: 'unresolved',
  RESOLVED: 'resolved',
};

// html内で参照するために定数をvuejsで読み込みしています。
var app = new Vue({
  el: '#app',
  data: {
    base_tags: [],
    inputed_tags: [],
    is_open_candidate_tags: false,
    candidate_tags: [],
    tag_kw: '',
    questions: [],
    my_tags: [],
    my_tag_mode: MY_TAG_MODE.COMMON,
    MY_TAG_MODE,
    is_editing_my_tag: false,
    my_tag: {
      filter_mode: MY_TAG_FILTER_MODE.NONE, //現在のフィルターモード
      FILTER_MODE: MY_TAG_FILTER_MODE, //フィルターモードの定数
    },
    QUESTION_FILTER_MODE,
    now_question_filter_mode: QUESTION_FILTER_MODE.CREATED_DESC,
    paginator: null,
  },
  created: function() {
    this._getTags();
    this._getInits();
  },
  methods: {
    _getTags: function() {
      axios.post('/teratail/public/ajax_q/get_tags')
        .then(response => {
          // console.log(response.data)
          this.base_tags = response.data.tags;
        })
        .catch(e => {
          console.log(e)
        })
    },
    _getQuestionsInit: function() {
			let axios_url = '';
			if(this.paginator === null) {
				axios_url = '/teratail/public/ajax_q/get_questions';
			} else {
				axios_url = `/teratail/public/ajax_q/get_questions?page=${this.paginator.current_page}`;
			}
      axios.get(axios_url)
        .then(response => {
          // console.log(response.data)
          this.paginator = response.data.questions;
          this.questions = response.data.questions.data;
          this.now_question_filter_mode = response.data.q_filter_mode;
        })
        .catch(e => {
          console.log(e)
        })
    },
    _getInits: function() {
      this._getQuestionsInit();
      axios.get('/teratail/public/ajax_q/get_my_tags')
        .then(response => {
          // console.log(response.data)
          this.my_tags = response.data.my_tags;
          this.my_tag.filter_mode = response.data.my_tag_filter_mode;
        })
        .catch(e => {
          console.log(e)
        })
    },
    changePage: function(page_num) {
      if (page_num > this.paginator.last_page) {
        page_num = this.paginator.last_page;
      }
      this.paginator.current_page = page_num;
      this._getQuestionsInit();
    },
    toggleQuestionsFilterMode: function(event) {
      event.preventDefault();
      const $target = event.target;
			this.paginator.current_page = 1;
      this.now_question_filter_mode = $target.dataset.question_filter_mode;
      axios.post(`/teratail/public/ajax_q/register_questions_filter_mode/${this.now_question_filter_mode}`)
        .then(response => {
          console.log(response.data)
          this._getInits();
        })
        .catch(e => {
          console.log(e)
        })
    },
		//Myタグによる質問絞り込みの切替。'なし' <=> '厳密'
    toggleMyTagFilterMode: function(event) {
      const $target = event.target;
      if ($target.checked === true) {
        this.my_tag.filter_mode = this.my_tag.FILTER_MODE.STRICT;
      } else {
        this.my_tag.filter_mode = this.my_tag.FILTER_MODE.NONE;
      }

      axios.post(`/teratail/public/ajax_q/register_my_tag_filter_mode/${this.my_tag.filter_mode}`)
        .then(response => {
          console.log(response.data)
          this._getInits();
        })
        .catch(e => {
          console.log(e)
        })
    },
    toEditMyTag: function() {
      this.my_tag_mode = MY_TAG_MODE.EDITING;
    },
    endEditMyTag: function() {
      this.my_tag_mode = MY_TAG_MODE.COMMON;
    },
    addToMyTag: function() {
      let params = new URLSearchParams();
      this.inputed_tags.forEach(tag => {
        params.append('tag_ids[]', tag.id);
      });

      axios.post('/teratail/public/ajax_q/add_to_my_tag', params)
        .then(response => {
          console.log(response.data)
          this._getInits();
          this.inputed_tags = [];
        })
        .catch(e => {
          console.log(e)
        })
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
    clicked_mytag: function(tag_idx, event) {
      if (this.my_tag_mode === this.MY_TAG_MODE.EDITING) {
        event.preventDefault();
        this.delete_my_tag(tag_idx);
      }
    },
    delete_my_tag: function(tag_idx) {
      let params = new URLSearchParams();
      const tag = this.my_tags[tag_idx];
      params.append('tag_id', tag.id);
      axios.post('/teratail/public/ajax_q/delete_my_tag', params)
        .then(response => {
          console.log(response.data)
          const deleted_tag = this.my_tags.splice(tag_idx, 1)[0];
          this._getInits();
        })
        .catch(e => {
          console.log(e)
        })
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
  },
});
