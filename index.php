<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Curious Cat Clone</title>
    <style>
        body {
            font-family: sans-serif;
        }
        code {
            font-family: "IBM Plex Mono", monospace;
        }
    </style>
</head>
<body>
<h1>API Endpoints</h1>

<h2>Questions</h2>
<p>All Routes require <code>Header Authorization: Bearer ACCESS_TOKEN</code></p>
<div>
    <h3>All Questions</h3>
    <code>GET /api/question.php</code>
</div>

<div>
    <h3>Specific Question</h3>
    <code>GET /api/question.php?id=2</code>
</div>

<div>
    <h3>Delete Question</h3>
    <code>DELETE /api/question.php?id=2</code>
</div>

<div>
    <h3>Create Question</h3>
    <code>POST /api/question.php</code>
    <h4>Example Request Body</h4>
    <code>
        {
        "label": "Question Title",
        "target_user": 1,
        "name_hidden": true
        }
    </code>
</div>

<hr>

<!--<h2>Answers</h2>-->
<!--<div>All Answers: <code>GET /api/answer.php</code></div>-->
<!--<div>Answers for Specific Question: <code>GET</code><a href="/api/answer.php?id=1"><code>/api/answer.php?id={QUESTION_ID}</code></div>-->
<!---->

<h2>Authentication</h2>
<div>
    <h3>Login</h3>
    <code>POST /api/login.php</code>
    <h4>Example Request Body</h4>
    <code>
        {
        "userOrEmail": "your@email.com",
        "password": "secret_password"
        }
    </code>
</div>

<div>
    <h3>Register</h3>
    <code>POST /api/register.php</code>
    <h4>Example Request Body</h4>
    <code>
        {
        "username": "your_username",
        "email_address": "your@email.com"
        "password": "secret_password",
        "confirm_password": "secret_password"
        }
    </code>
</div>

</body>
</html>
