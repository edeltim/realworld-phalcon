<?php

namespace RealWorld\Models;

use Firebase\JWT\JWT;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Behavior\Timestampable;
use Phalcon\Security;
use RealWorld\Validators\RegisterUser;
use RealWorld\Validators\UpdateUser;

/**
 * Class User
 * @package RealWorld\Models
 */
class User extends Model implements \JsonSerializable
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
     * @var string
     * @Column(type="string", length=64, nullable=true)
     */
    public $username;

    /**
     *
     * @var string
     * @Column(type="string", length=48, nullable=false)
     */
    public $email;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=false)
     */
    public $password;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $bio;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $image;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $token;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $token_expires;

    /**
     *
     * @var string
     * @Column(type="datetime", nullable=true)
     */
    public $created;

    /**
     *
     * @var string
     * @Column(type="datetime", nullable=true)
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
        $this->hasMany('id', 'Articles', 'user_id', ['alias' => 'Articles']);
        $this->hasMany('id', 'Comments', 'user_id', ['alias' => 'Comments']);
        $this->hasMany('id', 'Favorites', 'user_id', ['alias' => 'Favorites']);
        $this->hasMany('id', Follows::class, 'follower_id', ['alias' => 'Follows']);
        $this->hasMany('id', Follows::class, 'followed_id', ['alias' => 'Follows']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'users';
    }

    /**
     * @param mixed $data
     * @param mixed $whiteList
     * @return User|bool
     */
    public function create($data = null, $whiteList = null)
    {
        if (!parent::create($data, $whiteList)) {
            return false;
        }

        return $this;
    }

    public function beforeUpdate()
    {
        $validator = new UpdateUser();
        $validator->setEntity($this);

        return $this->validate($validator);
    }

    /**
     *
     */
    public function beforeSave()
    {
        // Convert the array into a string
        $this->token = $this->generateJWT();
    }

    /**
     * @return string
     */
    private function generateJWT()
    {
        // Encode the token which will expire 60 days from yesterday.
        $timestamp = time()-86400;
        $token = [
            'id' => $this->username,
            'exp' => strtotime("+7 day", $timestamp)
        ];
        $key = $this->getDI()->get('config')->application->security->salt;

        return JWT::encode($token, $key);
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password)
    {
        $security = new Security();
        $this->password = $security->hash($password);

        return $this;
    }

    /**
     * @param User $userToCheck
     * @return bool
     */
    public function isFollowing(User $userToCheck)
    {
        return (bool) Follows::findFirst([
            "conditions" => "followed_id = ?1",
            "bind"       => [
                1 => $userToCheck->id,
            ]
        ]);
    }

    /**
     * @param User $userToFollow
     * @return bool
     */
    public function follow(User $userToFollow)
    {
        if (!$this->isFollowing($userToFollow)) {
            $follower = new Follows([
                'follower_id' => $this->id,
                'followed_id' => $userToFollow->id,
            ]);

            return $follower->save();
        }
    }

    /**
     * @param User {
     * @return bool
     */
    public function unFollow(User $userToUnFollow)
    {
        return Follows::findFirst([
            "conditions" => "followed_id = ?1",
            "bind"       => [
                1 => $userToUnFollow->id,
            ]
        ])->delete();
    }
}
