const ratedToNo = ({ $btn }) => {
  const $rate_cnt = $btn.getElementsByClassName('js-rate_cnt')[0];
  $rate_cnt.innerHTML = parseInt($rate_cnt.innerHTML) - 1;
  $btn.classList.remove('btn-danger');
  $btn.classList.add('btn-primary');
}

const noToRated = ({ $btn }) => {
  const $rate_cnt = $btn.getElementsByClassName('js-rate_cnt')[0];
  $rate_cnt.innerHTML = parseInt($rate_cnt.innerHTML) + 1;
  $btn.classList.remove('btn-primary');
  $btn.classList.add('btn-danger');
}

const updateSumRates = ({ $sum_rates, add_num }) => {
  $sum_rates.innerHTML = parseInt($sum_rates.innerHTML) + (add_num);
}

const updateSumRatesByIfRateKind = ({ rate_kind, $sum_rates, add_num_obj }) => {
  if (rate_kind === 'high') {
    updateSumRates({ $sum_rates, add_num: add_num_obj.high });
  } else if (rate_kind === 'low') {
    updateSumRates({ $sum_rates, add_num: add_num_obj.low });
  }
}

var app = new Vue({
  el: '#app',
  mixins: [mixin_app, mdMixin, mdConvertMixin],
  data: {
    rewrite_requests: [],
    answers: [],
    is_show_rewrite_requests: false,
    user_id: '',
    text: '',
    input_answer: '', //回答入力欄
    answer_preview: '',
  },
  methods: {
    _ratedToNo_ifneed: function({ $btn, answer_id, rate_kind }) {
      const is_rated = $btn.classList.contains('btn-danger');
      if (is_rated) {
        ratedToNo({ $btn });
        this._ajax_update_rate({ answer_id, from_kind: this._get_opp_rate_kind(rate_kind), to_kind: rate_kind });
      }
    },
    _ajax_insert_rate: function({ answer_id, rate_kind }) {
      const params = new URLSearchParams();
      params.append('answer_id', answer_id);
      params.append('rate_kind', rate_kind);
      axios.post("/teratail/public/ajax_q/insert_rate", params)
        .then(response => {
          console.log(response.data)
        })
        .catch(e => {
          console.log(e)
        })
    },
    _ajax_update_rate: function({ answer_id, from_kind, to_kind }) {
      const params = new URLSearchParams();
      params.append('answer_id', answer_id);
      params.append('from_kind', from_kind);
      params.append('to_kind', to_kind);
      axios.post("/teratail/public/ajax_q/update_rate", params)
        .then(response => {
          console.log(response.data)
        })
        .catch(e => {
          console.log(e)
        })
    },
    _ajax_delete_rate: function({ answer_id, rate_kind }) {
      const params = new URLSearchParams();
      params.append('answer_id', answer_id);
      params.append('rate_kind', rate_kind);
      axios.post("/teratail/public/ajax_q/remove_rate", params)
        .then(response => {
          console.log(response.data)
        })
        .catch(e => {
          console.log(e)
        })
    },
    _get_opp_rate_kind: function(rate_kind) {
      return rate_kind === 'high' ? 'low' : 'high';
    },
    evaluate_rate: function(answer_id, rate_kind, event) {
      $target = event.target;
      const answer_idx = $target.dataset.answerIdx;
      const $sum_rates = document.getElementById(`jsSumRates_${answer_idx}`);
      //反対ボタンが評価済みならば、updateして戻る。
      let $opp_btn;
      if (rate_kind === 'high') {
        $opp_btn = $target.nextElementSibling;
      } else if (rate_kind === 'low') {
        $opp_btn = $target.previousElementSibling;
      }
      const opp_is_rated = $opp_btn.classList.contains('btn-danger');
      if (opp_is_rated) {
        ratedToNo({ $btn: $opp_btn });
        noToRated({ $btn: $target });
        this._ajax_update_rate({ answer_id, from_kind: this._get_opp_rate_kind(rate_kind), to_kind: rate_kind });
        updateSumRatesByIfRateKind({ rate_kind, $sum_rates, add_num_obj: { high: 2, low: -2 } });
        return;
      }

      //押下した評価ボタンが既に評価済みならば、評価を解除(評価を削除)(delete)
			//評価済みでないならば、評価する(評価を新たに登録)(insert)
      const target_is_rated = $target.classList.contains('btn-danger');
      if (target_is_rated) {
        ratedToNo({ $btn: $target });//押下ボタンdomを「評価済み」から「評価なし」の状態へ。
        this._ajax_delete_rate({ answer_id, rate_kind });
        updateSumRatesByIfRateKind({ rate_kind, $sum_rates, add_num_obj: { high: -1, low: 1 } });
      } else {
        noToRated({ $btn: $target });//押下ボタンdomを「評価なし」から「評価済み」の状態へ。
        this._ajax_insert_rate({ answer_id, rate_kind });
        updateSumRatesByIfRateKind({ rate_kind, $sum_rates, add_num_obj: { high: 1, low: -1 } });

      }
    },
    _get_answers: function() {
      const params = new URLSearchParams();
      params.append('question_id', this.$refs.question_id.value);
      axios.post('/teratail/public/ajax_q/get_answers', params)
        .then(response => {
          console.log(response.data)
          this.answers = response.data.answers
          this.user_id = response.data.user_id
        })
        .catch(e => {
          console.log(e)
        })
    },
    insert_rewrite_request: function() {
      const params = new URLSearchParams();
      params.append('question_id', this.$refs.question_id.value);
      params.append('rewrite_request_add_text', this.$refs.rewrite_request_add_input.value);
      axios.post('/teratail/public/ajax_q/insert_rewrite_requests', params)
        .then(response => {
          // console.log(response.data)
          this._get_rewrite_requests()
        })
        .catch(e => {
          console.log(e)
        })
    },
  }
})
