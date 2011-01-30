<?php
namespace Pok\Component\Remote;

use Symfony\Component\BrowserKit\Client,
    Symfony\Component\BrowserKit\History,
    Symfony\Component\BrowserKit\CookieJar,
    Symfony\Component\BrowserKit\Response;

class CurlClient extends Client
{
    protected $curl;

    public function __construct(array $server = array(), History $history = null, CookieJar $cookieJar = null)
    {
        parent::__construct($server, $history, $cookieJar);
        $this->curl = curl_init();
    }

    /**
     * Makes a request.
     *
     * @param Request $request A Request instance
     *
     * @return Response A Response instance
     */
    protected function doRequest($request)
    {
        curl_setopt($this->curl, CURLOPT_URL, $request->getUri());
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $request->getParameters());
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $request->getMethod());
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);

        $content = curl_exec($this->curl);

        $this->assertCurlError();

        $requestInfo = curl_getinfo($this->curl);

        return new Response($content, $requestInfo['http_code']);
    }

    public function assertCurlError()
    {
        if (curl_errno($this->curl)) {
            throw new \RuntimeException(curl_error($this->curl));
        }
    }

    public function __destruct()
    {
        curl_close($this->curl);
    }
}
