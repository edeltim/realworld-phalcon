<?php

namespace RealWorld\Models;

use Phalcon\Mvc\Model\Behavior\Timestampable;

/**
 * Class Comments
 * @package RealWorld\Models
 */
class Comments extends Model
{
    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=20, nullable=false)
     */
    public $id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=20, nullable=false)
     */
    public $userId;

    /**
     *
     * @var integer
     * @Column(type="integer", length=20, nullable=false)
     */
    public $articleId;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $body;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $created;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $modified;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->addBehavior(
            new Timestampable(
                [
                    "beforeCreate" => [
                        "field"  => "created",
                        "format" => 'Y-m-d H:i:s'
                    ],
                ]
            )
        );

        $this->addBehavior(
            new Timestampable(
                [
                    "beforeCreate" => [
                        "field"  => "modified",
                        "format" => 'Y-m-d H:i:s'
                    ],
                ]
            )
        );

        $this->setSchema("realworlddb");
        $this->belongsTo('userId', User::class, 'id', ['alias' => 'User']);
        $this->belongsTo('articleId', Articles::class, 'id', ['alias' => 'Articles']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'comments';
    }
}
