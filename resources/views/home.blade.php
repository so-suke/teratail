@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-9">
      @include('includes.home.filter_by_mytag')
      {{-- xserverだとエラーでるかも --}}
      <ul class="nav nav-tabs">
        <li class="nav-item">
          <a class="nav-link" v-on:click="toggleQuestionsFilterMode" v-bind:class="{ active: now_question_filter_mode === QUESTION_FILTER_MODE.CREATED_DESC }" href="#" :data-question_filter_mode="QUESTION_FILTER_MODE.CREATED_DESC">新着</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" v-on:click="toggleQuestionsFilterMode" v-bind:class="{ active: now_question_filter_mode === QUESTION_FILTER_MODE.UNANSWERED }" href="#" :data-question_filter_mode="QUESTION_FILTER_MODE.UNANSWERED">未回答</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" v-on:click="toggleQuestionsFilterMode" v-bind:class="{ active: now_question_filter_mode === QUESTION_FILTER_MODE.UNRESOLVED }" href="#" :data-question_filter_mode="QUESTION_FILTER_MODE.UNRESOLVED">未解決</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" v-on:click="toggleQuestionsFilterMode" v-bind:class="{ active: now_question_filter_mode === QUESTION_FILTER_MODE.RESOLVED }" href="#" :data-question_filter_mode="QUESTION_FILTER_MODE.RESOLVED">解決済</a>
        </li>
      </ul>

      @include('includes.questions')
    </div>

    <div class="col-md-3">
      <div class="d-flex justify-content-center border mb-3">
        <div class="d-flex flex-column align-items-center border-right p-2 px-3">
          <span>score</span>
          <span>{{ $user_score }}</span>
          <span>週間 ?</span>
        </div>
        <div class="d-flex flex-column align-items-center p-2 px-3">
          <span>ランキング</span>
          <span>????位</span>
          <span>前日比</span>
        </div>
      </div>

      <div class="d-flex flex-column">
        <div class="d-flex justify-content-between py-1 border-bottom mb-2">
          <span>Myタグ</span>
          <button class="btn btn-sm btn-primary" v-show="(my_tags.length > 0) && my_tag_mode === MY_TAG_MODE.COMMON" @click="toEditMyTag">編集</button>
          <button class="btn btn-sm btn-primary" v-show="(my_tags.length > 0) && my_tag_mode === MY_TAG_MODE.EDITING" @click="endEditMyTag">完了</button>
        </div>
        <div>
          <div class="d-flex flex-wrap mb-2" v-show="my_tags.length > 0">
            <li class="mytag_badge badge badge-primary align-self-center mr-2 mb-2 p-1 px-2 d-flex align-items-center" v-for="(tag, idx) in my_tags">
              <a class="text-light" :href="'/teratail/public/tags/' + tag.name" @click="clicked_mytag(idx, $event)">
                @{{ tag.name }}
                <span class="btn btn-sm btn-primary p-0 ml-2" @click="delete_my_tag(idx)" v-if="my_tag_mode === MY_TAG_MODE.EDITING">x</span>
              </a>
            </li>
          </div>

          <div class="p-2 text-light bg-success rounded mb-2" v-show="my_tags.length === 0">
            <span class="d-block font-weight-bold fz-13">あなたのフィードを最適化します</span>
            <span class="d-block fz-12">Myタグを登録すると、興味のある情報に絞り込んで効率的に質問を探すことができます</span>
          </div>

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

        </div>
      </div>
    </div>

    <ul class="pagination" v-if="paginator !== null && paginator.last_page > 1">
      <li class="page-item" v-bind:class="{ disabled: paginator.current_page == 1 }">
        <a class="page-link" @click.prevent="changePage(1)">First Page</a>
      </li>
      <li class="page-item" v-bind:class="{ disabled: paginator.current_page == 1 }">
        <a class="page-link" @click.prevent="changePage(1)">
          <span aria-hidden="true">«</span>
          {{-- Previous --}}
        </a>
      </li>
      <template v-for="page_num in paginator.last_page">
        <li class="page-item" v-bind:class="{ active: paginator.current_page == page_num }">
          <a class="page-link" @click.prevent="changePage(page_num)">@{{ page_num }}</a>
        </li>
      </template>
      <li class="page-item" v-bind:class="{ disabled: paginator.current_page == paginator.last_page }">
        <a class="page-link" @click.prevent="changePage(paginator.current_page + 1)">
          <span aria-hidden="true">»</span>
          {{-- Next --}}
        </a>
      </li>
      <li class="page-item" v-bind:class="{ disabled: paginator.current_page == paginator.last_page }">
        <a class="page-link" @click.prevent="changePage(paginator.last_page)">Last Page</a>
      </li>
    </ul>

  </div>
</div>
@endsection

@section('scripts')
@parent
<script src="{{ asset('/js/home.js') }}"></script>
@endsection
