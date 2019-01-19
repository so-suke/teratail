@extends('layouts.app')

@section('content')
<div class="questionHeaderTagsWrap border-bottom">
  <ul class="py-3 questionHeaderTags">
    @foreach ($tags as $tag)
    <li class="badge badge-primary p-2">{{ $tag->name }}</li>
    @endforeach
  </ul>
</div>
<div class="questionHeader py-5">
  <div class="d-flex justify-content-between questionHeader-position">
    <div class="d-flex flex-column">
      <span class="h3 font-weight-bold">{{ $question->title }}</span>
      <div class="d-flex align-items-end">
        <span class="mr-2">回答 {{ $answers->count() }}</span>
        <span class="fz-075rem">投稿 2018/11/18 01:11</span>
      </div>
    </div>
    <div class="d-flex flex-column">
      <ul class="d-flex bg-light p-1">
        <li class="px-3 border-right">
          評価<span>0</span>
        </li>
        <li class="px-3 border-right">
          クリップ<span>0</span>
        </li>
        <li class="px-3">
          VIEW<span>28</span>
        </li>
      </ul>
      <div class="d-flex align-items-end mt-2">
        <div class="mr-2">
          <img src="{{ asset('/img/userimg_default.jpg') }}" class="questionHeader-userImg rounded" alt="">
        </div>
        <span class="mr-2 font-weight-bold">{{ $question->u_name }}</span>
        <span>score: {{ $question->u_score }}</span>
      </div>
    </div>
  </div>
</div>
<div class="container">

  <div class="d-flex justify-content-between mt-3">
    <div class="w-75 mr-3">
      <div class="d-flex flex-column mb-3">
        <div id="markedContent" class="question-box mb-3 p-2">
          {!! $converted_q !!}
        </div>
        <div class="d-flex border-bottom pb-2 mb-2">
          @if ((int)$question->user_id !== Auth::id())
          <button class="btn btn-primary mr-2">クリップ</button>
          {{-- 後で作ります --}}
          {{-- <button class="btn btn-primary disabled">高評価</button> --}}
          @else
          <a href="{{ route('to_question_edit', ['question_id' => $question->id]) }}" class="btn btn-primary">編集</a>
          @endif
        </div>
        <div class="py-3 px-2 ml-5 border d-none">
          <span class="btn btn-primary d-none">
            {{-- @{{ rewrite_requests.length }}件の質問への追記・修正依頼 --}}
            2件の質問への追記・修正依頼
          </span>
          <div class="d-none">
            <span class="text-muted mb-2 d-block">質問への追記・修正、ベストアンサー選択の依頼</span>
            <ul class="d-flex flex-column">
              <li class="d-flex p-1" v-for="rewrite_request in rewrite_requests">
                <img src="{{ asset('/img/userimg_default.jpg') }}" class="rewriteRequest-userImg" alt="">
                <div class="d-flex flex-column ml-2 w-50">
                  <div class="d-flex">
                    <p class="mb-0 mr-2">rewrite_request.name</p>
                    {{-- <p class="mb-0 mr-2">@{{ rewrite_request.name }}</p> --}}
                    <p class="mb-0">2018/11/11 04:05</p>
                  </div>
                  <p class="p-0 fz-12">
                    rewrite_request.text
                    {{-- @{{ rewrite_request.text }} --}}
                  </p>
                  {{-- <p class="p-0 fz-12">何が問題・課題でしょうか。ゴールが明確になっていないと回答は得られにくいと思います。「自信がもてない」だけではどこまでを想定されているか分かりませんし、「合っているか」を聞きたいのか「最適か（この最適というのも曖昧な表現なので具体的に）」なのか。「これで大丈夫なのは分かっている」状態なのかどうなのかも記載してください。</p> --}}
                </div>
              </li>
            </ul>
            <div class="d-flex p-1">
              <img src="{{ asset('/img/userimg_default.jpg') }}" class="rewriteRequest-userImg" alt="">
              <div class="ml-2 w-100">
                <input ref="question_id" type="hidden" name="question_id" value="{{ $question->id }}">
                <input class="form-control fz-12" ref="rewrite_request_add_input" type="text" name="rewrite_request_add_text" placeholder="回答に追加の情報が必要な場合は記入してください">
                <div class="d-flex align-items-center justify-content-between mt-2">
                  <a href="#">質問に不備がある場合、質問を編集しましょう</a>
                  <button class="btn btn-primary" @click="insert_rewrite_request">投稿する</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        {{-- @endif --}}
      </div>

      @if ($answers->isNotEmpty())
      <div class="d-flex flex-column">
        <div class="d-flex justify-content-between">
          <p class="m-0">
            <span>回答</span>
            <span>{{ $answers->count() }}</span>
            <span>件</span>
          </p>

          <ul class="nav nav-tabs">
            <li class="nav-item">
              <a class="nav-link {{ $answers_order_type === '1' ? 'active':'' }}" href="{{ route('specify_answer_order', ['question_id' => $question->id, 'order_type' => '1']) }}" role="tab" aria-controls="home" aria-selected="true">評価が高い順</a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ $answers_order_type === '2' ? 'active':'' }}" href="{{ route('specify_answer_order', ['question_id' => $question->id, 'order_type' => '2']) }}" role="tab" aria-controls="profile" aria-selected="false">新着順</a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ $answers_order_type === '3' ? 'active':'' }}" href="{{ route('specify_answer_order', ['question_id' => $question->id, 'order_type' => '3']) }}" role="tab" aria-controls="contact" aria-selected="false">古い順</a>
            </li>
          </ul>
        </div>
      </div>
      @endif

      <ul>
        @foreach ($answers as $a_key => $answer)
        <li class="border-bottom border-secondary">
          <form action="{{ route('make_best_answer', ['q_id' => $question->id, 'a_id' => $answer->id]) }}" method="POST">
            @csrf
            @if ((int)$answer->id === (int)$best_answer_id)
            <span class="text-danger font-weight-bold">ベストアンサー</span>
            @endif
            <div class="d-flex answer-box mt-3">
              <div class="bg-info p-2 px-3 mr-3 align-self-start" id="jsSumRates_{{ $a_key }}">{{ $answer->sum_rates }}</div>
              <div class="w-100">
                <div class="d-flex flex-column w-100 mr-3 p-2">
                  <span class="md-preview">{!! $answer->converted_content !!}</span>
                </div>
                <div class="d-flex justify-content-end mb-2">
                  <p class="mr-2">
                    投稿
                    <time datetime="2018-11-18T01:17" itemprop="dateCreated">2018/11/18 01:17</time>
                  </p>
                  <div class="d-flex">
                    <div class="img-box">
                      <img src="{{ asset('/img/userimg_default.jpg') }}" class="w-100" alt="">
                    </div>
                    <div>
                      <p class="mb-0 fz-small">{{ $answer->u_name }}</p>
                      <p class="mb-0 fz-small">score {{ $answer->u_score }}</p>
                    </div>
                  </div>
                </div>
                <div class="d-flex justify-content-between mt-3 mb-2">
                  <ul class="d-flex align-items-center">
                    @if ((int)$answer->u_id === Auth::id())
                    <li class="btn btn-primary mr-2">
                      <a href="{{ route('to_edit_answer', ['answer_id' => $answer->id]) }}" class="text-light">編集</a>
                    </li>
                    @else

                    @php
                    $is_inArr_high = isset($answer->high_user_ids) && in_array(Auth::id(), $answer->high_user_ids, true) ? true : false;
                    $is_inArr_low = isset($answer->low_user_ids) && in_array(Auth::id(), $answer->low_user_ids, true) ? true : false;
                    $btn_class_high = $is_inArr_high ? 'btn-danger' : 'btn-primary';
                    $btn_class_low = $is_inArr_low ? 'btn-danger' : 'btn-primary';
                    @endphp

                    <li class="btn {{ $btn_class_high }} mr-2" data-answer-idx="{{ $a_key }}" @click="evaluate_rate({{ $answer->id }}, 'high', $event)">
                      + 高評価
                      <span class="js-rate_cnt nonClick">{{ $answer->high_cnt }}</span>
                    </li>


                    <li class="btn {{ $btn_class_low }} mr-2" data-answer-idx="{{ $a_key }}" @click="evaluate_rate({{ $answer->id }}, 'low', $event)">
                      - 低評価
                      <span class="js-rate_cnt nonClick">{{ $answer->low_cnt }}</span>
                    </li>

                    <li class="btn btn-primary mr-2">コメント投稿</li>
                    @if ((int)$question->user_id === Auth::id() && !$question->is_resolved)
                    <li>
                      <button class="btn btn-danger" type="submit">ベストアンサーにする</button>
                    </li>
                    @endif
                    @endif
                  </ul>
                  <ul class="d-flex align-items-center">
                    @if ((int)$answer->u_id === Auth::id())
                    <li class="mr-2">
                      <span>高評価({{ $answer->high_cnt }})</span>
                    </li>
                    <li>
                      <span>低評価({{ $answer->low_cnt }})</span>
                    </li>
                    @else
                    <li class="btn btn-danger disabled">通報</li>
                    @endif
                  </ul>
                </div>
              </div>
            </div>
          </form>
        </li>
        @endforeach
      </ul>

      <div class="myAnswer-wrapper d-flex flex-column align-items-center">
        @if ($answers->isEmpty() === true)
        <div class="border border-secondary rounded w-100 text-center emptyReplyBox">
          <span>まだ回答がついていません</span>
        </div>
        @endif
        {{-- ログインユーザの回答フォーム --}}
        @if ((int)$question->user_id !== Auth::id())
        <form class="d-flex justify-content-between mt-3 w-100" action="{{ route('insert_answer', ['question_id' => $question->id]) }}" method="post">
          @csrf
          <img class="myAnswer-userImg ml-2" src="{{ asset('/img/userimg_default.jpg') }}">
          <div class="w-100 ml-2">
            <div class="card">
              <div class="card-header">
                あなたの回答
              </div>
              <div class="card-body">
                @include('includes.md_inp_aux_btn_grp')
                <template>
                  <div class="mt-2 mb-3">
                    <textarea class="form-control" ref="mdInpPlace" @input="convert" name="answer_md" placeholder="回答を入力してください&#13;&#10;※Markdown記法が利用できます" rows="3"></textarea>
                  </div>
                </template>
                <div class="mb-3 answerInputArea__preview">
                  <p class="md-preview" v-html="converted"></p>
                </div>
                <button type="submit" class="btn btn-primary">回答する</button>
              </div>
            </div>
          </div>
        </form>
        @endif

      </div>
    </div>

    {{-- ページ右側コンテンツです。後で作ります。 --}}
    <div class="border w-25">
    </div>
  </div>
</div>

@include('includes.mdInpAuxDesc')

@endsection

@section('scripts')
@parent
<script src="{{ asset('/js/vmixin_md.js') }}"></script>
<script src="{{ asset('/js/contents/mixin_md_convert.js') }}"></script>
<script src="{{ asset('/js/question.js') }}"></script>
@endsection
