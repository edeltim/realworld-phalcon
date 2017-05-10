<?php

namespace RealWorld\Auth;

use Phalcon\Mvc\User\Component;
use RealWorld\Models\User;

/**
 * Class Auth
 * @package RealWorld\Auth
 * @property User user
 */
class Auth extends Component
{
    /**
     * @param array $credentials
     * @return User
     * @throws \Exception
     */
    public function check(array $credentials)
    {
        // First check if the user exists.
        $user = User::findFirst([
            "conditions" => "email = ?1",
            "bind"       => [
                1 => $credentials['email'],
            ]
        ]);

        if (!$user) {
            throw new \Exception('Wrong email/password combination');
        }

        // Check the password matches the one saves to the user.
        if (!$this->security->checkHash($credentials['password'], $user->password)) {
            throw new \Exception('Wrong email/password combination');
        }

        return $user;
    }
}