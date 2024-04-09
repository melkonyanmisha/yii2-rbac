<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string|null $auth_key
 * @property string|null $access_token
 * @property string $role
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property-write string $password
 * @property-read null|string $authKey
 * @property Post[] $posts
 */
class User extends ActiveRecord implements IdentityInterface
{
    public const ROLE_ADMINISTRATOR = 'administrator';
    public const ROLE_MODERATOR = 'moderator';
    public const ROLE_USER = 'user';

    public const STATUS_ACTIVE = 10;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'users';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['username', 'email', 'password_hash', 'created_at', 'updated_at'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'email', 'password_hash', 'access_token', 'role'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['access_token'], 'unique'],
            [['role'], 'in', 'range' => [self::ROLE_ADMINISTRATOR, self::ROLE_MODERATOR, self::ROLE_USER]],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'email' => 'Email',
            'password_hash' => 'Password',
            'auth_key' => 'Auth Key',
            'access_token' => 'Access Token',
            'role' => 'Role',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Finds user by username
     *
     * @param string $username
     *
     * @return ActiveRecord|null
     */
    public static function findByUsername(string $username): ActiveRecord|null
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @param int $id
     *
     * @return ActiveRecord|null
     */
    public static function findIdentity($id): ActiveRecord|null
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @param string $token
     * @param $type
     *
     * @return ActiveRecord|null
     */
    public static function findIdentityByAccessToken($token, $type = null): ActiveRecord|null
    {
        return static::findOne(['access_token' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->getPrimaryKey();
    }

    /**
     * @return string|null
     */
    public function getAuthKey(): ?string
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     *
     * @return bool
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password
     *
     * @return bool if password provided is valid for current user
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     *
     * @return void
     * @throws Exception
     */
    public function setPassword(string $password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     *
     * @return void
     * @throws Exception
     */
    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates access token
     *
     * @return void
     * @throws Exception
     */
    public function generateAccessToken(): void
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }

    /**
     * Gets query for [[Posts]].
     *
     * @return ActiveQuery
     */
    public function getPosts(): ActiveQuery
    {
        return $this->hasMany(Post::class, ['author_id' => 'id']);
    }

    /**
     * Finds user by token.
     *
     * @param string $token
     * @return static|null
     */
    public static function findByToken(string $token): ActiveRecord|null
    {
        return static::findOne(['access_token' => $token, 'status' => self::STATUS_ACTIVE]);
    }
}
