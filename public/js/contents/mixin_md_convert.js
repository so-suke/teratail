var mdConvertMixin = {
  data: {
    converted: '',
  },
  methods: {
    convert: function() {
      this.converted = marked(this.$refs.mdInpPlace.value, { renderer: renderer });
    },
  }
}
