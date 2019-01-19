@extends('layouts.app')

@section('content')
<div class="container">
  <div class="d-flex flex-column align-items-center">
    <div class="w-75">
      <div>
        <span class="h4">回答を編集する</span>
      </div>

      <form class="d-flex justify-content-between mt-3 w-100" action="{{ route('edit_answer', ['answer_id' => $answer->id]) }}" method="post">
        @csrf
        <img class="myAnswer-userImg ml-2" src="{{ asset('/img/userimg_default.jpg') }}">
        <div class="w-100 ml-2">
          <div class="card">
            <div class="card-header">本文</div>
            <div class="card-body">
              @include('includes.md_inp_aux_btn_grp')
              <template>
                <div class="mt-2 mb-3">
                  <textarea class="form-control" ref="mdInpPlace" @input="convert" name="answer_md_content" placeholder="回答を入力してください&#13;&#10;※Markdown記法が利用できます" rows="3">{{ $answer->md_content }}</textarea>
                </div>
              </template>
              <div class="mb-3 answerInputArea__preview">
                <p class="md-preview" v-html="converted"></p>
              </div>
              <button type="submit" class="btn btn-primary">更新する</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

@include('includes.mdInpAuxDesc')
@endsection

@section('scripts')
@parent
<script src="{{ asset('/js/vmixin_md.js') }}"></script>
<script src="{{ asset('/js/contents/mixin_md_convert.js') }}"></script>
<script src="{{ asset('/js/contents/edit_answer.js') }}"></script>
@endsection
