<div class="d-flex">
  <button type="button" class="btn btn-sm btn-primary mr-2" @click="mdInpAux_bold" @mouseover="showDesc" @mouseleave="hideDesc" data-desc="太字">B</button>
  <button type="button" class="btn btn-sm btn-primary mr-2" @click="mdInpAux_italic" @mouseover="showDesc" @mouseleave="hideDesc" data-desc="斜体">I</button>
  <button type="button" class="btn btn-sm btn-primary mr-2" @click="mdInpAux_del" @mouseover="showDesc" @mouseleave="hideDesc" data-desc="打ち消し線">d</button>
  <button type="button" class="btn btn-sm btn-primary mr-2" @click="mdInpAux_heading" @mouseover="showDesc" @mouseleave="hideDesc" data-desc="見出し">H</button>
  <button type="button" class="btn btn-sm btn-primary mr-2" @click="mdInpAux_showLinkModal" @mouseover="showDesc" @mouseleave="hideDesc" data-desc="リンクの挿入">L</button>
  <button type="button" class="btn btn-sm btn-primary mr-2" @click="mdInpAux_img" @mouseover="showDesc" @mouseleave="hideDesc" data-desc="画像の挿入">Im</button>
  <button type="button" class="btn btn-sm btn-primary mr-2" @click="mdInpAux_quotedTxt" @mouseover="showDesc" @mouseleave="hideDesc" data-desc="引用テキストの挿入">Qt</button>
  <button type="button" class="btn btn-sm btn-primary mr-2" @click="mdInpAux_code" @mouseover="showDesc" @mouseleave="hideDesc" data-desc="コードの挿入">Co</button>
  <button type="button" class="btn btn-sm btn-primary mr-2" @click="mdInpAux_ul" @mouseover="showDesc" @mouseleave="hideDesc" data-desc="リストの挿入">Ul</button>
  <button type="button" class="btn btn-sm btn-primary mr-2" @click="mdInpAux_ol" @mouseover="showDesc" @mouseleave="hideDesc" data-desc="番号リストの挿入">Ol</button>
  <button type="button" class="btn btn-sm btn-primary mr-2" @click="mdInpAux_table" @mouseover="showDesc" @mouseleave="hideDesc" data-desc="表の挿入">Ta</button>
  <button type="button" class="btn btn-sm btn-primary mr-2" @click="mdInpAux_horizon" @mouseover="showDesc" @mouseleave="hideDesc" data-desc="水平線の挿入">Ho</button>
</div>

<form></form>
<form class="d-none" action="{{ route('upload') }}" method="post" enctype="multipart/form-data" id="imgUploadForm">
  @csrf
	<input type="file" class="btn btn-sm btn-primary" ref="img_inp" name="upload_img" @change="upload">
</form>
