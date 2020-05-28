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

### Profile
GET `/api/profile.php?id=6`

Returns all data related to the users profile. 

Example response:
```json
{
  "info": {
        "follower_count": 0,
        "following_count": 0,
        "is_following": false,
        "own_profile": true  
  },
  "stats": {
        "questions_asked": 1,
        "questions_answered": 2
  },
  "timeline": [
        {
            "question_id": 6,
            "question_label": "This is another question",
            "question_timestamp": "2020-05-28",
            "question_name_hidden": 1,
            "answer_id": 2,
            "answer_label": "This is my answer",
            "answer_timestamp": "2020-05-28",
            "target_user": 6,
            "target_user_name": "Peter Smith",
            "from_user": 5,
            "from_user_name": "john@doe.com"
        },  
        {
            "question_id": 6,
            "question_label": "This is another question",
            "question_timestamp": "2020-05-28",
            "question_name_hidden": 1,
            "answer_id": 2,
            "answer_label": "This is my answer",
            "answer_timestamp": "2020-05-28",
            "target_user": 6,
            "target_user_name": "Peter Smith",
            "from_user": 5,
            "from_user_name": "john@doe.com"
        }
  ]
}
```


Coming up...

* Notification API
* Follower API
* Search API