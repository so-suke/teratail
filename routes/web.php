<?php

Route::get('/', function () {
  return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/home/search/', 'HomeController@saveSearchKW')->name('questions_search_by_kw');

Route::get('/questions/{q_id}', 'ActionController@questions')->name('questions');
Route::get('/question_input', 'ActionController@toQuestionInput')->name('question_input');

//質問投稿
Route::post('/insert_question', 'ActionController@insertQuestion')->name('insert_question');
//質問編集
Route::post('/edit_question', 'ActionController@editQuestion')->name('edit_question');
//質問編集画面へ遷移させる。
Route::get('/to_question_edit/{question_id}', 'ActionController@toQuestionEdit')->name('to_question_edit');
//回答編集
Route::post('/edit_answer', 'ActionController@editAnswer')->name('edit_answer');
//回答編集画面へ遷移させる。
Route::get('/to_edit_answer/{answer_id}', 'ActionController@toEditAnswer')->name('to_edit_answer');
//回答の並び順を指定する。
Route::get('/specify_answer_order/{question_id}/{order_type}', 'ActionController@specifyAnswerOrder')->name('specify_answer_order');

Route::get('/q_img/{img_name}', 'ImagesController@q_img');
Route::get('/tags/{lang_name}', 'ActionController@tags')->name('tags');

Route::post('/upload', 'ImagesController@upload')->name('upload');

Route::post('/add_to_my_tag', 'ActionController@add_to_my_tag')
  ->name('add_to_my_tag');

//回答投稿
Route::post('/questions/insert_answer/{question_id}', 'ActionController@insertAnswer')
  ->name('insert_answer');

Route::post('/questions/make_best_answer/{q_id}/{a_id}', 'ActionController@make_best_answer')
  ->name('make_best_answer');

Route::post('/ajax_q/get_answers', 'ActionController@get_answers');
Route::post('/ajax_q/insert_rate/', 'ActionController@insert_rate');
Route::post('/ajax_q/update_rate/', 'ActionController@update_rate');
Route::post('/ajax_q/remove_rate/', 'ActionController@remove_rate');
Route::post('/ajax_q/get_tags/', 'ActionController@get_tags');
Route::post('/ajax_q/get_inputed_tags/', 'ActionController@ajaxGetInputedTags');

//質問の絞り込み条件をセッションに登録。
Route::post('/ajax_q/register_questions_filter_mode/{mode_name}', 'ActionController@registerQuestionsFilterMode');
//質問のマイタグによる絞り込み条件をセッションに登録。
Route::post('/ajax_q/register_my_tag_filter_mode/{mode_name}', 'ActionController@registerMyTagFilterMode');

Route::post('/ajax_q/add_to_my_tag/', 'ActionController@add_to_my_tag');
Route::post('/ajax_q/delete_my_tag/', 'ActionController@delete_my_tag');
//ホーム画面の質問取得
Route::get('/ajax_q/get_questions/', 'HomeController@ajaxGetQuestions');
//ホーム画面の質問取得(検索キーワード考慮)
Route::get('/ajax_q/get_questions_by_searched_kw', 'HomeController@ajaxGetQuestionsBySearchedKW');

Route::get('/ajax_q/get_clip_questions/', 'HomeController@ajaxGetClipQuestions');
Route::get('/ajax_q/get_my_tags/', 'HomeController@ajax_get_my_tags');

//後で使うかもしれないので取っておきます。
// Route::post('/ajax_q/get_rewrite_requests', 'ActionController@get_rewrite_requests');
// Route::post('/ajax_q/insert_rewrite_requests', 'ActionController@insert_rewrite_requests');