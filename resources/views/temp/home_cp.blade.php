@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-9">
      <ul class="nav">
        <li class="nav-item">
          <a class="nav-link active" href="#">Active</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Link</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Link</a>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled" href="#">Disabled</a>
        </li>
      </ul>

      <ul class="d-flex flex-column">
        @foreach ($questions as $question)
        <li class="d-flex py-2 border-bottom border-secondary">
          <div class="d-flex flex-column">
            <span class="d-block">受付中</span>
            <div class="d-flex flex-column">
              <span>回答</span>
              <span>0</span>
            </div>
          </div>
          <div class="d-flex flex-column w-75">
            <a href="{{ route('questions', ['q_id' => $question->id]) }}">{{ $question->title }}</a>
            <ul class="d-flex">
              @foreach ($question->tags as $tag)
              <li class="mr-2 rounded bg-primary p-1" data-tag-id="{{ $tag['id'] }}">{{ $tag['name'] }}</li>
              @endforeach
            </ul>
            <div class="d-flex justify-content-between">
              <ul class="d-flex">
                <li>0 評価</li>
                <li>0 クリップ</li>
                <li>0 pv</li>
              </ul>
              <div class="d-flex align-items-center">
                <img class="user-icon-questions mr-2" src="{{ asset('img/p.png') }}" alt="">
                <span class="mr-2">samplename</span>
                <span>3分前</span>
              </div>
            </div>
          </div>
        </li>
        @endforeach
      </ul>
    </div>

    <div class="col-md-3">
      <div class="d-flex justify-content-center border mb-3">
        <div class="d-flex flex-column align-items-center border-right p-2 px-3">
          <span>score</span>
          <span>151</span>
          <span>週間 0</span>
        </div>
        <div class="d-flex flex-column align-items-center p-2 px-3">
          <span>ランキング</span>
          <span>1034位</span>
          <span>前日比</span>
        </div>
      </div>

      <div class="d-flex flex-column">
        <div class="d-flex justify-content-between py-1 border-bottom mb-2">
          <span>Myタグ</span>
          @if ($my_tags->count() > 0)
          <button class="btn btn-sm btn-primary">編集</button>
          @endif
        </div>
        <div>
          <div class="d-flex flex-wrap mb-2">
            @foreach ($my_tags as $tag)
            <li class="badge badge-primary align-self-center mr-2 mb-2 p-2 d-flex align-items-center">
              <a class="text-light" href="{{ route('tags', ['lang_name' => $tag->name]) }}">
                {{ $tag->name }}
              </a>
            </li>
            @endforeach
          </div>
          <div class="p-2 text-light bg-success rounded mb-2">
            <span class="d-block font-weight-bold fz-13">あなたのフィードを最適化します</span>
            <span class="d-block fz-12">Myタグを登録すると、興味のある情報に絞り込んで効率的に質問を探すことができます</span>
          </div>
          <form ref="myTagForm" action="{{ route('add_to_my_tag') }}" method="POST">
            @csrf
            <input type="hidden" name="tag_ids[]" v-bind:value="tag.id" v-for="tag in inputed_tags">

            <div class="border border-secondary p-2 mb-2" v-show="inputed_tags.length > 0">
              <ul class="d-flex flex-wrap">
                <li class="badge badge-primary align-self-center mr-2 mb-2 px-2 d-flex align-items-center" v-for="(tag, idx) in inputed_tags">
                  @{{ tag.name }}
                  <span class="btn btn-primary p-0 ml-2" @click="remove_tag(idx)">x</span>
                </li>
              </ul>
              <button class="btn btn-primary" type="button" @click="addToMyTag">このタグをMyタグに追加</button>
            </div>
            <div class="position-relative">
              <input class="form-control w-50" v-model="tag_kw" @input="search_candidate_tags" autocomplete="off" type="text">
              <ul class="autocomplete-results position-absolute border border-secondary p-2" v-show="is_open_candidate_tags">
                <li class="autocomplete-result" v-for="(tag, i) in candidate_tags">
                  <a class="d-block py-1" href="#" @click="set_result(tag, $event)">@{{ tag.name }}</a>
                </li>
              </ul>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
@parent
<script src="{{ asset('/js/q_input.js') }}"></script>
@endsection
