<?php
/**
 * CLAROLINE
 *
 * @version 0.1
 *
 * @copyright (c) 2014 Quentin Devos
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package OAUTH
 *
 * @author Quentin Devos <q.devos@student.uclouvain.be>
 *
 */

namespace OAuth2\Storage;


class ClarolineStorage implements AuthorizationCodeInterface,
    AccessTokenInterface,
    ClientCredentialsInterface,
    //UserCredentialsInterface,
    RefreshTokenInterface,
    JwtBearerInterface,
    ScopeInterface,
    PublicKeyInterface
{

    protected $config;

    public function __construct($config = array())
    {
        $tableNames = get_module_main_tbl(array(
            'oauth_clients',
            'oauth_access_tokens',
            'oauth_refresh_tokens',
            'oauth_authorization_codes',
            'oauth_jwt',
            'oauth_scopes',
            'oauth_public_keys'
        ));

        $this->config = array_merge(array(
            'client_table' => $tableNames['oauth_clients'],
            'access_token_table' => $tableNames['oauth_access_tokens'],
            'refresh_token_table' => $tableNames['oauth_refresh_tokens'],
            'code_table' => $tableNames['oauth_authorization_codes'],
            'jwt_table' => $tableNames['oauth_jwt'],
            'scope_table' => $tableNames['oauth_scopes'],
            'public_key_table' => $tableNames['oauth_public_keys'],
        ), $config);
    }

    /**
     * Look up the supplied oauth_token from storage.
     *
     * We need to retrieve access token data as we create and verify tokens.
     *
     * @param $oauth_token
     * oauth_token to be check with.
     *
     * @return
     * An associative array as below, and return NULL if the supplied oauth_token
     * is invalid:
     * - expires: Stored expiration in unix timestamp.
     * - client_id: (optional) Stored client identifier.
     * - user_id: (optional) Stored user identifier.
     * - scope: (optional) Stored scope values in space-separated string.
     * - id_token: (optional) Stored id_token (if "use_openid_connect" is true).
     *
     * @ingroup oauth2_section_7
     */
    public function getAccessToken($oauth_token)
    {
        $stmt = sprintf('SELECT * FROM `%s` WHERE `access_token` = \'%s\';', $this->config['access_token_table'], \Claroline::getDatabase()->escape($oauth_token));
        $result = \Claroline::getDatabase()->query($stmt);
        if (!$result->isEmpty()) {
            $token = $result->fetch();
            $token['expires'] = strtotime($token['expires']);
        }

        return null;
    }

    /**
     * Store the supplied access token values to storage.
     *
     * We need to store access token data as we create and verify tokens.
     *
     * @param $oauth_token
     * oauth_token to be stored.
     * @param $client_id
     * Client identifier to be stored.
     * @param $user_id
     * User identifier to be stored.
     * @param int $expires
     * Expiration to be stored as a Unix timestamp.
     * @param string $scope
     * (optional) Scopes to be stored in space-separated string.
     *
     * @ingroup oauth2_section_4
     */
    public function setAccessToken($oauth_token, $client_id, $user_id, $expires, $scope = null)
    {
        // convert expires to datestring
        $expires = date('Y-m-d H:i:s', $expires);

        if ($this->getAccessToken($oauth_token)) {
            $stmt = sprintf('UPDATE `%s` SET `client_id`=\'%s\', `expires`=\'%s\', `user_id`=\'%s\', `scope`=\'%s\' WHERE `access_token`=\'%s\'',
                $this->config['access_token_table'],
                \Claroline::getDatabase()->escape($client_id),
                \Claroline::getDatabase()->escape($expires),
                \Claroline::getDatabase()->escape($user_id),
                \Claroline::getDatabase()->escape($scope),
                \Claroline::getDatabase()->escape($oauth_token)
            );
        } else {
            $stmt = sprintf('INSERT INTO `%s` (`access_token`, `client_id`, `expires`, `user_id`, `scope`) VALUES (\'%s\', \'%s\', \'%s\', \'%s\', \'%s\')',
                $this->config['access_token_table'],
                \Claroline::getDatabase()->escape($oauth_token),
                \Claroline::getDatabase()->escape($client_id),
                \Claroline::getDatabase()->escape($expires),
                \Claroline::getDatabase()->escape($user_id),
                \Claroline::getDatabase()->escape($scope)
            );
        }

        return Claroline::getDatabase()->exec($stmt);
    }

    /**
     * Fetch authorization code data (probably the most common grant type).
     *
     * Retrieve the stored data for the given authorization code.
     *
     * Required for OAuth2::GRANT_TYPE_AUTH_CODE.
     *
     * @param $code
     * Authorization code to be check with.
     *
     * @return
     * An associative array as below, and null if the code is invalid
     * @code
     * return array(
     *     "client_id"    => CLIENT_ID,      // REQUIRED Stored client identifier
     *     "user_id"      => USER_ID,        // REQUIRED Stored user identifier
     *     "expires"      => EXPIRES,        // REQUIRED Stored expiration in unix timestamp
     *     "redirect_uri" => REDIRECT_URI,   // REQUIRED Stored redirect URI
     *     "scope"        => SCOPE,          // OPTIONAL Stored scope values in space-separated string
     * );
     * @endcode
     *
     * @see http://tools.ietf.org/html/rfc6749#section-4.1
     *
     * @ingroup oauth2_section_4
     */
    public function getAuthorizationCode($code)
    {
        $stmt = sprintf('SELECT * FROM `%s` WHERE `authorization_code` = \'%s\';', $this->config['code_table'], \Claroline::getDatabase()->escape($code));
        $result = \Claroline::getDatabase()->query($stmt);
        if (!$result->isEmpty()) {
            $code = $result->fetch();
            $code['expires'] = strtotime($code['expires']);
            return $code;
        }

        return null;
    }

    /**
     * Take the provided authorization code values and store them somewhere.
     *
     * This function should be the storage counterpart to getAuthCode().
     *
     * If storage fails for some reason, we're not currently checking for
     * any sort of success/failure, so you should bail out of the script
     * and provide a descriptive fail message.
     *
     * Required for OAuth2::GRANT_TYPE_AUTH_CODE.
     *
     * @param $code
     * Authorization code to be stored.
     * @param $client_id
     * Client identifier to be stored.
     * @param $user_id
     * User identifier to be stored.
     * @param string $redirect_uri
     * Redirect URI(s) to be stored in a space-separated string.
     * @param int $expires
     * Expiration to be stored as a Unix timestamp.
     * @param string $scope
     * (optional) Scopes to be stored in space-separated string.
     *
     * @ingroup oauth2_section_4
     */
    public function setAuthorizationCode($code, $client_id, $user_id, $redirect_uri, $expires, $scope = null, $id_token = null)
    {
        if( $id_token )
        {
            return $this->setAuthorizationCodeWithIdToken($code, $client_id, $user_id, $redirect_uri, $expires, $scope, $id_token);
        }

        // convert expires to datestring
        $expires = date('Y-m-d H:i:s', $expires);

        if ($this->getAuthorizationCode($code)) {
            $stmt = sprintf('UPDATE `%s` SET `client_id`=\'%s\', `expires`=\'%s\', `user_id`=\'%s\', `scope`=\'%s\', `redirect_uri`=\'%s\' WHERE `authorization_code`=\'%s\'',
                $this->config['code_table'],
                \Claroline::getDatabase()->escape($client_id),
                \Claroline::getDatabase()->escape($expires),
                \Claroline::getDatabase()->escape($user_id),
                \Claroline::getDatabase()->escape($scope),
                \Claroline::getDatabase()->escape($redirect_uri),
                \Claroline::getDatabase()->escape($code)
            );
        } else {
            $stmt = sprintf('INSERT INTO `%s` (`access_token`, `client_id`, `expires`, `user_id`, `scope`, `redirect_uri`) VALUES (\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')',
                $this->config['code_table'],
                \Claroline::getDatabase()->escape($code),
                \Claroline::getDatabase()->escape($client_id),
                \Claroline::getDatabase()->escape($expires),
                \Claroline::getDatabase()->escape($user_id),
                \Claroline::getDatabase()->escape($scope),
                \Claroline::getDatabase()->escape($redirect_uri)
            );
        }

        return Claroline::getDatabase()->exec($stmt);
    }

    private function setAuthorizationCodeWithIdToken($code, $client_id, $user_id, $redirect_uri, $expires, $scope = null, $id_token = null)
    {
        // convert expires to datestring
        $expires = date('Y-m-d H:i:s', $expires);

        if ($this->getAuthorizationCode($code)) {
            $stmt = sprintf('UPDATE `%s` SET `client_id`=\'%s\', `expires`=\'%s\', `user_id`=\'%s\', `scope`=\'%s\', `redirect_uri`=\'%s\', `id_token`=\'%s\' WHERE `authorization_code`=\'%s\'',
                $this->config['code_table'],
                \Claroline::getDatabase()->escape($client_id),
                \Claroline::getDatabase()->escape($expires),
                \Claroline::getDatabase()->escape($user_id),
                \Claroline::getDatabase()->escape($scope),
                \Claroline::getDatabase()->escape($redirect_uri),
                \Claroline::getDatabase()->escape($id_token),
                \Claroline::getDatabase()->escape($code)
            );
        } else {
            $stmt = sprintf('INSERT INTO `%s` (`access_token`, `client_id`, `expires`, `user_id`, `scope`, `redirect_uri`, `id_token`) VALUES (\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')',
                $this->config['code_table'],
                \Claroline::getDatabase()->escape($code),
                \Claroline::getDatabase()->escape($client_id),
                \Claroline::getDatabase()->escape($expires),
                \Claroline::getDatabase()->escape($user_id),
                \Claroline::getDatabase()->escape($scope),
                \Claroline::getDatabase()->escape($redirect_uri),
                \Claroline::getDatabase()->escape($id_token)
            );
        }

        return Claroline::getDatabase()->exec($stmt);
    }

    /**
     * once an Authorization Code is used, it must be exipired
     *
     * @see http://tools.ietf.org/html/rfc6749#section-4.1.2
     *
     *    The client MUST NOT use the authorization code
     *    more than once.  If an authorization code is used more than
     *    once, the authorization server MUST deny the request and SHOULD
     *    revoke (when possible) all tokens previously issued based on
     *    that authorization code
     *
     */
    public function expireAuthorizationCode($code)
    {
        $stmt = sprintf('DELETE FROM `%s` WHERE `authorization_code` = \'%s\';', $this->config['code_table'], \Claroline::getDatabase()->escape($code));

        return \Claroline::getDatabase()->exec($stmt);
    }

    /**
     * Make sure that the client credentials is valid.
     *
     * @param $client_id
     * Client identifier to be check with.
     * @param $client_secret
     * (optional) If a secret is required, check that they've given the right one.
     *
     * @return
     * TRUE if the client credentials are valid, and MUST return FALSE if it isn't.
     * @endcode
     *
     * @see http://tools.ietf.org/html/rfc6749#section-3.1
     *
     * @ingroup oauth2_section_3
     */
    public function checkClientCredentials($client_id, $client_secret = null)
    {
        $stmt = sprintf('SELECT * from `%s` where `client_id` = \'%s\'',
            $this->config['client_table'],
            \Claroline::getDatabase()->escape($client_id)
        );

        $result = \Claroline::getDatabase()->query($stmt);
        $result = $result->fetch();

        return $result && $result['client_secret'] == $client_secret;
    }

    /**
     * Determine if the client is a "public" client, and therefore
     * does not require passing credentials for certain grant types
     *
     * @param $client_id
     * Client identifier to be check with.
     *
     * @return
     * TRUE if the client is public, and FALSE if it isn't.
     * @endcode
     *
     * @see http://tools.ietf.org/html/rfc6749#section-2.3
     * @see https://github.com/bshaffer/oauth2-server-php/issues/257
     *
     * @ingroup oauth2_section_2
     */
    public function isPublicClient($client_id)
    {
        $stmt = sprintf('SELECT * FROM `%s` WHERE `client_id` = \'%s\';',
            $this->config['client_table'],
            \Claroline::getDatabase()->escape($client_id)
        );

        $result = \Claroline::getDatabase()->query($stmt);
        if (!$result->isEmpty()) {
            $result = $result->fetch();
            return empty($result['client_secret']);
        }

        return false;
    }

    /**
     * Get client details corresponding client_id.
     *
     * OAuth says we should store request URIs for each registered client.
     * Implement this function to grab the stored URI for a given client id.
     *
     * @param $client_id
     * Client identifier to be check with.
     *
     * @return array
     * Client details. The only mandatory key in the array is "redirect_uri".
     * This function MUST return FALSE if the given client does not exist or is
     * invalid. "redirect_uri" can be space-delimited to allow for multiple valid uris.
     * @code
     * return array(
     *     "redirect_uri" => REDIRECT_URI,      // REQUIRED redirect_uri registered for the client
     *     "client_id"    => CLIENT_ID,         // OPTIONAL the client id
     *     "grant_types"  => GRANT_TYPES,       // OPTIONAL an array of restricted grant types
     *     "user_id"      => USER_ID,           // OPTIONAL the user identifier associated with this client
     *     "scope"        => SCOPE,             // OPTIONAL the scopes allowed for this client
     * );
     * @endcode
     *
     * @ingroup oauth2_section_4
     */
    public function getClientDetails($client_id)
    {
        $stmt = sprintf('SELECT * FROM `%s` WHERE `client_id` = \'%s\';',
            $this->config['client_table'],
            \Claroline::getDatabase()->escape($client_id)
        );
        $result = \Claroline::getDatabase()->query($stmt);
        return $result->fetch();
    }

    /**
     * Get the scope associated with this client
     *
     * @return
     * STRING the space-delineated scope list for the specified client_id
     */
    public function getClientScope($client_id)
    {
        if (!$clientDetails = $this->getClientDetails($client_id)) {
            return false;
        }

        if (isset($clientDetails['scope'])) {
            return $clientDetails['scope'];
        }

        return null;
    }

    /**
     * Check restricted grant types of corresponding client identifier.
     *
     * If you want to restrict clients to certain grant types, override this
     * function.
     *
     * @param $client_id
     * Client identifier to be check with.
     * @param $grant_type
     * Grant type to be check with
     *
     * @return
     * TRUE if the grant type is supported by this client identifier, and
     * FALSE if it isn't.
     *
     * @ingroup oauth2_section_4
     */
    public function checkRestrictedGrantType($client_id, $grant_type)
    {
        $details = $this->getClientDetails($client_id);
        if (isset($details['grant_types'])) {
            $grant_types = explode(' ', $details['grant_types']);

            return in_array($grant_type, (array) $grant_types);
        }

        // if grant_types are not defined, then none are restricted
        return true;
    }

    /**
     * Get the public key associated with a client_id
     *
     * @param $client_id
     * Client identifier to be checked with.
     *
     * @return
     * STRING Return the public key for the client_id if it exists, and MUST return FALSE if it doesn't.
     */
    public function getClientKey($client_id, $subject)
    {
        $stmt = sprintf('SELECT `public_key` FROM `%s` WHERE `client_id` = \'%s\' AND `subject` = \'%s\';',
            $this->config['jwt_table'],
            \Claroline::getDatabase()->escape($client_id),
            \Claroline::getDatabase()->escape($subject)
        );

        $result = \Claroline::getDatabase()->query($stmt);
        return $result->fetch(\Database_ResultSet::FETCH_COLUMN);
    }

    /**
     * Get a jti (JSON token identifier) by matching against the client_id, subject, audience and expiration.
     *
     * @param $client_id
     * Client identifier to match.
     *
     * @param $subject
     * The subject to match.
     *
     * @param $audience
     * The audience to match.
     *
     * @param $expiration
     * The expiration of the jti.
     *
     * @param $jti
     * The jti to match.
     *
     * @return
     * An associative array as below, and return NULL if the jti does not exist.
     * - issuer: Stored client identifier.
     * - subject: Stored subject.
     * - audience: Stored audience.
     * - expires: Stored expiration in unix timestamp.
     * - jti: The stored jti.
     */
    public function getJti($client_id, $subject, $audience, $expiration, $jti)
    {
        $stmt = sprintf('SELECT `public_key` FROM `%s` WHERE `issuer` = \'%s\' AND `subject` = \'%s\' AND `audience` = \'%s\' AND `expires` = \'%s\' AND `jti` = \'%s\';',
            $this->config['jti_table'],
            \Claroline::getDatabase()->escape($client_id),
            \Claroline::getDatabase()->escape($subject),
            \Claroline::getDatabase()->escape($audience),
            \Claroline::getDatabase()->escape($expiration),
            \Claroline::getDatabase()->escape($jti)
        );

        $result = \Claroline::getDatabase()->query($stmt);
        if( !$result->isEmpty() )
        {
            return $result->fetch();
        }

        return null;
    }

    /**
     * Store a used jti so that we can check against it to prevent replay attacks.
     * @param $client_id
     * Client identifier to insert.
     *
     * @param $subject
     * The subject to insert.
     *
     * @param $audience
     * The audience to insert.
     *
     * @param $expiration
     * The expiration of the jti.
     *
     * @param $jti
     * The jti to insert.
     */
    public function setJti($client_id, $subject, $audience, $expiration, $jti)
    {
        $stmt = sprintf('INSERT INTO `%s` (`issuer`, `subject`, `audience`, `expires`, `jti`) VALUES (\'%s\', \'%s\', \'%s\', \'%s\', \'%s\')',
            $this->config['jti_table'],
            \Claroline::getDatabase()->escape($client_id),
            \Claroline::getDatabase()->escape($subject),
            \Claroline::getDatabase()->escape($audience),
            \Claroline::getDatabase()->escape($expiration),
            \Claroline::getDatabase()->escape($jti)
        );

        return \Claroline::getDatabase()->exec($stmt);
    }

    public function getPublicKey($client_id = null)
    {
        $stmt = sprintf('SELECT `public_key` FROM `%s` WHERE `client_id` = \'%s\' OR `client_id` IS NULL ORDER BY `client_id` IS NOT NULL DESC;',
            $this->config['public_key_table'],
            \Claroline::getDatabase()->escape($client_id)
        );

        $result = \Claroline::getDatabase()->query($stmt);
        if (!$result->isEmpty()) {
            $key = $result->fetch();
            return $key['public_key'];
        }
        return null;
    }

    public function getPrivateKey($client_id = null)
    {
        $stmt = sprintf('SELECT `private_key` FROM `%s` WHERE `client_id` = \'%s\' OR `client_id` IS NULL ORDER BY `client_id` IS NOT NULL DESC;',
            $this->config['public_key_table'],
            \Claroline::getDatabase()->escape($client_id)
        );

        $result = \Claroline::getDatabase()->query($stmt);
        if (!$result->isEmpty()) {
            $key = $result->fetch();
            return $key['private_key'];
        }
        return null;
    }

    public function getEncryptionAlgorithm($client_id = null)
    {
        $stmt = sprintf('SELECT `encryption_algorithm` FROM `%s` WHERE `client_id` = \'%s\' OR `client_id` IS NULL ORDER BY `client_id` IS NOT NULL DESC;',
            $this->config['public_key_table'],
            \Claroline::getDatabase()->escape($client_id)
        );

        $result = \Claroline::getDatabase()->query($stmt);
        if (!$result->isEmpty()) {
            $key = $result->fetch();
            return $key['encryption_algorithm'];
        }
        return 'RS256';
    }

    /**
     * Grant refresh access tokens.
     *
     * Retrieve the stored data for the given refresh token.
     *
     * Required for OAuth2::GRANT_TYPE_REFRESH_TOKEN.
     *
     * @param $refresh_token
     * Refresh token to be check with.
     *
     * @return
     * An associative array as below, and NULL if the refresh_token is
     * invalid:
     * - refresh_token: Refresh token identifier.
     * - client_id: Client identifier.
     * - user_id: User identifier.
     * - expires: Expiration unix timestamp, or 0 if the token doesn't expire.
     * - scope: (optional) Scope values in space-separated string.
     *
     * @see http://tools.ietf.org/html/rfc6749#section-6
     *
     * @ingroup oauth2_section_6
     */
    public function getRefreshToken($refresh_token)
    {
        $stmt = sprintf('SELECT * FROM `%s` WHERE `refresh_token` = \'%s\';',
            $this->config['refresh_token_table'],
            \Claroline::getDatabase()->escape($refresh_token)
        );
        $result = \Claroline::getDatabase()->query($stmt);
        if (!$result->isEmpty()) {
            $token = $result->fetch();
            $token['expires'] = strtotime($token['expires']);
            return $token;
        }

        return null;
    }

    /**
     * Take the provided refresh token values and store them somewhere.
     *
     * This function should be the storage counterpart to getRefreshToken().
     *
     * If storage fails for some reason, we're not currently checking for
     * any sort of success/failure, so you should bail out of the script
     * and provide a descriptive fail message.
     *
     * Required for OAuth2::GRANT_TYPE_REFRESH_TOKEN.
     *
     * @param $refresh_token
     * Refresh token to be stored.
     * @param $client_id
     * Client identifier to be stored.
     * @param $user_id
     * User identifier to be stored.
     * @param $expires
     * Expiration timestamp to be stored. 0 if the token doesn't expire.
     * @param $scope
     * (optional) Scopes to be stored in space-separated string.
     *
     * @ingroup oauth2_section_6
     */
    public function setRefreshToken($refresh_token, $client_id, $user_id, $expires, $scope = null)
    {
        $stmt = sprintf('INSERT INTO `%s` (`refresh_token`, `client_id`, `user_id`, `expires`, `scope`) VALUES (\'%s\', \'%s\', \'%s\', \'%s\', \'%s\')',
            $this->config['refresh_token_table'],
            \Claroline::getDatabase()->escape($refresh_token),
            \Claroline::getDatabase()->escape($client_id),
            \Claroline::getDatabase()->escape($user_id),
            \Claroline::getDatabase()->escape($expires),
            \Claroline::getDatabase()->escape($scope)
        );

        return \Claroline::getDatabase()->exec($stmt);
    }

    /**
     * Expire a used refresh token.
     *
     * This is not explicitly required in the spec, but is almost implied.
     * After granting a new refresh token, the old one is no longer useful and
     * so should be forcibly expired in the data store so it can't be used again.
     *
     * If storage fails for some reason, we're not currently checking for
     * any sort of success/failure, so you should bail out of the script
     * and provide a descriptive fail message.
     *
     * @param $refresh_token
     * Refresh token to be expirse.
     *
     * @ingroup oauth2_section_6
     */
    public function unsetRefreshToken($refresh_token)
    {
        $stmt = sprintf('DELETE FROM `%s` WHERE `refresh_token` = \'%s\';',
            $this->config['refresh_token_table'],
            \Claroline::getDatabase()->escape($refresh_token)
        );

        return \Claroline::getDatabase()->exec($stmt);
    }

    /**
     * Check if the provided scope exists.
     *
     * @param $scope
     * A space-separated string of scopes.
     *
     * @return
     * TRUE if it exists, FALSE otherwise.
     */
    public function scopeExists($scope)
    {
        $scope = explode(' ', \Claroline::getDatabase()->escape($scope));
        $whereIn = implode(',', array_fill(0, count($scope), '?'));
        $stmt = sprintf('SELECT count(scope) as count FROM `%s` WHERE `scope` IN (%s)',
            $this->config['scope_table'],
            $whereIn
        );
        $result = \Claroline::getDatabase()->query($stmt);

        if ( !$result->isEmpty() )
        {
            $result = $result->fetch();
            return $result['count'] == count($scope);
        }

        return false;
    }

    /**
     * The default scope to use in the event the client
     * does not request one. By returning "false", a
     * request_error is returned by the server to force a
     * scope request by the client. By returning "null",
     * opt out of requiring scopes
     *
     * @param $client_id
     * An optional client id that can be used to return customized default scopes.
     *
     * @return
     * string representation of default scope, NULL if
     * scopes are not defined, or false to force scope
     * request by the client
     *
     * ex:
     *     'default'
     * ex:
     *     null
     */
    public function getDefaultScope($client_id = null)
    {
        $stmt = sprintf('SELECT `scope` FROM `%s` WHERE `is_default`=%s',
            $this->config['scope_table'],
            true
        );

        $result = \Claroline::getDatabase()->query($stmt);

        if ( !$result->isEmpty() )
        {
            $result = $result->fetch();
            $defaultScope = array_map(function ($row) {
                return $row['scope'];
            }, $result);

            return implode(' ', $defaultScope);
        }

        return null;
    }
}