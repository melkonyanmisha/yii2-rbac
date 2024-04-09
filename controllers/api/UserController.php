<?php

namespace app\controllers\api;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use app\models\User;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\filters\VerbFilter;
use yii\web\Response;
use app\models\LoginForm;
use yii\rest\ActiveController;
use yii\filters\ContentNegotiator;

class UserController extends ActiveController
{
    public $modelClass = 'app\models\User';
    public $enableCsrfValidation = false;

    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'register' => ['POST', 'OPTIONS'],
                    'login' => ['POST', 'OPTIONS'],
                    'me' => ['GET', 'OPTIONS'],
                ],
            ],

            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],

            'corsFilter' => [
                'class' => Cors::class,
                'cors' => [
                    // restrict access to
                    'Origin' => ['http://localhost:3000'],
                    // Allow only POST and PUT methods
                    'Access-Control-Request-Method' => ['POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    // Allow only headers 'X-Wsse'
                    'Access-Control-Request-Headers' => ['Content-Type', 'Authorization'],
                    // Allow credentials (cookies, authorization headers, etc.) to be exposed to the browser
                    'Access-Control-Allow-Credentials' => true,
                    // Allow OPTIONS caching
                    'Access-Control-Max-Age' => 86400,
                    // Allow the X-Pagination-Current-Page header to be exposed to the browser.
                    'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
                ],
            ],
        ];
    }

    /**
     * Register a new user.
     *
     * @return array
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionRegister(): array
    {
        $user = new User();
        $requestBody = Yii::$app->getRequest()->getBodyParams();
        $user->username = $requestBody['username'] ?? '';
        $user->email = $requestBody['email'] ?? '';
        $user->password_hash = isset($requestBody['password']) ? Yii::$app->security->generatePasswordHash(
            $requestBody['password']
        ) : '';
        $user->generateAuthKey();
        $user->generateAccessToken();
        $user->role = User::ROLE_USER;
        $user->status = User::STATUS_ACTIVE;
        $user->created_at = time();
        $user->updated_at = time();

        if ($user->load($requestBody, '') && $user->validate()) {
            if ($user->save()) {
                $userData = $user->attributes;
                unset($userData['password_hash'], $userData['auth_key']);

                return $userData;
            } else {
                Yii::$app->response->setStatusCode(500); // Internal Server Error

                return ['error' => 'Failed to save user.'];
            }
        } else {
            Yii::$app->response->setStatusCode(422); // Unprocessable Entity

            return $user->errors;
        }
    }

    /**
     * Login a user and returns the authentication token.
     *
     * @return array|string[]
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionLogin(): array
    {
        $model = new LoginForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        // If login is successful, generate and return a new access token
        if ($model->login()) {
            $user = $model->getUser();
            $user->generateAccessToken();
            $user->save();

            return ['access_token' => $user->access_token];
        } else {
            // If login fails, return an error response
            Yii::$app->response->statusCode = 401;

            return ['error' => 'Invalid username or password'];
        }
    }

    /**
     * Get data of the currently authenticated user.
     *
     * @return array
     */
    public function actionMe()
    {
        $token = Yii::$app->getRequest()->getHeaders()->get('Authorization');

        if ($token !== null && preg_match('/^Bearer\s+(.*?)$/', $token, $matches)) {
            $user = User::findByToken($matches[1]);

            if ($user !== null) {
                $userData = $user->attributes;
                unset($userData['password_hash'], $userData['auth_key']);
                return $userData;
            }
        }

        Yii::$app->response->setStatusCode(401); // Unauthorized
        return ['error' => 'User not authenticated'];
    }
}
