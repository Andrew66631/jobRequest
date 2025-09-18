<?php

namespace app\controllers\api;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use app\models\User;
use app\services\UserService;
use app\behaviors\InputValidationBehavior;

class AuthController extends Controller
{
    public $enableCsrfValidation = false;

    private $userService;

    public function __construct($id, $module, UserService $userService, $config = [])
    {
        $this->userService = $userService;
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['inputValidation'] = [
            'class' => InputValidationBehavior::class,
            'requiredFields' => [
                'login' => ['username', 'password'],
                'register' => ['username', 'password', 'email'],
            ],
        ];

        $behaviors['authenticator'] = [
            'class' => \yii\filters\auth\HttpBearerAuth::class,
            'except' => ['login', 'register', 'options'],
        ];

        return $behaviors;
    }

    public function actionOptions()
    {
        Yii::$app->response->statusCode = 200;
    }

    public function actionLogin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $username = $request->post('username');
        $password = $request->post('password');

        try {
            $user = $this->userService->authenticateUser($username, $password);

            return [
                'success' => true,
                'token' => $user['token'],
                'user' => [
                    'id' => $user['user']->id,
                    'username' => $user['user']->username,
                    'email' => $user['user']->email,
                ]
            ];

        } catch (\yii\web\BadRequestHttpException $e) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionRegister()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $username = $request->post('username');
        $password = $request->post('password');
        $email = $request->post('email');

        try {
            $this->userService->validateRegistrationData($username, $password, $email);
            $user = $this->userService->createUser($username, $password, $email);

            return [
                'success' => true,
                'token' => $user['token'],
                'user' => [
                    'id' => $user['user']->id,
                    'username' => $user['user']->username,
                    'email' => $user['user']->email,
                ]
            ];

        } catch (\yii\web\BadRequestHttpException $e) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}