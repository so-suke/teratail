select a.id, a.question_id, a.md_content,
high.cnt as 'high_cnt',low.cnt as 'low_cnt',
(high.cnt - low.cnt) as sum_rates,
u.id as 'u_id', u.name as 'u_name', u.score as 'u_score',
a.created_at
from answers as a
join (
	select a.id as answer_id, count(ar.answer_id) as cnt
	from answers as a
	left join answer_rates as ar on a.id = ar.answer_id
	and ar.kind='high'
	where question_id = 1
	group by a.id, ar.answer_id
) as high on a.id = high.answer_id
join (
	select a.id as answer_id, count(ar.answer_id) as cnt
	from answers as a
	left join answer_rates as ar on a.id = ar.answer_id
	and ar.kind='low'
	where question_id = 1
	group by a.id, ar.answer_id
) as low on a.id = low.answer_id
join users as u on a.user_id = u.id
order by a.created_at desc

-- 該当質問idsのタグを取得。
select *
from question_to_tags as qtt
join tags as t on qtt.tag_id = t.id
and qtt.question_id in (1,2)

-- 質問一覧, 検索キーワード考慮
select q2.id, q2.user_id, u.name as u_name, q2.is_resolved, q2.title, q2.md_content, q2.created_at, q2.created_at_fmt, q2.answer_cnt
from (
	select q.id, q.user_id, q.is_resolved, q.title, q.md_content, q.created_at, DATE_FORMAT(q.created_at, "%Y-%m-%d") as created_at_fmt, (
		select count(question_id)
		from answers
		where question_id = q.id
		group by question_id
	) as answer_cnt
	from questions as q
) as q2
join users as u on q2.user_id = u.id
and q2.title like '%php%'

-- 未回答の質問一覧
select q2.id, q2.user_id, u.name as u_name, q2.is_resolved, q2.title, q2.md_content, q2.created_at, q2.created_at_fmt, q2.answer_cnt
from (
	select q.id, q.user_id, q.is_resolved, q.title, q.md_content, q.created_at, DATE_FORMAT(q.created_at, "%Y-%m-%d") as created_at_fmt, (
		select count(question_id)
		from answers
		where question_id = q.id
		group by question_id
	) as answer_cnt
	from questions as q
) as q2
join users as u on q2.user_id = u.id
and q2.answer_cnt is null

-- 未回答の質問一覧にMyタグによる絞り込みも考慮させたい。
select q2.id, q2.user_id, u.name as u_name, q2.is_resolved, q2.title, q2.md_content, q2.created_at, q2.created_at_fmt, q2.answer_cnt
from (
	select q.id, q.user_id, q.is_resolved, q.title, q.md_content, q.created_at, DATE_FORMAT(q.created_at, "%Y-%m-%d") as created_at_fmt, (
		select count(question_id)
		from answers
		where question_id = q.id
		group by question_id
	) as answer_cnt
	from questions as q
	where q.id in (
		select question_id
		from question_to_tags
		where tag_id in (
			select tag_id
			from my_tags
			where owner_id = 1
		)
	)
) as q2
join users as u on q2.user_id = u.id
and q2.answer_cnt is null

-- 未解決or解決済の質問一覧
select q.id, q.user_id, u.name as user_name, q.is_resolved, q.title, q.md_content, q.created_at, DATE_FORMAT(q.created_at, "%Y-%m-%d") as created_at_fmt, (
	select count(question_id)
	from answers
	where question_id = q.id
	group by question_id
) as answer_cnt
from questions as q
join users as u on q.user_id = u.id
-- and q.is_resolved = 0
and q.is_resolved = 1