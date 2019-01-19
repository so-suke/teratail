<?php

namespace App\Http\Controllers;
use App\Answer;
use App\AnswerRate;
use App\BestAnswer;
use App\MyTag;
use App\Question;
use App\QuestionToTag;
use App\Tag;
use App\User;
use Carbon\Carbon;
use cebe\markdown\GithubMarkdown as Markdown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
use Parsedown;

class ActionController extends Controller {

  public function __construct() {
    $this->middleware('auth');
  }

  public function test() {
    include app_path() . '/variables/score_points.php';
    Log::debug(1);

    $user_id = Auth::id();
    //↓↓**質問がベストアンサー解決済みになっている場合↓↓ */
    $best_answers = BestAnswer::select(DB::raw("bs.question_id, DATE_FORMAT(bs.created_at, '%m/%d') AS date, q.title as q_title"))
      ->from('best_answers as bs')
      ->join(DB::raw("
            (select q.id, q.title
            from questions as q
            where user_id = $user_id) as q
        "), 'bs.question_id', '=', 'q.id')
      ->get();

    $scores = [];
    $action_kind = 'q';
    $action_kind_name = '質問';
    foreach ($best_answers as $key => $best_answer) {
      if (array_key_exists($best_answer->date, $scores) !== true) {
        $scores[$best_answer->date] = ['score' => 0, 'actions' => []];
      }
      if (array_key_exists("q_$best_answer->question_id", $scores[$best_answer->date]['actions']) !== true) {
        $scores[$best_answer->date]['actions']["q_$best_answer->question_id"] = [
          'score' => 0,
          'action_kind' => $action_kind,
          'action_kind_name' => $action_kind_name,
          'title' => $best_answer->q_title,
          'details' => [],
        ];
      }

      $add_score = $score_points['make_q_resolved'];
      $scores[$best_answer->date]['actions']["q_$best_answer->question_id"]['details'][] = [
        'score' => $add_score,
        'msg' => '質問を解決済みにしました',
      ];
      $scores[$best_answer->date]['score'] += $add_score;
      $scores[$best_answer->date]['actions']["q_$best_answer->question_id"]['score'] += $add_score;
    }
    //↑↑**質問がベストアンサー解決済みになっている場合↑↑ */

    //↓↓**回答にされた評価に関しての場合↓↓ */
    $answer_rates = AnswerRate::select(DB::raw("a.question_id, q.title as q_title, a.id as a_id, ar.kind, DATE_FORMAT(ar.updated_at, '%m/%d') AS date"))
      ->from('answer_rates as ar')
      ->join(DB::raw("
            (select id, question_id, user_id
            from answers
            where user_id = $user_id) as a
        "), 'ar.answer_id', '=', 'a.id')
      ->join('questions as q', 'a.question_id', '=', 'q.id')
      ->get();

    $action_kind = 'a';
    $action_kind_name = '回答';

    foreach ($answer_rates as $key => $answer_rate) {
      if (array_key_exists($answer_rate->date, $scores) !== true) {
        $scores[$answer_rate->date] = ['score' => 0, 'actions' => []];
      }
      if (array_key_exists("a_$answer_rate->a_id", $scores[$answer_rate->date]['actions']) !== true) {
        $scores[$answer_rate->date]['actions']["a_$answer_rate->a_id"] = [
          'score' => 0,
          'action_kind' => $action_kind,
          'action_kind_name' => $action_kind_name,
          'title' => $answer_rate->q_title,
          'details' => [],
        ];
      }

      if ($answer_rate->kind === 'high') {
        $add_score = $score_points['answer_high_rate'];
        $msg = '回答に高評価(+)をもらいました';
      } else {
        $add_score = $score_points['answer_low_rate'];
        $msg = '回答に-の評価がつきました';
      }

      $scores[$answer_rate->date]['actions']["a_$answer_rate->a_id"]['details'][] = [
        'score' => $add_score,
        'msg' => $msg,
      ];
      $scores[$answer_rate->date]['score'] += $add_score;
      $scores[$answer_rate->date]['actions']["a_$answer_rate->a_id"]['score'] += $add_score;
    }
    //↑↑**回答にされた評価に関しての場合↑↑ */

    //↓↓**回答がベストアンサーに選ばれた場合↓↓ */
    $best_answers = BestAnswer::select(DB::raw("a.question_id, q.title as 'q_title', a.id as 'a_id', DATE_FORMAT(bs.created_at, '%m/%d') AS date"))
      ->from('best_answers as bs')
      ->join(DB::raw("
            (select id, question_id, user_id
            from answers
            where user_id = $user_id) as a
        "), 'bs.answer_id', '=', 'a.id')
      ->join('questions as q', 'a.question_id', '=', 'q.id')
      ->get();

    $action_kind = 'a';
    $action_kind_name = '回答';

    foreach ($best_answers as $key => $best_answer) {
      if (array_key_exists($best_answer->date, $scores) !== true) {
        $scores[$best_answer->date] = ['score' => 0, 'actions' => []];
      }
      if (array_key_exists("a_$best_answer->a_id", $scores[$best_answer->date]['actions']) !== true) {
        $scores[$best_answer->date]['actions']["a_$best_answer->a_id"] = [
          'score' => 0,
          'action_kind' => $action_kind,
          'action_kind_name' => $action_kind_name,
          'title' => $best_answer->q_title,
          'details' => [],
        ];
      }

      $add_score = $score_points['chosen_best_answer'];
      $msg = 'ベストアンサーに選ばれました';

      $scores[$best_answer->date]['actions']["a_$best_answer->a_id"]['details'][] = [
        'score' => $add_score,
        'msg' => $msg,
      ];
      $scores[$best_answer->date]['score'] += $add_score;
      $scores[$best_answer->date]['actions']["a_$best_answer->a_id"]['score'] += $add_score;
    }
    //↑↑**回答がベストアンサーに選ばれた場合↑↑ */

    return view('contents.user', [
      'scores' => $scores,
    ]);
  }

  //質問入力画面へ遷移させる。
  public function toQuestionInput() {
    return view('contents.q_input', [
    ]);
  }

  //質問編集画面へ遷移させる。
  public function toQuestionEdit(Request $request, $question_id) {
    $question = Question::find($question_id);
    $request->session()->put('edit_question_id', $question->id);
    return view('contents.question_edit', [
      'question' => $question,
    ]);
  }

  //質問編集。
  public function editQuestion(Request $request) {
    $question_id = $request->question_id;
    $question_title = $request->question_title;
    $md_content = $request->md_content;
    $tag_ids = $request->tag_ids;

    $question = Question::find($question_id);
    $question->title = $question_title;
    $question->md_content = $md_content;
    $question->save();

    QuestionToTag::where('question_id', $question_id)->delete();
    foreach ($tag_ids as $key => $tag_id) {
      $question_to_tag = new QuestionToTag;
      $question_to_tag->question_id = $question_id;
      $question_to_tag->tag_id = $tag_id;
      $question_to_tag->save();
    }

    return redirect()->route('questions', ['q_id' => $question_id]);
  }

  //質問編集画面へ遷移させる。
  public function ajaxGetInputedTags(Request $request) {
    $edit_question_id = $request->session()->get('edit_question_id');
    $tags = Tag::select(DB::raw('t.id, t.name'))
      ->from('tags as t')
      ->join('question_to_tags as qtt', function ($join) use ($edit_question_id) {
        $join->on('t.id', '=', 'qtt.tag_id')
          ->where('qtt.question_id', '=', $edit_question_id);
      })
      ->get();
    return response()->json([
      'tags' => $tags,
    ]);
  }

  //質問をDBにインサートする。
  public function insertQuestion(Request $request) {
    //質問の登録と「質問とタグのつながり」の登録。
    $question = new Question;
    $question->user_id = Auth::id();
    $question->is_resolved = false;
    $question->title = $request->question_title;
    $question->md_content = $request->md_content;
    $question->save();

    foreach ($request->tag_ids as $key => $tag_id) {
      $question_to_tag = new QuestionToTag;
      $question_to_tag->question_id = $question->id;
      $question_to_tag->tag_id = $tag_id;
      $question_to_tag->save();
    }

    return redirect()->route('home');
  }

	//質問の絞り込み条件をセッションに登録。
  public function registerQuestionsFilterMode($mode_name, Request $request) {
    $request->session()->put('questions_filter_mode', $mode_name);
    return response()->json([
      'result' => 'success',
    ]);
  }

	//質問のマイタグによる絞り込み条件をセッションに登録。
  public function registerMyTagFilterMode($mode_name, Request $request) {
    $request->session()->put('my_tag_filter_mode', $mode_name);
    return response()->json([
      'result' => 'success',
    ]);
  }

  public function add_to_my_tag(Request $request) {
    $tag_ids = $request->tag_ids;
    $now = Carbon::now()->format('Y-m-d H:i:s');
    $inserts = [];
    //送られてきたタグsをインサートデータとして格納。
    foreach ($tag_ids as $key => $tag_id) {
      $inserts[] = [
        'owner_id' => Auth::id(),
        'tag_id' => $tag_id,
        'created_at' => $now,
        'updated_at' => $now,
      ];
    }

    $is_success = DB::table('my_tags')
      ->insert($inserts);

    return response()->json([
      'result' => 'success',
    ]);
  }

  public function delete_my_tag(Request $request) {
    $tag_id = $request->tag_id;
    MyTag::where('owner_id', Auth::id())
      ->where('tag_id', $tag_id)
      ->delete();

    return response()->json([
      'result' => 'success',
    ]);
  }

  public function make_best_answer($q_id, $a_id) {
    $question = Question::find($q_id);
    $question->is_resolved = true;
    $question->save();

    $best_answer = new BestAnswer;
    $best_answer->question_id = $q_id;
    $best_answer->answer_id = $a_id;
    $best_answer->save();
    return redirect()->action('ActionController@questions', ['q_id' => $q_id]);
  }

  public function get_tags() {
    $tags = Tag::get();
    return response()->json([
      'tags' => $tags,
    ]);
  }

  public function specifyAnswerOrder(Request $request, $question_id, $order_type) {
    $request->session()->put('answers_order_type', $order_type);
    return redirect()->route('questions', ['q_id' => $question_id]);
  }

  public function questions(Request $request, $q_id) {
    //(質問と質問ユーザ)情報取得
    $question = Question::select(DB::raw('q.id, q.user_id, q.is_resolved, q.title, q.md_content,
			q.created_at, q.updated_at, u.name as u_name, u.score as u_score'))
      ->from('questions as q')
      ->join('users as u', 'q.user_id', '=', 'u.id')
      ->where('q.id', $q_id)
      ->first();

    $tags = Tag::select('t.id', 't.name')
      ->from('question_to_tags as qtt')
      ->join('tags as t', function ($join) use ($question) {
        $join->on('qtt.tag_id', '=', 't.id')
          ->where('qtt.question_id', '=', $question->id);
      })
      ->get();

    $Extension = new Extension();
    $converted_q = $Extension
      ->setBreaksEnabled(true)
      ->text($question->md_content);

		//回答の並び順の取得と指定。
    $answers_order_type = $request->session()->get('answers_order_type', '1');
    $answers_orderby_column = '';
    $answers_orderby_kind = '';
    if ($answers_order_type === '1') {
      $answers_orderby_column = 'sum_rates';
      $answers_orderby_kind = 'desc';
    } else if ($answers_order_type === '2') {
      $answers_orderby_column = 'a.created_at';
      $answers_orderby_kind = 'desc';
    } else if ($answers_order_type === '3') {
      $answers_orderby_column = 'a.created_at';
      $answers_orderby_kind = 'asc';
    }
    //回答内容と回答スコア値(高低評価数と合計評価数)とユーザ情報。
    $answers = Answer::select(DB::raw("a.id, a.question_id, a.md_content,
			high.cnt as 'high_cnt',low.cnt as 'low_cnt',
			(high.cnt - low.cnt) as sum_rates,
			u.id as 'u_id', u.name as 'u_name', u.score as 'u_score'"))
      ->from('answers as a')
      ->join(DB::raw("(
				select a.id as answer_id, count(ar.answer_id) as cnt
				from answers as a
				left join answer_rates as ar on a.id = ar.answer_id
				and ar.kind='high'
				where question_id = {$q_id}
				group by a.id, ar.answer_id
			) as high"), 'a.id', '=', 'high.answer_id')
      ->join(DB::raw("(
				select a.id as answer_id, count(ar.answer_id) as cnt
				from answers as a
				left join answer_rates as ar on a.id = ar.answer_id
				and ar.kind='low'
				where question_id = {$q_id}
				group by a.id, ar.answer_id
			) as low"), 'a.id', '=', 'low.answer_id')
      ->join('users as u', 'a.user_id', '=', 'u.id')
      ->orderBy($answers_orderby_column, $answers_orderby_kind)
      ->get();

    foreach ($answers as $key => $answer) {
      $answer->converted_content = $Extension
        ->setBreaksEnabled(true)
        ->text($answer->md_content);
    }

    //該当質問の回答のidsを取得。そのidsに該当する複数の評価を取得。
    $answer_high_rates = AnswerRate::where('kind', 'high')
      ->whereRaw("
				answer_id in (
					select id
					from answers
					where question_id = {$q_id}
				)"
      )
      ->get();

    $answer_low_rates = AnswerRate::where('kind', 'low')
      ->whereRaw("
				answer_id in (
					select id
					from answers
					where question_id = {$q_id}
				)"
      )
      ->get();

    $answer_id_rate_user_ids = [];

    foreach ($answer_high_rates as $key => $answer_high_rate) {
      if (isset($answer_id_rate_user_ids[$answer_high_rate->answer_id]) !== true) {
        $answer_id_rate_user_ids[$answer_high_rate->answer_id]['high'] = [];
        $answer_id_rate_user_ids[$answer_high_rate->answer_id]['low'] = [];
      }
      $answer_id_rate_user_ids[$answer_high_rate->answer_id]['high'][] = $answer_high_rate->user_id;
    }
    foreach ($answer_low_rates as $key => $answer_low_rate) {
      $answer_id_rate_user_ids[$answer_low_rate->answer_id]['low'][] = $answer_low_rate->user_id;
    }

    foreach ($answer_id_rate_user_ids as $answer_id => $rate_user_ids) {
      foreach ($rate_user_ids as $rate => $user_ids) {
        $target = $answers->where('id', $answer_id)->first();
        if ($rate === 'high') {
          $target->high_user_ids = $user_ids;
        } elseif ($rate === 'low') {
          $target->low_user_ids = $user_ids;
        }
      }
    }

    $best_answer = BestAnswer::where('question_id', $q_id)
      ->first();

    $best_answer_id = null;
    if ($best_answer !== null) {
      //ベストアンサーを回答の一番上に持ってくる処理。
      $search_target = $answers->where('id', $best_answer->answer_id)->first();
      $forget_target_key = $answers->search($search_target);
      $target_best_answer = $answers->get($forget_target_key);
      $answers->forget($forget_target_key);
      $answers->prepend($target_best_answer);
      $best_answer_id = $best_answer->answer_id;
    }
    return view('contents.question', [
      'question' => $question,
      'tags' => $tags,
      'answers' => $answers,
      'best_answer_id' => $best_answer_id,
      'converted_q' => $converted_q,
			'answers_order_type' => $answers_order_type,
    ]);
  }

  public function toEditAnswer($answer_id) {
    $answer = Answer::find($answer_id);
    return view('contents.edit_answer', [
      'answer' => $answer,
    ]);
  }

  public function editAnswer(Request $request) {
    $answer = Answer::find($request->answer_id);
    $answer->md_content = $request->answer_md_content;
    $answer->save();

    return redirect()->route('questions', ['q_id' => $answer->question_id]);
  }

  public function insertAnswer($question_id, Request $request) {

    //回答インサート
    $answer = new Answer;
    $answer->question_id = $question_id;
    $answer->user_id = Auth::id();
    $answer->md_content = $request->answer_md;
    $answer->save();

    //回答をした場合、そのユーザのスコアに、設定されている値を加算する。
    $user = Auth::user();
    include app_path() . '/variables/score_points.php';
    $user->score += $score_points['answer'];
    $user->save();

    return redirect()->route('questions', ['q_id' => $question_id]);

  }

  public function insert_rate(Request $request) {
    $answer_id = $request->answer_id;
    $rate_kind = $request->rate_kind;

    $answer_rate = new AnswerRate;
    $answer_rate->user_id = Auth::id();
    $answer_rate->answer_id = $answer_id;
    $answer_rate->kind = $rate_kind;

    $answer_rate->save();

    //スコア値計算・回答ユーザスコアに加算
    include app_path() . '/variables/score_points.php';
    if ($rate_kind === 'high') {
      $add_score = $score_points['answer_high_rate'];
    } else if ($rate_kind === 'low') {
      $add_score = $score_points['answer_low_rate'];
    }

    $answerd_u_id = Answer::find($answer_id)
      ->user_id;

    $user = User::find($answerd_u_id);
    $user->score += $add_score;
    $user->save();

    return response()->json([
      'result' => 'success',
    ]);
  }

  public function update_rate(Request $request) {
    $answer_id = $request->answer_id;
    $from_kind = $request->from_kind;
    $to_kind = $request->to_kind;

    $target_rate = AnswerRate::where('user_id', Auth::id())
      ->where('answer_id', $answer_id)
      ->where('kind', $from_kind)
      ->first();

    $target_rate->kind = $to_kind;
    $target_rate->save();

    include app_path() . '/variables/score_points.php';
    if ($from_kind === 'high' && $to_kind === 'low') {
      $subt_score = $score_points['answer_high_rate'];
      $add_score = $score_points['answer_low_rate'];
    } else if ($from_kind === 'low' && $to_kind === 'high') {
      $subt_score = $score_points['answer_low_rate'];
      $add_score = $score_points['answer_high_rate'];
    }
    $answerd_u_id = Answer::find($answer_id)
      ->user_id;
    $user = User::find($answerd_u_id);
    $user->score -= $subt_score;
    $user->score += $add_score;
    $user->save();

    return response()->json([
      'result' => 'success',
    ]);
  }

  public function remove_rate(Request $request) {
    $answer_id = $request->answer_id;
    $rate_kind = $request->rate_kind;

    AnswerRate::where('user_id', Auth::id())
      ->where('answer_id', $answer_id)
      ->where('kind', $rate_kind)
      ->delete();

    //スコア値計算・回答ユーザスコアに加算
    include app_path() . '/variables/score_points.php';
    if ($rate_kind === 'high') {
      $subt_score = $score_points['answer_high_rate'];
    } else if ($rate_kind === 'low') {
      $subt_score = $score_points['answer_low_rate'];
    }

    $answerd_u_id = Answer::find($answer_id)
      ->user_id;

    $user = User::find($answerd_u_id);
    $user->score -= $subt_score;
    $user->save();

    return response()->json([
      'result' => 'success',
    ]);
  }

}

class Extension extends Parsedown {
  protected function blockFencedCode($excerpt) {
    $language_name = substr($excerpt['body'], 3);
    $code = parent::blockFencedCode($excerpt);
    $code['element']['attributes']['class'] = $code['element']['text']['attributes']['class'];
    $code['element']['text']['attributes']['class'] = 'hljs ' . $code['element']['text']['attributes']['class'];
    $code['element']['text']['attributes']['data-language'] = $language_name;

    return $code;
  }
}

class MyMarkdown extends Markdown {
  protected function consumeFencedCode($lines, $current) {
    // create block array
    $block = [
      'fencedCode',
      'content' => [],
    ];
    $line = rtrim($lines[$current]);

    // detect language and fence length (can be more than 3 backticks)
    $fence = substr($line, 0, $pos = strrpos($line, '`') + 1);
    $language = substr($line, $pos);
    if (!empty($language)) {
      $block['language'] = $language;
    }

    // consume all lines until ```
    for ($i = $current + 1, $count = count($lines); $i < $count; $i++) {
      if (rtrim($line = $lines[$i]) !== $fence) {
        $block['content'][] = $line;
      } else {
        // stop consuming when code block is over
        break;
      }
    }
    return [$block, $i];
  }

  protected function renderFencedCode($block) {
    $pre_class = isset($block['language']) ? ' class="language-' . $block['language'] . '"' : '';
    $code_class = isset($block['language']) ? ' class="hljs language-' . $block['language'] . '" data-language="' . $block['language'] . '"' : '';
    return "<pre$pre_class><code$code_class>" . htmlspecialchars(implode("\n", $block['content']) . "\n", ENT_NOQUOTES, 'UTF-8') . '</code></pre>';
  }
}
