<ul class="d-flex flex-column">
  <li class="d-flex py-2 border-bottom border-secondary" v-for="question in questions">
    <div class="d-flex flex-column questionsListLeft">
      <span v-if="parseInt(question.is_resolved) === 1" class="d-block text-danger">解決済</span>
      <span v-else class="d-block">受付中</span>
      <div class="d-flex flex-column">
        <span>回答</span>
        <span>@{{ question.answer_cnt === null ? 0 : question.answer_cnt }}</span>
      </div>
    </div>
    <div class="d-flex flex-column w-100">
      <a :href="'/teratail/public/questions/' + question.id">@{{ question.title }}</a>
      <ul class="d-flex">
        <li class="mr-2 rounded bg-primary p-1" :data-tag-id="tag.id" v-for="tag in question.tags">@{{ tag.name }}</li>
      </ul>
      <div class="d-flex justify-content-between">
        <ul class="d-flex">
          <li>? 評価</li>
          <li class="ml-2">? クリップ</li>
          <li class="ml-2">? pv</li>
        </ul>
        <div class="d-flex align-items-center">
          <img class="user-icon-questions mr-2" src="{{ asset('img/p.png') }}" alt="">
          <span class="mr-3">@{{ question.user_name }}</span>
          <span class="">作成日: @{{ question.created_at_fmt }}</span>
        </div>
      </div>
    </div>
  </li>
</ul>
