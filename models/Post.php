<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "posts".
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property int $author_id
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $author
 */
class Post extends \yii\db\ActiveRecord
{
    public const STATUS_DEFAULT = 'pending';
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_DECLINED = 'declined';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'posts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['title', 'content', 'author_id', 'created_at', 'updated_at'], 'required'],
            [['content'], 'string'],
            [['author_id', 'created_at', 'updated_at'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => ['pending', 'declined', 'approved']],
            [
                ['author_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => User::class,
                'targetAttribute' => ['author_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id'         => 'ID',
            'title'      => 'Title',
            'content'    => 'Content',
            'author_id'  => 'Author ID',
            'status'     => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return string[]
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING  => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_DECLINED => 'Declined',
        ];
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        $statuses = [
            self::STATUS_PENDING  => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_DECLINED => 'Declined',
        ];

        return isset($statuses[$this->status]) ? $statuses[$this->status] : '';
    }

    /**
     * Gets query for [[Author]].
     *
     * @return ActiveQuery
     */
    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }
}
