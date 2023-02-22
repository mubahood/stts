<?php
// This file was auto-generated from sdk-root/src/data/sso-oidc/2019-06-10/api-2.json
return [ 'version' => '2.0', 'metadata' => [ 'apiVersion' => '2019-06-10', 'endpointPrefix' => 'oidc', 'jsonVersion' => '1.1', 'protocol' => 'rest-json', 'serviceAbbreviation' => 'SSO OIDC', 'serviceFullName' => 'AWS SSO OIDC', 'serviceId' => 'SSO OIDC', 'signatureVersion' => 'v4', 'signingName' => 'awsssooidc', 'uid' => 'sso-oidc-2019-06-10', ], 'operations' => [ 'CreateToken' => [ 'name' => 'CreateToken', 'http' => [ 'method' => 'POST', 'requestUri' => '/token', ], 'input' => [ 'shape' => 'CreateTokenRequest', ], 'output' => [ 'shape' => 'CreateTokenResponse', ], 'errors' => [ [ 'shape' => 'InvalidRequestException', ], [ 'shape' => 'InvalidClientException', ], [ 'shape' => 'InvalidGrantException', ], [ 'shape' => 'UnauthorizedClientException', ], [ 'shape' => 'UnsupportedGrantTypeException', ], [ 'shape' => 'InvalidScopeException', ], [ 'shape' => 'AuthorizationPendingException', ], [ 'shape' => 'SlowDownException', ], [ 'shape' => 'AccessDeniedException', ], [ 'shape' => 'ExpiredTokenException', ], [ 'shape' => 'InternalServerException', ], ], 'authtype' => 'none', ], 'RegisterClient' => [ 'name' => 'RegisterClient', 'http' => [ 'method' => 'POST', 'requestUri' => '/client/register', ], 'input' => [ 'shape' => 'RegisterClientRequest', ], 'output' => [ 'shape' => 'RegisterClientResponse', ], 'errors' => [ [ 'shape' => 'InvalidRequestException', ], [ 'shape' => 'InvalidScopeException', ], [ 'shape' => 'InvalidClientMetadataException', ], [ 'shape' => 'InternalServerException', ], ], 'authtype' => 'none', ], 'StartDeviceAuthorization' => [ 'name' => 'StartDeviceAuthorization', 'http' => [ 'method' => 'POST', 'requestUri' => '/device_authorization', ], 'input' => [ 'shape' => 'StartDeviceAuthorizationRequest', ], 'output' => [ 'shape' => 'StartDeviceAuthorizationResponse', ], 'errors' => [ [ 'shape' => 'InvalidRequestException', ], [ 'shape' => 'InvalidClientException', ], [ 'shape' => 'UnauthorizedClientException', ], [ 'shape' => 'SlowDownException', ], [ 'shape' => 'InternalServerException', ], ], 'authtype' => 'none', ], ], 'shapes' => [ 'AccessDeniedException' => [ 'type' => 'structure', 'members' => [ 'error' => [ 'shape' => 'Error', ], 'error_description' => [ 'shape' => 'ErrorDescription', ], ], 'error' => [ 'httpStatusCode' => 400, ], 'exception' => true, ], 'AccessToken' => [ 'type' => 'string', ], 'AuthCode' => [ 'type' => 'string', ], 'AuthorizationPendingException' => [ 'type' => 'structure', 'members' => [ 'error' => [ 'shape' => 'Error', ], 'error_description' => [ 'shape' => 'ErrorDescription', ], ], 'error' => [ 'httpStatusCode' => 400, ], 'exception' => true, ], 'ClientId' => [ 'type' => 'string', ], 'ClientName' => [ 'type' => 'string', ], 'ClientSecret' => [ 'type' => 'string', ], 'ClientType' => [ 'type' => 'string', ], 'CreateTokenRequest' => [ 'type' => 'structure', 'required' => [ 'clientId', 'clientSecret', 'grantType', ], 'members' => [ 'clientId' => [ 'shape' => 'ClientId', ], 'clientSecret' => [ 'shape' => 'ClientSecret', ], 'grantType' => [ 'shape' => 'GrantType', ], 'deviceCode' => [ 'shape' => 'DeviceCode', ], 'code' => [ 'shape' => 'AuthCode', ], 'refreshToken' => [ 'shape' => 'RefreshToken', ], 'scope' => [ 'shape' => 'Scopes', ], 'redirectUri' => [ 'shape' => 'URI', ], ], ], 'CreateTokenResponse' => [ 'type' => 'structure', 'members' => [ 'accessToken' => [ 'shape' => 'AccessToken', ], 'tokenType' => [ 'shape' => 'TokenType', ], 'expiresIn' => [ 'shape' => 'ExpirationInSeconds', ], 'refreshToken' => [ 'shape' => 'RefreshToken', ], 'idToken' => [ 'shape' => 'IdToken', ], ], ], 'DeviceCode' => [ 'type' => 'string', ], 'Error' => [ 'type' => 'string', ], 'ErrorDescription' => [ 'type' => 'string', ], 'ExpirationInSeconds' => [ 'type' => 'integer', ], 'ExpiredTokenException' => [ 'type' => 'structure', 'members' => [ 'error' => [ 'shape' => 'Error', ], 'error_description' => [ 'shape' => 'ErrorDescription', ], ], 'error' => [ 'httpStatusCode' => 400, ], 'exception' => true, ], 'GrantType' => [ 'type' => 'string', ], 'IdToken' => [ 'type' => 'string', ], 'InternalServerException' => [ 'type' => 'structure', 'members' => [ 'error' => [ 'shape' => 'Error', ], 'error_description' => [ 'shape' => 'ErrorDescription', ], ], 'error' => [ 'httpStatusCode' => 500, ], 'exception' => true, 'fault' => true, ], 'IntervalInSeconds' => [ 'type' => 'integer', ], 'InvalidClientException' => [ 'type' => 'structure', 'members' => [ 'error' => [ 'shape' => 'Error', ], 'error_description' => [ 'shape' => 'ErrorDescription', ], ], 'error' => [ 'httpStatusCode' => 401, ], 'exception' => true, ], 'InvalidClientMetadataException' => [ 'type' => 'structure', 'members' => [ 'error' => [ 'shape' => 'Error', ], 'error_description' => [ 'shape' => 'ErrorDescription', ], ], 'error' => [ 'httpStatusCode' => 400, ], 'exception' => true, ], 'InvalidGrantException' => [ 'type' => 'structure', 'members' => [ 'error' => [ 'shape' => 'Error', ], 'error_description' => [ 'shape' => 'ErrorDescription', ], ], 'error' => [ 'httpStatusCode' => 400, ], 'exception' => true, ], 'InvalidRequestException' => [ 'type' => 'structure', 'members' => [ 'error' => [ 'shape' => 'Error', ], 'error_description' => [ 'shape' => 'ErrorDescription', ], ], 'error' => [ 'httpStatusCode' => 400, ], 'exception' => true, ], 'InvalidScopeException' => [ 'type' => 'structure', 'members' => [ 'error' => [ 'shape' => 'Error', ], 'error_description' => [ 'shape' => 'ErrorDescription', ], ], 'error' => [ 'httpStatusCode' => 400, ], 'exception' => true, ], 'LongTimeStampType' => [ 'type' => 'long', ], 'RefreshToken' => [ 'type' => 'string', ], 'RegisterClientRequest' => [ 'type' => 'structure', 'required' => [ 'clientName', 'clientType', ], 'members' => [ 'clientName' => [ 'shape' => 'ClientName', ], 'clientType' => [ 'shape' => 'ClientType', ], 'scopes' => [ 'shape' => 'Scopes', ], ], ], 'RegisterClientResponse' => [ 'type' => 'structure', 'members' => [ 'clientId' => [ 'shape' => 'ClientId', ], 'clientSecret' => [ 'shape' => 'ClientSecret', ], 'clientIdIssuedAt' => [ 'shape' => 'LongTimeStampType', ], 'clientSecretExpiresAt' => [ 'shape' => 'LongTimeStampType', ], 'authorizationEndpoint' => [ 'shape' => 'URI', ], 'tokenEndpoint' => [ 'shape' => 'URI', ], ], ], 'Scope' => [ 'type' => 'string', ], 'Scopes' => [ 'type' => 'list', 'member' => [ 'shape' => 'Scope', ], ], 'SlowDownException' => [ 'type' => 'structure', 'members' => [ 'error' => [ 'shape' => 'Error', ], 'error_description' => [ 'shape' => 'ErrorDescription', ], ], 'error' => [ 'httpStatusCode' => 400, ], 'exception' => true, ], 'StartDeviceAuthorizationRequest' => [ 'type' => 'structure', 'required' => [ 'clientId', 'clientSecret', 'startUrl', ], 'members' => [ 'clientId' => [ 'shape' => 'ClientId', ], 'clientSecret' => [ 'shape' => 'ClientSecret', ], 'startUrl' => [ 'shape' => 'URI', ], ], ], 'StartDeviceAuthorizationResponse' => [ 'type' => 'structure', 'members' => [ 'deviceCode' => [ 'shape' => 'DeviceCode', ], 'userCode' => [ 'shape' => 'UserCode', ], 'verificationUri' => [ 'shape' => 'URI', ], 'verificationUriComplete' => [ 'shape' => 'URI', ], 'expiresIn' => [ 'shape' => 'ExpirationInSeconds', ], 'interval' => [ 'shape' => 'IntervalInSeconds', ], ], ], 'TokenType' => [ 'type' => 'string', ], 'URI' => [ 'type' => 'string', ], 'UnauthorizedClientException' => [ 'type' => 'structure', 'members' => [ 'error' => [ 'shape' => 'Error', ], 'error_description' => [ 'shape' => 'ErrorDescription', ], ], 'error' => [ 'httpStatusCode' => 400, ], 'exception' => true, ], 'UnsupportedGrantTypeException' => [ 'type' => 'structure', 'members' => [ 'error' => [ 'shape' => 'Error', ], 'error_description' => [ 'shape' => 'ErrorDescription', ], ], 'error' => [ 'httpStatusCode' => 400, ], 'exception' => true, ], 'UserCode' => [ 'type' => 'string', ], ],];
