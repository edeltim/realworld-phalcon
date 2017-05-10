<?php

namespace RealWorld\Validators;

use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Url;
use RealWorld\Models\User;

/**
 * Class UpdateUserValidator
 * @package RealWorld\Validators
 */
class UpdateUserValidator extends Validation
{
    public function initialize()
    {
        $this
            ->add(
                'username',
                new Uniqueness([
                    "model"   => new User(),
                    "message" => "has already been taken",
                ])
            )
            ->add(
                'email',
                new Uniqueness([
                    "model"   => new User(),
                    "message" => "has already been taken",
                ])
            )
            ->add(
                "email",
                new Email([
                    "message" => "is invalid",
                ])
            )
            ->add(
                'bio',
                new StringLength([
                    'max' => 255,
                    'message' => 'is too long'
                ])
            )
            ->add('image',
                new Url([
                    'max' => 255,
                    'message' => 'is not a url'
                ])
            )
        ;
    }
}