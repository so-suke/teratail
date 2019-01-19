<div class="d-flex justify-content-end">
  <span class="font-weight-bold mr-2">Myタグによる絞り込み</span>
  <div>
    <span class="mr-2">
      @{{ my_tag.filter_mode === my_tag.FILTER_MODE.NONE ? 'なし':'厳密'}}
    </span>
    <label class="switch">
      <input type="checkbox" @click="toggleMyTagFilterMode" :checked="my_tag.filter_mode === my_tag.FILTER_MODE.STRICT">
      <span class="slider round"></span>
    </label>
  </div>
</div>
