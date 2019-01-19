var mdMixin = {
  data: {
    converted: '',
  },
  methods: {
    convert: function() {
      this.converted = marked(this.$refs.mdInpPlace.value, { renderer: renderer });
    },
    showDesc: function(e) {
      const e_target = e.target;
      const bounding_client_rect = e_target.getBoundingClientRect();
      const desc = e_target.dataset.desc;
      const left = window.pageXOffset + bounding_client_rect.left - (desc.length * 5);
      const top = window.pageYOffset + bounding_client_rect.top + 35;
      this.$refs.mdInpAuxDesc.style.display = "block";
      this.$refs.mdInpAuxDesc.innerHTML = desc;
      this.$refs.mdInpAuxDesc.style.left = `${left}px`;
      this.$refs.mdInpAuxDesc.style.top = `${top}px`;
    },
    hideDesc: function() {
      this.$refs.mdInpAuxDesc.style.display = "none";
    },
    _mdInpAuxInsertFocus: function({ start_txt, main_txt, end_txt, special = null }) {
      const start_range = special !== null ? special.start_range : this.$refs.mdInpPlace.value.length + start_txt.length;
      const end_range = special !== null ? start_range + special.range_target.length : start_range + main_txt.length

      this.$refs.mdInpPlace.value += `${start_txt}${main_txt}${end_txt}`;
      setTimeout(() => {
        this.$refs.mdInpPlace.focus();
        this.$refs.mdInpPlace.setSelectionRange(start_range, end_range);
        this.convert();
      }, 1);
    },
    mdInpAux_bold: function() {
      const start_txt = '**';
      const main_txt = 'ボールドテキスト';
      const end_txt = '**';

      this._mdInpAuxInsertFocus({ start_txt, main_txt, end_txt });
    },
    mdInpAux_italic: function() {
      //todo italickにならないので修正
      const start_txt = '__';
      const main_txt = 'イタリックテキスト';
      const end_txt = '__';

      this._mdInpAuxInsertFocus({ start_txt, main_txt, end_txt });
    },
    mdInpAux_del: function() {
      const start_txt = '~~';
      const main_txt = '打ち消し線';
      const end_txt = '~~';

      this._mdInpAuxInsertFocus({ start_txt, main_txt, end_txt });
    },
    mdInpAux_heading: function() {
      const start_txt = '### ';
      const main_txt = 'ヘディングのテキスト';
      const end_txt = '';

      this._mdInpAuxInsertFocus({ start_txt, main_txt, end_txt });
    },
    mdInpAux_showLinkModal: function() {
      const hyperlink_input = this.$refs.hyperlink_input;
      hyperlink_input.value = 'http://';
      $('#linkModal').modal('show');
    },
    mdInpAux_insertHyperlink: function() {
      $('#linkModal').modal('hide');
      const hyperlink = this.$refs.hyperlink_input.value;

      const start_txt = '[リンク内容](';
      const main_txt = hyperlink;
      const end_txt = ')';

      this._mdInpAuxInsertFocus({
        start_txt,
        main_txt,
        end_txt,
        special: {
          start_range: this.$refs.mdInpPlace.value.length + 1,
          range_target: 'リンク内容',
        }
      });
    },
    mdInpAux_quotedTxt: function() {
      const start_txt = '> ';
      const main_txt = '引用テキスト';
      const end_txt = '';

      this._mdInpAuxInsertFocus({ start_txt, main_txt, end_txt });
    },
    mdInpAux_code: function() {
      const start_txt = '```';
      const main_txt = 'ここに言語を入力';
      const end_txt = "\nコード\n```";

      this._mdInpAuxInsertFocus({ start_txt, main_txt, end_txt });
    },
    mdInpAux_ul: function() {
      const start_txt = '- ';
      const main_txt = 'リスト';
      const end_txt = '';

      this._mdInpAuxInsertFocus({ start_txt, main_txt, end_txt });
    },
    mdInpAux_ol: function() {
      const start_txt = '0. ';
      const main_txt = '番号リスト';
      const end_txt = '';

      this._mdInpAuxInsertFocus({ start_txt, main_txt, end_txt });
    },
    mdInpAux_table: function() {
      this.$refs.mdInpPlace.value += `|列1|列2|列3|\n|:--|:--:|--:|\n||||`;
      this.convert();
    },
    mdInpAux_horizon: function() {
      this.$refs.mdInpPlace.value += `---`;
    },
    mdInpAux_img: function() {
      this.$refs.img_inp.value = null;
      this.$refs.img_inp.click();
    },
    upload: function(e) {
      var formData = new FormData();
      formData.append("image", this.$refs.img_inp.files[0]);
      axios.post('/upload', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        .then(response => {
          console.log(response.data.img_file_name);
          const img_file_name = response.data.img_file_name;
          const start_txt = '![';
          const main_txt = 'イメージ説明';
          const end_txt = `](/q_img/${img_file_name})`;
          this._mdInpAuxInsertFocus({ start_txt, main_txt, end_txt });
        })
        .catch(error => {
          console.log(error);
        });
    }
  }
}
