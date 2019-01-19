<?php

namespace App\Http\Controllers;

use App\MyTag;
use App\Question;
use App\QuestionToTag;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

// use Illuminate\Http\Request;

class HomeController extends Controller {

  const QUESTION_FILTER_MODE = [
    'CREATED_DESC' => 'created_desc',
    'UNANSWERED' => 'unanswered',
    'UNRESOLVED' => 'unresolved',
    'RESOLVED' => 'resolved',
  ];
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct() {
    $this->middleware('auth');
  }

  public function index() {
    $user = Auth::user();
    return view('home', [
      'user_score' => $user->score,
    ]);
  }

  public function saveSearchKW(Request $request) {
    $user = Auth::user();
    $request->session()->put('questions_searched_kw', $request->q);
    return view('contents.home_by_searched_kw', [
      'user_score' => $user->score,
      'searched_kw' => $request->q,
    ]);
  }

  public function ajaxGetClipQuestions(Request $request) {
    $questions = Question::from("questions as q")
      ->join(DB::raw("
            (select question_id
            from clips
            where user_id = 1) as c
        "), 'q.id', '=', 'c.question_id')
      ->get();

    //＊＊以下、質問にタグ配列を持たせる処理。＊＊
    $q_ids = []; //質問id配列, sqlのin句絞り込みに使用。
    foreach ($questions as $key => $question) {
      $q_ids[] = $question->id;
    }

    // タグ情報を取得。
    $qtt_list = QuestionToTag::select('qtt.question_id', 'qtt.tag_id', "t.name as tag_name")
      ->from('question_to_tags as qtt')
      ->join('tags as t', function ($join) use ($q_ids) {
        $join->on('qtt.tag_id', '=', 't.id')
          ->whereIn('qtt.question_id', $q_ids);
      })
      ->get();

    // 質問idをキーとするタグ情報(タグid, タグname)配列を作成。
    $q_id_tags = [];
    foreach ($qtt_list as $qtt) {
      $q_id_tags[$qtt->question_id][] = [
        'id' => $qtt->tag_id,
        'name' => $qtt->tag_name,
      ];
    }

    foreach ($questions as $question) {
      $question->tags = $q_id_tags[$question->id];
    }

    //＊＊以上、質問にタグ配列を持たせる処理。＊＊

    return response()->json([
      'questions' => $questions,
    ]);
  }

  //ホーム画面の質問取得(検索キーワード考慮)
  public function ajaxGetQuestionsBySearchedKW(Request $request) {
    $filter_mode = $request->session()->get('questions_filter_mode', $this::QUESTION_FILTER_MODE['CREATED_DESC']);
    $searched_kw = $request->session()->get('questions_searched_kw', '');
    $user_id = Auth::id();

    //(未回答)抽出の場合
    if ($filter_mode === $this::QUESTION_FILTER_MODE['UNANSWERED']) {
      $q_builder_final = DB::table(DB::raw('(
				select q.id, q.user_id, q.is_resolved, q.title, q.md_content, q.created_at, DATE_FORMAT(q.created_at, "%Y-%m-%d") as created_at_fmt, (
					select count(question_id)
					from answers
					where question_id = q.id
					group by question_id
				) as answer_cnt
				from questions as q
			) as q2'))
        ->select(DB::raw('q2.id, q2.user_id, u.name as u_name, q2.is_resolved, q2.title, q2.md_content, q2.created_at, q2.created_at_fmt, q2.answer_cnt'))
        ->join('users as u', function ($join) use ($searched_kw) {
          $join->on('q2.user_id', '=', 'u.id')
            ->whereNull('q2.answer_cnt')
            ->where('q2.title', 'like', "%$searched_kw%");
        });
    } else {
      $q_builder0 = Question::from('questions as q')
        ->select(DB::raw('q.id, q.user_id, u.name as user_name, q.is_resolved, q.title, q.md_content, q.created_at, DATE_FORMAT(q.created_at, "%Y-%m-%d") as created_at_fmt, (
					select count(question_id)
					from answers
					where question_id = q.id
					group by question_id
				) as answer_cnt'));

      if ($filter_mode === $this::QUESTION_FILTER_MODE['UNRESOLVED'] || $filter_mode === $this::QUESTION_FILTER_MODE['RESOLVED']) {
        $is_resolved = $filter_mode === $this::QUESTION_FILTER_MODE['RESOLVED'] ? 1 : 0;
        $q_builder_final = $q_builder0
          ->join('users as u', function ($join) use ($is_resolved, $searched_kw) {
            $join->on('q.user_id', '=', 'u.id')
              ->where('q.is_resolved', $is_resolved)
              ->where('q.title', 'like', "%$searched_kw%");
          });
      } else {
        //フィルターモード: 新着(通常)
        Log::debug('come');
        $q_builder_final = $q_builder0
          ->join('users as u', function ($join) use ($searched_kw) {
            $join->on('q.user_id', '=', 'u.id')
              ->where('q.title', 'like', "%$searched_kw%");
          });
      }
    }

    $questions = $q_builder_final
      ->orderBy('created_at', 'desc')
      ->paginate(3);

    //＊＊以下、質問にタグ配列を持たせる処理。＊＊
    $q_ids = []; //質問id配列, sqlのin句絞り込みに使用。
    foreach ($questions as $key => $question) {
      $q_ids[] = $question->id;
    }

    // タグ情報を取得。
    $qtt_list = QuestionToTag::select('qtt.question_id', 'qtt.tag_id', "t.name as tag_name")
      ->from('question_to_tags as qtt')
      ->join('tags as t', function ($join) use ($q_ids) {
        $join->on('qtt.tag_id', '=', 't.id')
          ->whereIn('qtt.question_id', $q_ids);
      })
      ->get();

    // 質問idをキーとするタグ情報(タグid, タグname)配列を作成。
    $q_id_tags = [];
    foreach ($qtt_list as $qtt) {
      $q_id_tags[$qtt->question_id][] = [
        'id' => $qtt->tag_id,
        'name' => $qtt->tag_name,
      ];
    }

    foreach ($questions as $question) {
      //質問にタグが登録されていない場合は、空配列(タグなしの意味)
      if (array_key_exists($question->id, $q_id_tags) === true) {
        $question->tags = $q_id_tags[$question->id];
      } else {
        $question->tags = [];
      }
    }

    //＊＊以上、質問にタグ配列を持たせる処理。＊＊

    return response()->json([
      'questions' => $questions,
      'q_filter_mode' => $filter_mode,
    ]);
  }

  //ホーム画面の質問取得
  public function ajaxGetQuestions(Request $request) {
    $filter_mode = $request->session()->get('questions_filter_mode', $this::QUESTION_FILTER_MODE['CREATED_DESC']);
		$my_tag_filter_mode = $request->session()->get('my_tag_filter_mode', 'none');
		$user_id = Auth::id();
    if ($filter_mode === $this::QUESTION_FILTER_MODE['UNANSWERED']) {
      $q_builder_final = DB::table(DB::raw('(
					select q.id, q.user_id, q.is_resolved, q.title, q.md_content, q.created_at, DATE_FORMAT(q.created_at, "%Y-%m-%d") as created_at_fmt, (
						select count(question_id)
						from answers
						where question_id = q.id
						group by question_id
					) as answer_cnt
					from questions as q
				) as q2'))
        ->select(DB::raw('q2.id, q2.user_id, u.name as u_name, q2.is_resolved, q2.title, q2.md_content, q2.created_at, q2.created_at_fmt, q2.answer_cnt'))
        ->join('users as u', function ($join) {
          $join->on('q2.user_id', '=', 'u.id')
            ->whereNull('q2.answer_cnt');
        });
    } else {
      $q_builder0 = Question::from('questions as q')
        ->select(DB::raw('q.id, q.user_id, u.name as user_name, q.is_resolved, q.title, q.md_content, q.created_at, DATE_FORMAT(q.created_at, "%Y-%m-%d") as created_at_fmt, (
					select count(question_id)
					from answers
					where question_id = q.id
					group by question_id
				) as answer_cnt'));

      //質問フィルター: 未解決、解決済の場合。
      if ($filter_mode === $this::QUESTION_FILTER_MODE['UNRESOLVED'] || $filter_mode === $this::QUESTION_FILTER_MODE['RESOLVED']) {
        $is_resolved = $filter_mode === $this::QUESTION_FILTER_MODE['RESOLVED'] ? 1 : 0;
        $q_builder_final = $q_builder0
          ->join('users as u', function ($join) use ($is_resolved) {
            $join->on('q.user_id', '=', 'u.id')
              ->where('q.is_resolved', $is_resolved);
          });
      } else {
        //フィルターモード: 新着(通常)
        $q_builder_final = $q_builder0
          ->join('users as u', 'q.user_id', '=', 'u.id');
      }
    }

    //Myタグによる質問絞り込み考慮。
    if ($my_tag_filter_mode === 'strict') {
      $q_builder_complete = $q_builder_final
        ->whereRaw("q.id in (
          select question_id
          from question_to_tags
          where tag_id in (
            select tag_id
            from my_tags
            where owner_id = ${user_id}
          )
        )");
    } else {
			$q_builder_complete = $q_builder_final;
		}

    //共通処理：最終的に作成日時で降順。
    $questions = $q_builder_complete
      ->orderBy('created_at', 'desc')
      ->paginate(3);

    //＊＊以下、質問にタグ配列を持たせる処理。＊＊
    $q_ids = []; //質問id配列, sqlのin句絞り込みに使用。
    foreach ($questions as $key => $question) {
      $q_ids[] = $question->id;
    }

    // タグ情報を取得。
    $qtt_list = QuestionToTag::select('qtt.question_id', 'qtt.tag_id', "t.name as tag_name")
      ->from('question_to_tags as qtt')
      ->join('tags as t', function ($join) use ($q_ids) {
        $join->on('qtt.tag_id', '=', 't.id')
          ->whereIn('qtt.question_id', $q_ids);
      })
      ->get();

    // 質問idをキーとするタグ情報(タグid, タグname)配列を作成。
    $q_id_tags = [];
    foreach ($qtt_list as $qtt) {
      $q_id_tags[$qtt->question_id][] = [
        'id' => $qtt->tag_id,
        'name' => $qtt->tag_name,
      ];
    }

    foreach ($questions as $question) {
      //質問にタグが登録されていない場合は、空配列(タグなしの意味)
      if (array_key_exists($question->id, $q_id_tags) === true) {
        $question->tags = $q_id_tags[$question->id];
      } else {
        $question->tags = [];
      }
    }

    //＊＊以上、質問にタグ配列を持たせる処理。＊＊

    return response()->json([
      'questions' => $questions,
      'q_filter_mode' => $filter_mode,
    ]);
  }

  public function ajax_get_my_tags(Request $request) {
    $my_tag_filter_mode = $request->session()->get('my_tag_filter_mode', 'none');

    //loginユーザのmytagを取得
    $my_tags = MyTag::where('owner_id', Auth::id())
      ->join('tags', 'my_tags.tag_id', '=', 'tags.id')
      ->get();

    // dd($questions);
    return response()->json([
      'my_tags' => $my_tags,
      'my_tag_filter_mode' => $my_tag_filter_mode,
    ]);
  }
}
