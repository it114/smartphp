<?php
//接口设计规范：https://github.com/Dreampie/http-api-design-ZH_CN
//
//如何处理版本号问题：
///http://ningandjiao.iteye.com/blog/1990004
//http://stackoverflow.com/questions/2024600/rest-api-versioning-only-version-the-representation-not-the-resource-itself?lq=1
//http://stackoverflow.com/questions/10742594/versioning-rest-api?lq=1
//本期先把version放在url中，并且是查询构造参数中，不走rest格式
//其实可以这样理解，每一个接口版本对应一个目录，每一个目录对应于一个后台git代码管理的tag，这样就好弄了，在代码层面每次只开发最新版本的代码就行了。

//jwt中的sub iss等的含义
//好文http://www.sitepoint.com/php-authorization-jwt-json-web-tokens/
//http://www-01.ibm.com/support/knowledgecenter/SSEQTP_8.5.5/com.ibm.websphere.wlp.doc/ae/cwlp_jwttoken.html
/**
 * Claims in a JSON Web Token
A valid JSON Web Token must be signed. A Liberty profile server that is configured as an OpenID Connect Provider only supports HMAC-SHA256 as the token signing algorithm. The signing key for each OpenID Connect Client is the secret attribute in the client configuration of the OpenID Connect Provider. In the example that is shown, the signing key that is used would be "{xor}LDo8LTor".

<client name="client01" displayname="client01" secret="{xor}LDo8LTor" ... />
The OpenID Connect Provider also verifies the following claims in a JWT:

'iss' (issuer)发行者
This claim is required in a JWT. The iss claim must match the name attribute or the redirect attribute of the client configuration in the OpenID Connect Provider. In the following example, the iss claim must match either client01 or http://op201406.ibm.com:8010/oauthclient/redirect.jsp.
<client name="client01" redirect="http://op201406.ibm.com:8010/oauthclient/redirect.jsp" scope="openid profile email" ... />
'sub' (subject)主题
This claim is required in a JWT. The value of the subject must be a valid user name in the user registry of the OpenID Connect Provider server.
'aud' (audience)听众
This claim is required in a JWT. The value of the audience claim is the name of the issuerIdentifier when the issuerIdentifier attribute is specified in the openidConnectProvider configuration. If the issuerIdentifier attribute is not specified in the openidConnectProvider configuration, the audience must be the token endpoint URI of the OpenID Connect Provider. In the following example, the value of the audience claim would be "OpenIDConnectProviderID1".
<openidConnectProvider id="OidcConfigSample" oauthProviderRef="OAuthConfigSample" issuerIdentifier="OpenIDConnectProviderID1" />
'exp' (expiration)过期
This claim is required in a JWT and limits the time window that the JWT can be used. The OpenID Connect Provider verifies the exp against its system clock, plus some allowable clock skew.
'nbf' (not before)
This is an optional claim. When present, the token is only valid after the time specified by this claim. The OpenID Connect Provider verifies this time against its system clock, plus some allowable clock skew.
'iat' (issued at)可选的发行时间
By default, this is an optional claim. However, if the iatRequired attribute of thejwtGrantType element is set to true, then all JWTs are required to contain the iat claim. When present, the iat claim indicates the time at which the JWT was issued. A JWT cannot be issued longer than the maxTokenLifetime.
'jti' (JWT ID)
This is an optional claim and is the unique identifier of a JWT Token. When present, the same JWT ID cannot be reused by an issuer. For example, if client01 issues a JWT whose jti is id6098364921, then no other JWT issued by client01 can have a jti value of id6098364921. A JWT with a jti claim identical to another JWT is considered to be a replay attack. Liberty profile servers that are configured as OpenID Connect Providers set up a jti cache on the server. The size of the cache is specified by the maxJtiCacheSize in the jwtGrantType configuration. The jti IDs that are kept in the cache are checked against any new incoming jti ID. The jti IDs stored in the cache are not discarded unless the cache is full.
 */