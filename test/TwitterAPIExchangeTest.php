<?php

/**
 * Class TwitterAPIExchangeTest
 *
 * Contains ALL the integration tests
 *
 * @note This test account is not actively monitored so you gain nothing by hi-jacking it :-)
 */
class TwitterAPIExchangeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    const CONSUMER_KEY = 'VXD22AD9kcNyNgsfW6cwkWRkw';

    /**
     * @var string
     */
    const CONSUMER_SECRET = 'y0k3z9Y46V0DMAKGe4Az2aDtqNt9aXjg3ssCMCldUheGBT0YL9';

    /**
     * @var string
     */
    const OAUTH_ACCESS_TOKEN = '3232926711-kvMvNK5mFJlUFzCdtw3ryuwZfhIbLJtPX9e8E3Y';

    /**
     * @var string
     */
    const OAUTH_ACCESS_TOKEN_SECRET = 'EYrFp0lfNajBslYV3WgAGmpHqYZvvNxP5uxxSq8Dbs1wa';

    /**
     * @var \TwitterAPIExchange
     */
    protected $exchange;

    /**
     * @var int Stores a tweet id (for /update) to be deleted later (by /destroy)
     */
    private static $tweetId;

    /**
     * @var int Stores uploaded media id
     */
    private static $mediaId;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $settings  = [];

        /** Because I'm lazy... **/
        $reflector = new \ReflectionClass($this);

        foreach ($reflector->getConstants() as $key => $value)
        {
            $settings[strtolower($key)] = $value;
        }

        $this->exchange = new \TwitterAPIExchange($settings);
    }

    /**
     * GET statuses/mentions_timeline
     *
     * @see https://dev.twitter.com/rest/reference/get/statuses/mentions_timeline
     */
    public function testStatusesMentionsTimeline()
    {
        $this->markTestSkipped();
        $url    = 'https://api.twitter.com/1.1/statuses/mentions_timeline.json';
        $method = 'GET';
        $params = '?max_id=595150043381915648';

        $data     = $this->exchange->request($url, $method, $params);
        $expected = "@j7php Test mention";

        $this->assertContains($expected, $data);
    }

    /**
     * GET statuses/user_timeline
     *
     * @see https://dev.twitter.com/rest/reference/get/statuses/user_timeline
     */
    public function testStatusesUserTimeline()
    {
        $this->markTestSkipped();
        $url    = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $method = 'GET';
        $params = '?user_id=3232926711';

        $data     = $this->exchange->request($url, $method, $params);
        $expected = "Test Tweet";

        $this->assertContains($expected, $data);
    }

    /**
     * GET statuses/home_timeline
     *
     * @see https://dev.twitter.com/rest/reference/get/statuses/home_timeline
     */
    public function testStatusesHomeTimeline()
    {
        $this->markTestSkipped();
        $url    = 'https://api.twitter.com/1.1/statuses/home_timeline.json';
        $method = 'GET';
        $params = '?user_id=3232926711';

        $data     = $this->exchange->request($url, $method, $params);
        $expected = "Test Tweet";

        $this->assertContains($expected, $data);
    }

    /**
     * GET statuses/retweets_of_me
     *
     * @see https://dev.twitter.com/rest/reference/get/statuses/retweets_of_me
     */
    public function testStatusesRetweetsOfMe()
    {
        $this->markTestSkipped();
        $url    = 'https://api.twitter.com/1.1/statuses/retweets_of_me.json';
        $method = 'GET';

        $data     = $this->exchange->request($url, $method);
        $expected = 'travis CI and tests';

        $this->assertContains($expected, $data);
    }

    /**
     * GET statuses/retweets/:id
     *
     * @see https://api.twitter.com/1.1/statuses/retweets/:id.json
     */
    public function testStatusesRetweetsOfId()
    {
        $this->markTestSkipped();
        $url    = 'https://api.twitter.com/1.1/statuses/retweets/595155660494471168.json';
        $method = 'GET';

        $data     = $this->exchange->request($url, $method);
        $expected = 'travis CI and tests';

        $this->assertContains($expected, $data);
    }

    /**
     * GET Statuses/Show/:id
     *
     * @see https://dev.twitter.com/rest/reference/get/statuses/show/:id
     */
    public function testStatusesShowId()
    {
        $this->markTestSkipped();
        $url    = 'https://api.twitter.com/1.1/statuses/show.json';
        $method = 'GET';
        $params = '?id=595155660494471168';

        $data     = $this->exchange->request($url, $method, $params);
        $expected = 'travis CI and tests';

        $this->assertContains($expected, $data);
    }

    /**
     * POST media/upload
     *
     * @see https://dev.twitter.com/rest/reference/post/media/upload
     *
     * @note Uploaded unattached media files will be available for attachment to a tweet for 60 minutes
     */
    public function testMediaUpload()
    {
        /**
         *=========
         * =========
         *  This test is currently failing because media/upload doesn't work yet
         * =========
         *=========
         */
        $file = file_get_contents(__DIR__ . '/img.png');
        $data = base64_encode($file);

        $url    = 'https://upload.twitter.com/1.1/media/upload.json';
        $method = 'POST';
        $params = [
            'media_data' => $data
        ];

        $data     = $this->exchange->request($url, $method, $params);
        $expected = 'image/png';

        $this->assertContains($expected, $data);

        /** Store the media id for later **/
        $data = @json_decode($data, true);

        $this->assertArrayHasKey('media_id', is_array($data) ? $data : []);

        self::$mediaId = $data['media_id'];

        var_dump(self::$mediaId);
    }

    /**
     * POST statuses/update
     *
     * @see https://dev.twitter.com/rest/reference/post/statuses/update
     */
    public function testStatusesUpdate()
    {
        $this->markTestSkipped();
        if (!self::$mediaId)
        {
            $this->fail('Cannot /update status because /upload failed');
        }

        $url    = 'https://api.twitter.com/1.1/statuses/update.json';
        $method = 'POST';
        $params = [
            'status' => 'TEST TWEET TO BE DELETED' . rand()
        ];

        $data     = $this->exchange->request($url, $method, $params);
        $expected = 'TEST TWEET TO BE DELETED';

        $this->assertContains($expected, $data);

        /** Store the tweet id for testStatusesDestroy() **/
        $data = @json_decode($data, true);

        $this->assertArrayHasKey('id_str', is_array($data) ? $data : []);

        self::$tweetId = $data['id_str'];

        /** We've done this now, yay **/
        self::$mediaId = null;
    }

    /**
     * POST statuses/destroy/:id
     *
     * @see https://dev.twitter.com/rest/reference/post/statuses/destroy/%3Aid
     */
    public function testStatusesDestroy()
    {
        $this->markTestSkipped();
        if (!self::$tweetId)
        {
            $this->fail('Cannot /destroy status because /update failed');
        }

        $url    = sprintf('https://api.twitter.com/1.1/statuses/destroy/%d.json', self::$tweetId);
        $method = 'POST';
        $params = [
            'id' => self::$tweetId
        ];

        $data     = $this->exchange->request($url, $method, $params);
        $expected = 'TEST TWEET TO BE DELETED';

        $this->assertContains($expected, $data);

        /** We've done this now, yay **/
        self::$tweetId = null;
    }
}