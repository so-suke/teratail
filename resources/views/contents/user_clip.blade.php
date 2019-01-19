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
          <span>クリップ</span>
        </div>
        @include('includes.questions')
      </div>
    </div>

  </div>
</div>
@endsection

@section('scripts')
@parent
<script src="{{ asset('/js/contents/user.js') }}"></script>
@endsection
