# Curious Cat Clone - Backend API

## Questions

All Question routes require an `Authorization Header` in the format: `Authorization: Bearer ACCESS_TOKEN`

### All Questions
GET `/api/question.php`

### Specific Question
GET `/api/question.php?id=2`

### Delete Question
DELETE `/api/question.php?id=2`

### Create Question
POST `/api/question.php`

Example Request Body

```json
{
    "label": "Question Title",
    "target_user": 1,
    "name_hidden": true
}
```

## Answers

All Answer routes require an `Authorization Header` in the format: `Authorization: Bearer ACCESS_TOKEN`

### Answers to Question
GET `/api/answer.php?question=2`

### Delete Answer
DELETE `/api/answer.php?id=2`

### Create Answer
POST `/api/answer.php`

Example Request Body

```json
{
    "question_id": 5,
    "label": "Answer Text Here"
}
```

## Authentication

### Register
POST `/api/register.php`

Example Request Body

```json
{
    "username": "john-doe",
    "email_address": "john@doe.com",
    "password": "super_secret_password",
    "confirm_password": "super_secret_password"
}
```

### Login
POST `/api/login.php`

Example Request Body

```json
{
    "userOrEmail": "john@doe.com",
    "password": "super_secret_password"
}
```

Coming up...

* Notification API
* Follower API
* Search API
* Profile API