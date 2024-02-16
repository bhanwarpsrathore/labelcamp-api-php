<?php

declare(strict_types=1);

namespace LabelcampAPI;

class Session {

    protected string $accessToken = '';
    protected string $username = '';
    protected string $password = '';
    protected int $expirationTime = 0;

    protected ?Request $request = null;

    /**
     * Constructor
     * Set up client credentials.
     *
     * @param Request $request Optional. The Request object to use.
     */
    public function __construct(string $username = '', string $password = '', ?Request $request = null) {
        $this->setUsername($username);
        $this->setPassword($password);
        $this->request = $request ?? new Request();
    }

    /**
     * Get Username
     * 
     * @return string The username.
     */
    public function getUsername(): string {
        return $this->username;
    }

    /**
     * Get password
     * 
     * @return string The password.
     */
    public function getPassword(): string {
        return $this->password;
    }

    /**
     * Get Access Token
     * 
     * @return string The access token.
     */
    public function getAccessToken(): string {
        return $this->accessToken;
    }

    /**
     * Get the access token expiration time.
     *
     * @return int A Unix timestamp indicating the token expiration time.
     */
    public function getTokenExpiration(): int {
        return $this->expirationTime;
    }

    /**
     * Request an access token given an authorization code.
     *
     *  @return bool True when the access token was successfully granted, false otherwise.
     */
    public function requestAccessToken(): bool {
        $parameters = [
            'grant_type' => 'password',
            'username' => $this->getUsername(),
            'password' => $this->getPassword()
        ];

        ['body' => $response] = $this->request->token('POST', '/token', [
            'form_params' => $parameters
        ]);

        if (isset($response['access_token'])) {
            $this->accessToken = $response["access_token"];
            $this->expirationTime = time() + $response["expires_in"];

            return true;
        }

        return false;
    }

    /**
     * Set the username
     * 
     * @param string $username The username.
     * @return self
     */
    public function setUsername(string $username): self {
        $this->username = $username;

        return $this;
    }

    /**
     * Set the password
     * 
     * @param string $password The password.
     * @return self
     */
    public function setPassword(string $password): self {
        $this->password = $password;

        return $this;
    }
}
