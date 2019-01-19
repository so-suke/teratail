@extends('layouts.app')

@section('content')
<div class="container">
  <form action="{{ route('insert_question') }}" method="post">
    @csrf
    <template v-for="tag in inputed_tags">
      <input type="hidden" name="tag_ids[]" :value="tag.id">
    </template>
    <input class="form-control" v-model="title" type="text" name="question_title" placeholder="タイトル：わからないこと、解決したいことを10文字以上で書いてください" required>
    <div class="p-2 border border-secondary">
      <span class="fz-12">タグを入力して候補から選択（"PHP", "Java"など関連する言語やツール 5つまで）</span>
      <div class="d-flex">
        <span class="badge badge-primary align-self-center mr-2 px-2 d-flex align-items-center" v-for="(tag, idx) in inputed_tags">
          @{{ tag.name }}
          <span class="btn btn-primary p-0 ml-2" @click="remove_tag(idx)">x</span>
        </span>

        <div class="position-relative">
          <input class="form-control w-50" v-model="tag_kw" @input="search_candidate_tags" autocomplete="off" type="text" name="" id="">
          <ul class="autocomplete-results position-absolute border border-secondary p-2" v-show="is_open_candidate_tags">
            <li class="autocomplete-result" v-for="(tag, i) in candidate_tags">
              <a class="d-block py-1" href="#" @click="set_result(tag, $event)">@{{ tag.name }}</a>
            </li>
          </ul>
        </div>

      </div>
    </div>
    <div id="content"></div>
    <div class="d-flex flex-column">
      <div class="d-flex">
        <div class="d-flex flex-column">
          @include('includes.md_inp_aux_btn_grp')
          {{-- <form action="/upload" method="post" enctype="multipart/form-data" id="imgUploadForm"></form> --}}

          <textarea class="md-height" name="md_content" ref="mdInpPlace" @input="convert" cols="30" rows="10" placeholder="分からないこと、解決したいことの詳細を30～10000文字で入力してください。" required></textarea>
        </div>
        <div class="border border-secondary w-100">
          <p class="text-muted mb-0">プレビュー</p>
          <p class="md-preview md-height mb-0 p-4" ref="content" v-html="converted"></p>
        </div>
      </div>

      <button class="btn btn-primary col-1" type="submit">質問する</button>
    </div>

  </form>
</div>

<!-- LinkModal -->
<div class="modal fade" id="linkModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">teratail.comの内容</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>insert Hyperlink</p>
        <input type="text" class="form-control" ref="hyperlink_input">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
        <button type="button" class="btn btn-primary" @click="mdInpAux_insertHyperlink">OK</button>
      </div>
    </div>
  </div>
</div>

@include('includes.mdInpAuxDesc')

@endsection

@section('scripts')
@parent
<script src="{{ asset('/js/vmixin_md.js') }}"></script>
<script src="{{ asset('/js/q_input.js') }}"></script>
@endsection
