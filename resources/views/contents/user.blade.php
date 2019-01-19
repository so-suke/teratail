@extends('layouts.app')

@section('content')
<div class="container">
  <div class="d-flex">
    <div class="col-3 border d-flex flex-column align-items-center">
      <div class="d-flex flex-column align-items-center">
        <img class="userIcon" src="{{ asset('img/p.png') }}" alt="">
        <span>fjaiofjawiefjaw</span>
        <span>2016/9/8</span>
      </div>
      <div class="d-flex">
        <div class="d-flex flex-column">
          <span>score</span>
          <span>151</span>
          <span>週間0</span>
        </div>
        <div class="d-flex flex-column">
          <span>ランキング</span>
          <span>1037位</span>
          <span>週間 - 位</span>
        </div>
      </div>
      <button class="btn btn-primary">プロフィール編集</button>
    </div>
    <div class="col-9 border">
      <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Profile</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Contact</a>
        </li>
      </ul>
      <div class="d-flex flex-column px-1 py-2">
        <div class="d-flex">
          <span class="d-block">score</span>
          <span>※score合計値への反映には数分かかる場合があります</span>
        </div>
        <ul class="d-flex flex-column">
          @foreach ($scores as $date_key => $score_item)
          <li class="mb-2">
            <p class="m-0" @click="openBelowList">
              <span class="notouch">{{ $score_item['score'] }}</span>
              <span class="ml-5 notouch">{{ $date_key }}</span>
            </p>
            <ul class="ml-4 d-none">
              @foreach ($score_item['actions'] as $q_id => $action)
              <li>
                <p class="m-0 d-flex" @click="openBelowList">
                  <span class="notouch">{{ ($action['score']) }}</span>
                  <template class="d-flex">
                    <span class="ml-5 notouch mr-2">{{ $action['action_kind_name'] }}</span>
                    <a href="{{ route('questions', ['q_id' => $q_id]) }}">{{ $action['title'] }}</a>
                  </template>
                </p>
                <ul class="ml-5 d-none">
                  @foreach ($action['details'] as $detaile)
                  <li>
                    <span>{{ $detaile['score'] }}</span>
                    <span class="ml-5">{{ $detaile['msg'] }}</span>
                  </li>
                  @endforeach
                </ul>
              </li>
              @endforeach
            </ul>
          </li>
          @endforeach
        </ul>
      </div>
    </div>

  </div>
</div>
@endsection

@section('scripts')
@parent
<script src="{{ asset('/js/contents/user.js') }}"></script>
@endsection
