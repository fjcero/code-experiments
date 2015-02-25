<?php 

$method   = "get";
$endpoint = "https://api.twitter.com/1.1/users/search.json";
$query    = [
    'q' => 'fjcero',
];

// Required Keys
$consumer_key       = 'your-consumer-key';
$consumer_secret    = 'your-consumer-secret-key';
$oauth_token        = "the-oauth-token";
$oauth_token_secret = "the-oauth-token-secret";

// Lets follow the instructions of Twitter to generate a Signature:
// The hard way: https://dev.twitter.com/oauth/overview/creating-signatures
// The easy way: https://dev.twitter.com/oauth/tools/signature-generator/7964289

$body = [
    'oauth_consumer_key'     => $consumer_key,
    'oauth_nonce'            => md5(microtime() . mt_rand()),
    'oauth_signature_method' => 'HMAC-SHA1',
    'oauth_timestamp'        => time(),
    'oauth_version'          => '1.0',
    'oauth_token'            => $oauth_token,
];

$params = array_unique(array_merge($query, $body));
ksort($params);
$params = http_build_query($params, null, '&', PHP_QUERY_RFC3986);

$signature = base64_encode( 
    hash_hmac(
        'SHA1', 
        strtoupper($method).'&'.urlencode($endpoint).'&'.urlencode($params),
        urlencode($consumer_secret).'&'.urlencode($oauth_token_secret),
        true
    )
);

$body['oauth_signature'] = $signature;
$authorization = http_build_query($body, null, ', ');

// This output could be used in CLI
echo vsprintf('curl --%s \'%s\' --data \'%s\' --header \'Authorization: OAuth %s\' --verbose', [ 
    $method,
    $endpoint,
    http_build_query($query, null, '&'),
    $authorization
]);