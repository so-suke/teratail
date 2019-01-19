var mixin_app = {
  data: {
    unread_cnt: null,
    notices: [],
		unread_notice_ids: [],
  },
  mounted: function() {

  },
  methods: {
    mark_as_read: function() {
      //既読にする
			const showed_unread_ids = this.unread_notice_ids.map((notice_id) => {
				return notice_id.id;
			});
      const params = new URLSearchParams();
      params.append('showed_unread_ids', showed_unread_ids);
      axios.post('/teratail/public/ajax_q/mark_as_read', params)
        .then(response => {
					this._get_unread_notice_ids();
        })
        .catch(e => {
          console.log(e)
        })
    },
    submit_logout: function(event) {
      event.preventDefault();
      this.$refs.logout_form.submit();
    },
  }
}
