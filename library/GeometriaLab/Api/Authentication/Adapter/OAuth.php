<?php

namespace GeometriaLab\Api\Authentication\Adapter;

use GeometriaLab\Api\Authentication\OAuthTokenInterface;

use Zend\ServiceManager\ServiceManager as ZendServiceManager,
    Zend\Authentication\Adapter\AdapterInterface as ZendAdapterInterface,
    Zend\Authentication\Result as ZendAuthenticationResult,
    Zend\Http\Request as ZendRequest;


class OAuth implements ZendAdapterInterface
{
    /**
     * When using the bearer token type, there is a specifc Authorization header required: "Bearer"
     */
    const TOKEN_BEARER_HEADER_NAME = 'Bearer';
    /**
     * Used to define the name of the OAuth access token parameter
     * (POST & GET). This is for the "bearer" token type.
     * Other token types may use different methods and names.
     *
     * IETF Draft section 2 specifies that it should be called "access_token"
     *
     * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-bearer-06#section-2.2
     * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-bearer-06#section-2.3
     */
    const TOKEN_PARAM_NAME = 'access_token';

    /**
     * Access token
     *
     * @var OAuthTokenInterface
     */
    protected $accessToken;
    /**
     * A space-separated string of required scope(s), if you want to check for scope.
     *
     * @var string
     */
    protected $scope;

    /**
     * Set token
     *
     * @param OAuthTokenInterface $accessToken
     * @return OAuth
     */
    public function setAccessToken(OAuthTokenInterface $accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * Set scope
     *
     * @param string $scope
     * @return OAuth
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * Get scope
     *
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Get access token
     *
     * @return OAuthTokenInterface
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Performs an authentication attempt
     *
     * @return ZendAuthenticationResult
     */
    public function authenticate()
    {
        if ($this->getAccessToken() === null) {
            return new ZendAuthenticationResult(
                ZendAuthenticationResult::FAILURE_CREDENTIAL_INVALID,
                null,
                array('The access token provided is invalid.')
            );
        }

        // Check token expiration (expires is a mandatory parameter)
        if ($this->getAccessToken()->hasExpired()) {
            return new ZendAuthenticationResult(
                ZendAuthenticationResult::FAILURE,
                null,
                array('The access token provided has expired.')
            );
        }

        // Check scope, if provided
        // If token doesn't have a scope, it's NULL/empty, or it's insufficient, then throw an error
        if ($this->getScope() && (!$this->getAccessToken()->getScope() || !$this->checkScope($this->getScope(), $this->getAccessToken()->getScope()))) {
            return new ZendAuthenticationResult(
                ZendAuthenticationResult::FAILURE,
                null,
                array('The request requires higher privileges than provided by the access token.')
            );
        }

        return new ZendAuthenticationResult(
            ZendAuthenticationResult::SUCCESS,
            $this->getAccessToken()->getData()
        );
    }

    /**
     * This is a convenience function that can be used to get the token, which can then
     * be passed to verifyAccessToken(). The constraints specified by the draft are
     * attempted to be adheared to in this method.
     *
     * As per the Bearer spec (draft 8, section 2) - there are three ways for a client
     * to specify the bearer token, in order of preference: Authorization Header,
     * POST and GET.
     *
     * @param ZendRequest $request
     * @return string|null
     */
    public static function getBearerToken(ZendRequest $request)
    {
        $tokens = array();

        $token = self::getBearerTokenFromHeaders($request);
        if ($token !== null) {
            $tokens[] = $token;
        }

        $token = self::getBearerTokenFromPost($request);
        if ($token !== null) {
            $tokens[] = $token;
        }

        $token = self::getBearerTokenFromQuery($request);
        if ($token !== null) {
            $tokens[] = $token;
        }

        if (count($tokens) < 1) {
            return null;
        }

        return reset($tokens);
    }

    /**
     * Check if everything in required scope is contained in available scope.
     *
     * @param string $required_scope Required scope to be check with.
     * @param string $available_scope
     * @return bool True if everything in required scope is contained in available scope, and False if it isn't.
     * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-7
     */
    protected function checkScope($required_scope, $available_scope)
    {
        // The required scope should match or be a subset of the available scope
        if (!is_array($required_scope)) {
            $required_scope = explode(' ', trim($required_scope));
        }

        if (!is_array($available_scope)) {
            $available_scope = explode(' ', trim($available_scope));
        }

        return (count(array_diff($required_scope, $available_scope)) == 0);
    }

    /**
     * Get the access token from the header
     *
     * @param ZendRequest $request
     * @return string|null
     */
    protected static function getBearerTokenFromHeaders(ZendRequest $request)
    {
        $bearer = null;
        $requestHeaders = $request->getHeaders();
        if (!$requestHeaders->has('AUTHORIZATION')) {
            // The Authorization header may not be passed to PHP by Apache;
            // Trying to obtain it through apache_request_headers()
            if (function_exists('apache_request_headers')) {
                $headers = apache_request_headers();
                // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
                $headers = array_combine(array_map('ucwords', array_keys($headers)), array_values($headers));
                if (isset($headers['Authorization'])) {
                    $bearer = $headers['Authorization'];
                }
            }
        } else {
            $bearer = $requestHeaders->get('AUTHORIZATION')->getFieldValue();
        }

        if (!$bearer) {
            return null;
        }

        if (!preg_match('/' . preg_quote(self::TOKEN_BEARER_HEADER_NAME, '/') . '\s(\S+)/', $bearer, $matches)) {
            return null;
        }

        $token = $matches[1];

        return $token;
    }

    /**
     * Get the token from POST data
     *
     * @param ZendRequest $request
     * @return string|null
     */
    protected static function getBearerTokenFromPost(ZendRequest $request)
    {
        if ($request->isPost()) {
            return null;
        }

        $contentType = $request->getHeaders()->get('Content-Type');

        if ($contentType && $contentType->getFieldValue() != 'application/x-www-form-urlencoded') {
            return null;
        }

        if (!$token = $request->getPost(self::TOKEN_PARAM_NAME)) {
            return null;
        }

        return $token;
    }

    /**
     * Get the token from the query string
     *
     * @param ZendRequest $request
     * @return string|null
     */
    protected static function getBearerTokenFromQuery(ZendRequest $request)
    {
        if (!$token = $request->getQuery(self::TOKEN_PARAM_NAME)) {
            return null;
        }

        return $token;
    }
}
