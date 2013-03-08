twitter-api-php
===============

Simple PHP Wrapper for Twitter API v1.1 calls

Using this wrapper, API calls can be performed using only a few parameters.

Instructions:

- [x] Create a twitter app: https://dev.twitter.com/apps/, enable read/write access in apps, and grab your access tokens.
- [x] Choose a URL to make a request to from: https://dev.twitter.com/docs/api/1.1/ (example https://api.twitter.com/1.1/blocks/create.json)
- [x] Choose the corresponding request method for the URL above (either GET or POST)
- [x] Choose your postfields (example 'screen_name' => 'usernameToBlock')

Put this data into the following format and use the following code example (contained in index.php)

```php
require_once('TwitterAPIExchange.php');

/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
$settings = array(
    'oauth_access_token' => "YOUR_OAUTH_ACCESS_TOKEN",
    'oauth_access_token_secret' => "YOUR_OAUTH_ACCESS_TOKEN_SECRET",
    'consumer_key' => "YOUR_CONSUMER_KEY",
    'consumer_secret' => "YOUR_CONSUMER_SECRET"
);

/** URL for REST request, see: https://dev.twitter.com/docs/api/1.1/ **/
$url = 'https://api.twitter.com/1.1/blocks/create.json';
$requestMethod = 'POST';

/** POST fields required by the URL above. See relevant docs as above **/
$postfields = array(
    'screen_name' => 'usernameToBlock', 
    'skip_status' => '1'
);

/** Perform the request and echo the response **/
$twitter = new TwitterAPIExchange($settings);
echo $twitter->buildOauth($url, $requestMethod)
         ->setPostfields($postfields)
         ->performRequest();
```

And that's it!
