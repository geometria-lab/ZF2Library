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
        $token = self::getBearerTokenFromHeaders($request);
        if ($token !== null) {
            return $token;
        }

        $token = $request->getPost(self::TOKEN_PARAM_NAME);
        if ($token !== null) {
            return $token;
        }

        $token = $request->getQuery(self::TOKEN_PARAM_NAME);
        if ($token !== null) {
            return $token;
        }

        return null;
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
        if (!$requestHeaders->has('Authorization')) {
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
            $bearer = $requestHeaders->get('Authorization')->getFieldValue();
        }

        if (!$bearer) {
            return null;
        }

        $parts = explode(' ', $bearer);

        if (count($parts) != 2) {
            return null;
        }

        if ($parts[0] != self::TOKEN_BEARER_HEADER_NAME) {
            return null;
        }

        return $parts[1];
    }
}
