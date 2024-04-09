<?php

namespace app\controllers\api;

use Yii;
use yii\filters\Cors;
use yii\base\InvalidConfigException;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\rest\ActiveController;
use app\models\Post;
use yii\filters\ContentNegotiator;
use yii\web\ServerErrorHttpException;
use yii\web\Request;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;


class PostController extends ActiveController
{
    public $modelClass = 'app\models\Post';
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
                    'index' => ['GET'],
                    'view' => ['GET'],
                    'create' => ['POST', 'OPTIONS'],
                    'update' => ['PUT', 'OPTIONS'],
                ],
            ],

            'authenticator' => [
                'class' => HttpBearerAuth::class,
                'except' => ['index', 'view'], // Allow access without authentication
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
     * @return array
     */
    public function actions(): array
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update']); //To overwrite parent class methods

        return $actions;
    }

    /**
     * @param Request $request
     *
     * @return array
     */

    public function actionIndex(Request $request): array
    {
        $authorId = $request->getQueryParam('author_id');
        $startDate = $request->getQueryParam('start_date');
        $endDate = $request->getQueryParam('end_date');
        $title = $request->getQueryParam('title');

        // Initialize query
        $postsQuery = Post::find()->where(['status' => Post::STATUS_APPROVED]);

        // Apply author_id filter if provided
        if ($authorId) {
            $postsQuery->andWhere(['author_id' => $authorId]);
        }

        // Apply start date filter if provided
        if ($startDate) {
            $postsQuery->andWhere(['>=', 'created_at', $startDate]);
        }

        // Apply start date filter if provided
        if ($endDate) {
            $postsQuery->andWhere(['<=', 'created_at', $endDate]);
        }

        // Apply title filter if provided
        if ($title) {
            $postsQuery->andWhere(['LIKE', 'title', $title]);
        }

        // Always sort by created_at in descending order
        $postsQuery->orderBy(['created_at' => SORT_DESC]);

        // Fetch data using ActiveDataProvider to handle pagination
        $dataProvider = new ActiveDataProvider([
            'query' => $postsQuery,
            'pagination' => false, // Disable pagination
        ]);

        // Get the sorted and filtered posts
        $posts = $dataProvider->getModels();

        $response = [];

        foreach ($posts as $post) {
            $response[] = [
                ...$post->attributes,
                'author_username' => $post->author->username,
            ];
        }

        return $response;
    }

    /**
     * Displays a single Post model.
     *
     * @param int $id
     *
     * @return array
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id): array
    {
        // Find the post with the given ID and status approved
        $post = Post::findOne(['id' => $id, 'status' => Post::STATUS_APPROVED]);

        if (!$post) {
            throw new NotFoundHttpException('The requested post does not exist.');
        }

        // Return the post attributes along with author_username
        return [
            ...$post->attributes,
            'author_username' => $post->author->username,
        ];
    }

    /**
     * Creates a new Post model.
     * If creation is successful, returns the newly created model.
     *
     * @return array
     * @throws InvalidConfigException
     * @throws ServerErrorHttpException
     */
    public function actionCreate(): array
    {
        $post = new Post();
        $post->load(Yii::$app->getRequest()->getBodyParams(), '');
        $post->created_at = time();
        $post->updated_at = time();
        $post->status = Post::STATUS_DEFAULT;

        if ($post->validate()) {
            if ($post->save()) {
                Yii::$app->response->setStatusCode(201); // Created

                return $post->attributes;
            } else {
                Yii::$app->response->setStatusCode(500); // Internal Server Error
                throw new ServerErrorHttpException('Failed to save the post.');
            }
        } else {
            Yii::$app->response->setStatusCode(422); // Unprocessable Entity

            return $post->errors;
        }
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws ForbiddenHttpException
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionUpdate(int $id): array
    {
        $post = Post::findOne($id);

        if (!$post) {
            throw new NotFoundHttpException('Post not found.');
        }

        $current_user = Yii::$app->user->identity;

        if ($current_user->id !== $post->author_id) {
            throw new ForbiddenHttpException('You are not allowed to edit this post.');
        }

        $postData = Yii::$app->getRequest()->getBodyParams();
        $post->load($postData, '');

        $post->updated_at = time(); // Update timestamp

        if ($post->validate()) {
            if ($post->save()) {
                Yii::$app->response->setStatusCode(200); // OK

                return $post->attributes;
            } else {
                Yii::$app->response->setStatusCode(500); // Internal Server Error
                throw new ServerErrorHttpException('Failed to update the post.');
            }
        } else {
            Yii::$app->response->setStatusCode(422); // Unprocessable Entity

            return $post->errors;
        }
    }
}