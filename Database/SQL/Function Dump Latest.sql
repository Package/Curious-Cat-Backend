CREATE OR REPLACE FUNCTION public.fn_stats_questions()
 RETURNS TABLE(id integer, username character varying, photo_file character varying, created_at timestamp without time zone, counter bigint)
 LANGUAGE sql
AS $function$
SELECT

    u.id,
    u.username,
    u.photo_file,
    u.created_at,
    COUNT(DISTINCT q.id) AS counter

FROM
    users u

    INNER JOIN questions q
        ON q.from_user = u.id
        AND q.name_hidden = 0

GROUP BY
    u.id,
    u.username,
    u.photo_file,
    u.created_at

ORDER BY
    counter

    DESC LIMIT 5;

$function$;

CREATE OR REPLACE FUNCTION public.fn_profile_stats(profileid integer)
 RETURNS TABLE(questions_asked bigint, questions_answered bigint)
 LANGUAGE sql
AS $function$
SELECT
    COUNT(fu.id) AS questions_asked,
    COUNT(tu.id) AS questions_answered
FROM
    questions q

        LEFT OUTER JOIN users tu
                        ON tu.id = q.target_user

        LEFT OUTER JOIN users fu
                        ON fu.id = q.from_user
                            AND q.name_hidden = 0 -- Don't count questions hidden in the total

WHERE
        tu.id = profileId OR fu.id = profileId;
$function$;

CREATE OR REPLACE FUNCTION public.fn_question_get(questionid integer)
 RETURNS TABLE(id integer, created_at timestamp without time zone, name_hidden smallint, label character varying, target_user integer, from_username character varying, from_user integer)
 LANGUAGE sql
AS $function$
    SELECT
        q.id,
        q.created_at,
        q.name_hidden,
        q.label,
        q.target_user,
        CASE WHEN q.name_hidden = 1 THEN NULL ELSE u.username END AS from_username,
        CASE WHEN q.name_hidden = 1 THEN NULL ELSE u.id END AS from_user

    FROM
         questions q

         INNER JOIN users u
            ON u.id = q.from_user

    WHERE q.id = questionId
       OR questionId = 0;
$function$;

CREATE OR REPLACE FUNCTION public.fn_stats_answers()
 RETURNS TABLE(id integer, username character varying, photo_file character varying, created_at timestamp without time zone, counter bigint)
 LANGUAGE sql
AS $function$
SELECT

    u.id,
    u.username,
    u.photo_file,
    u.created_at,
    COUNT(DISTINCT a.id) AS counter

FROM
    users u

        INNER JOIN answers a
                   ON a.user_id = u.id

GROUP BY
    u.id,
    u.username,
    u.photo_file,
    u.created_at

ORDER BY
    counter

    DESC LIMIT 5;

$function$;

CREATE OR REPLACE FUNCTION public.fn_stats_newest()
 RETURNS TABLE(id integer, username character varying, photo_file character varying, counter timestamp without time zone)
 LANGUAGE sql
AS $function$
SELECT
    u.id,
    u.username,
    u.photo_file,       
    u.created_at AS counter

FROM
    users u

ORDER BY
    u.created_at

    DESC LIMIT 5;

$function$;

CREATE OR REPLACE FUNCTION public.fn_notification_get(userid integer)
 RETURNS TABLE(id integer, label character varying, notification_read smallint, user_id integer, created_at timestamp without time zone, from_user integer, from_username character varying, from_user_photo character varying, notification_type smallint, notification_type_string character varying, context character varying, context_id integer, hidden smallint)
 LANGUAGE sql
AS $function$
SELECT
    n.id,
    n.label,
    n.notification_read,
    n.user_id,
    n.created_at,
    CASE WHEN n.hidden = 1 THEN NULL ELSE n.from_user END AS from_user,
    CASE WHEN n.hidden = 1 THEN 'Anonymous' ELSE u.username END AS from_username,
    CASE WHEN n.hidden = 1 THEN NULL ELSE u.photo_file END AS from_user_photo,
    n.notification_type,
    nt.type AS notification_type_string,
    n.context,
    n.context_id,
    n.hidden

FROM
    notifications n

        INNER JOIN users u
                   ON u.id = n.from_user

        INNER JOIN notification_type nt
                   ON nt.id = n.notification_type

WHERE user_id = userId AND notification_read = 0

ORDER BY
    created_at DESC

LIMIT 50;
$function$;

CREATE OR REPLACE FUNCTION public.fn_profile_info(profileid integer, currentuser integer)
 RETURNS TABLE(follower_count bigint, following_count bigint, user_id integer, username character varying, created_at timestamp without time zone, photo_file character varying, is_following boolean, own_profile boolean)
 LANGUAGE sql
AS $function$
SELECT
    u2.follower_count AS follower_count,
    u2.following_count AS following_count,
    u2.id AS user_id,
    u2.username AS username,
    u2.created_at AS created_at,
    u2.photo_file AS photo_file,
    CASE WHEN f.created_at IS NOT NULL THEN TRUE ELSE FALSE END AS is_following,
    CASE WHEN u2.id = currentUser THEN TRUE ELSE FALSE END AS own_profile
FROM
    (
        SELECT
            u.id AS id,
            u.username AS username,
            u.created_at AS created_at,
            u.photo_file AS photo_file,
            SUM( CASE WHEN f.followed_user = u.id THEN 1 ELSE 0 END ) AS follower_count,
            SUM( CASE WHEN f.following_user = u.id THEN 1 ELSE 0 END ) AS following_count

        FROM
            users u

                LEFT JOIN followers f
                          ON f.following_user = u.id
                              OR f.followed_user = u.id

        WHERE
                u.id = profileid

        GROUP BY
            u.id, u.username, u.created_at, u.photo_file
    ) u2

        LEFT OUTER JOIN followers f
                        ON f.following_user = currentuser
                            AND f.followed_user = u2.id
$function$;

CREATE OR REPLACE FUNCTION public.fn_profile_questions(profileid integer)
 RETURNS TABLE(question_id integer, question_label character varying, question_timestamp timestamp without time zone, question_name_hidden smallint, answer_id integer, answer_label character varying, answer_timestamp timestamp without time zone, target_user integer, target_user_name character varying, target_user_photo character varying, from_user integer, from_user_name character varying, from_user_photo character varying)
 LANGUAGE sql
AS $function$
SELECT
    q.id AS question_id,
    q.label AS question_label,
    q.created_at AS question_timestamp,
    q.name_hidden AS question_name_hidden,
    a.id AS answer_id,
    a.label AS answer_label,
    a.created_at AS answer_timestamp,
    tu.id AS target_user,
    tu.username AS target_user_name,
    tu.photo_file AS target_user_photo,
    fu.id AS from_user,
    fu.username AS from_user_name,
    fu.photo_file AS from_user_photo

FROM
    questions q

        LEFT OUTER JOIN answers a
                        ON q.id = a.question_id

        INNER JOIN users tu
                   ON tu.id = q.target_user

        INNER JOIN users fu
                   ON fu.id = q.from_user

WHERE
        q.from_user = profileId AND q.name_hidden = 0

ORDER BY
    q.created_at DESC;
$function$;

CREATE OR REPLACE FUNCTION public.fn_home_timeline(targetuserid integer)
 RETURNS TABLE(question_id integer, question_label character varying, question_timestamp timestamp without time zone, question_name_hidden smallint, answer_id integer, answer_label character varying, answer_timestamp timestamp without time zone, target_user integer, target_user_name character varying, target_user_photo character varying, from_user integer, from_user_name character varying, from_user_photo character varying)
 LANGUAGE sql
AS $function$
SELECT
    q.id AS question_id,
    q.label AS question_label,
    q.created_at AS question_timestamp,
    q.name_hidden AS question_name_hidden,
    a.id AS answer_id,
    a.label AS answer_label,
    a.created_at AS answer_timestamp,
    u2.id AS target_user,
    u2.username AS target_user_name,
    u2.photo_file AS target_user_photo,
    CASE WHEN q.name_hidden = 1 THEN NULL ELSE u1.id END AS from_user,
    CASE WHEN q.name_hidden = 1 THEN NULL ELSE u1.username END AS from_user_name,
    CASE WHEN q.name_hidden = 1 THEN NULL ELSE u1.photo_file END AS from_user_photo

FROM questions q

    INNER JOIN answers a
        ON q.id = a.question_id

    INNER JOIN users u1 -- From
        ON u1.id = q.from_user

    INNER JOIN users u2 -- To
        ON u2.id = q.target_user

    INNER JOIN followers f
        ON f.following_user = targetUserId
        AND f.followed_user = u2.id

ORDER BY a.created_at DESC
LIMIT 50;
$function$;

CREATE OR REPLACE FUNCTION public.fn_search_question(searchquery character varying, searchlimit smallint)
 RETURNS TABLE(question_id integer, question_label character varying, question_timestamp timestamp without time zone, question_name_hidden smallint, from_user integer, from_user_name character varying, from_user_photo character varying, target_user integer, target_user_name character varying, target_user_photo character varying, answer_id integer, answer_label character varying, answer_timestamp timestamp without time zone)
 LANGUAGE sql
AS $function$
SELECT
    q.id AS question_id,
    q.label AS question_label,
    q.created_at AS question_timestamp,
    q.name_hidden AS question_name_hidden,
    CASE WHEN q.name_hidden = 1 THEN NULL ELSE fu.id END AS from_user,
    CASE WHEN q.name_hidden = 1 THEN NULL ELSE fu.username END AS from_user_name,
    CASE WHEN q.name_hidden = 1 THEN NULL ELSE fu.photo_file END AS from_user_photo,
    tu.id AS target_user,
    tu.username AS target_user_name,
    tu.photo_file AS target_user_photo,
    a.id AS answer_id,
    a.label AS answer_label,
    a.created_at AS answer_timestamp

FROM questions q

    INNER JOIN users fu
        ON fu.id = q.from_user

    INNER JOIN users tu
        ON tu.id = q.target_user

    LEFT OUTER JOIN answers a
        ON q.id = a.question_id

WHERE
        LOWER(q.label) LIKE searchQuery OR
        LOWER(a.label) LIKE searchQuery

ORDER BY
    q.created_at DESC

LIMIT searchLimit;
$function$;

CREATE OR REPLACE FUNCTION public.fn_profile_answers(profileid integer)
 RETURNS TABLE(question_id integer, question_label character varying, question_timestamp timestamp without time zone, question_name_hidden smallint, answer_id integer, answer_label character varying, answer_timestamp timestamp without time zone, target_user integer, target_user_name character varying, target_user_photo character varying, from_user integer, from_user_name character varying, from_user_photo character varying)
 LANGUAGE sql
AS $function$
SELECT
    q.id AS question_id,
    q.label AS question_label,
    q.created_at AS question_timestamp,
    q.name_hidden AS question_name_hidden,
    a.id AS answer_id,
    a.label AS answer_label,
    a.created_at AS answer_timestamp,
    tu.id AS target_user,
    tu.username AS target_user_name,
    tu.photo_file AS target_user_photo,
    CASE WHEN q.name_hidden = 1 THEN NULL ELSE fu.id END AS from_user,
    CASE WHEN q.name_hidden = 1 THEN NULL ELSE fu.username END AS from_user_name,
    CASE WHEN q.name_hidden = 1 THEN NULL ELSE fu.photo_file END AS from_user_photo

FROM

    questions q

    LEFT OUTER JOIN answers a
        ON q.id = a.question_id

    INNER JOIN users tu
        ON tu.id = q.target_user

    INNER JOIN users fu
        ON fu.id = q.from_user

WHERE
        q.target_user = profileId

ORDER BY
    q.created_at DESC;
$function$

