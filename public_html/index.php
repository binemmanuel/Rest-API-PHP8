<?php

use Binemmanuel\ServeMyPhp\{
    Database,
    Request,
    Response,
    Router,
    Rule
};
use Model\User;

require __DIR__ . '/../config.php';

$db = (new Database($_ENV))->mysqli();

$app = new Router($db);

$app->get('/', function (Request $req, Response $res) {
    $res::sendJson(['message' => 'Welcome']);
});

$app->post('/api/add/user', function (Request $req, Response $res) use ($db) {
    $user = (new User($db))->loadData($req->jsonBody());

    $user->makeRules([
        'username' => [
            Rule::REQUIRED,
            [Rule::UNIQUE, 'class' => $user::class],
        ],
        'email' => [
            Rule::REQUIRED,
            Rule::EMAIL,
            [Rule::UNIQUE, 'class' => $user::class],
        ],
        'password' => [
            Rule::REQUIRED,
            [Rule::MIN_LENGTH, 4],
        ],
    ]);

    if ($user->hasError()) {
        return $res::sendJson([
            'error' => true,
            'errors' => $user->errors(),
        ], statusCode: 400);
    }

    $user->userId = $user->id;
    $user = $user->save();

    return $res::sendJson([
        'error' => false,
        'message' => 'Created successfully',
        'user' => $user,
    ]);
});

$app->get('/api/get/users', function (Request $req, Response $res) use ($db) {
    $users = (new User($db))->fetchAll();

    $res::sendJson([
        'length' => count($users),
        'users' => $users,
    ]);
});

$app->post('/api/auth/user', function (Request $req, Response $res) use ($db) {
    $user = (new User($db))->loadData($req->jsonBody());

    $user->makeRules([
        'username' => [Rule::REQUIRED],
        'password' => [Rule::REQUIRED],
    ]);

    if ($user->hasError()) {
        return $res::sendJson([
            'error' => true,
            'errors' => $user->errors(),
        ]);
    }

    $user = $user->verify();

    if (empty($user)) {
        return $res::sendJson([
            'error' => true,
            'message' => 'Incorrect useranme or password',
        ], statusCode: 401);
    }

    return $res::sendJson([
        'error' => false,
        'message' => 'Authenticated successfully',
    ]);
});

$app->delete('/api/delete/user', function (Request $req, Response $res) use ($db) {
    $user = (new User($db))->loadData($req->jsonBody());

    $user->makeRules([
        'userId' => [Rule::REQUIRED],
    ]);

    if ($user->hasError()) {
        return $res::sendJson([
            'error' => true,
            'errors' => $user->errors(),
        ]);
    }

    $deleted = $user->delete(['userId' => $user->userId]);

    return $res::sendJson([
        'error' => !$deleted,
        'message' => !$deleted
            ? 'Something went when trying to delete a user'
            : 'Deleted successfully',
    ]);
});

$app->run();
