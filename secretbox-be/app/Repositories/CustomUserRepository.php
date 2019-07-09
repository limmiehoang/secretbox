<?php
/**
 * Created by PhpStorm.
 * User: limmie
 * Date: 17/04/2019
 * Time: 12:23
 */

namespace SecretBox\Repositories;

use SecretBox\User;

use Auth0\Login\Auth0User;
use Auth0\Login\Auth0JWTUser;
use Auth0\Login\Repository\Auth0UserRepository;


class CustomUserRepository extends Auth0UserRepository
{
    /**
     * Get an existing user or create a new one
     *
     * @param array $profile - Auth0 profile
     *
     * @return User
     */
    protected function upsertUser( $profile ) {

        // See if we have a user that matches the Auth0 user_id
        $user = User::where( 'sub', $profile['sub'] )->first();

        // In not, add them to the database
        if ( ! $user ) {
            $user = new User();

            // All are required, no default set
            $user->setAttribute( 'sub', $profile['sub'] );
            $user->setAttribute( 'email', isset( $profile['email'] ) ? $profile['email'] : $profile[config('laravel-auth0.api_identifier') . '/email'] );
            $user->setAttribute( 'name', isset( $profile['name'] ) ? $profile['name'] : $profile[config('laravel-auth0.api_identifier') . '/name'] );

            $user->save();
        }
        return $user;
    }

    /**
     * Authenticate a user with a decoded ID Token
     *
     * @param object $jwt
     *
     * @return Auth0JWTUser
     */
    public function getUserByDecodedJWT( $jwt ) {
        $user = $this->upsertUser( (array) $jwt );
        return new Auth0JWTUser( (object) $user->getAttributes() );
    }

    /**
     * Get a User from the database using Auth0 profile information
     *
     * @param array $userinfo
     *
     * @return Auth0User
     */
    public function getUserByUserInfo( $userinfo ) {
        $user = $this->upsertUser( $userinfo['profile'] );
        return new Auth0User( $user->getAttributes(), $userinfo['accessToken'] );
    }
}