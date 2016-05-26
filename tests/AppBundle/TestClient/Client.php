<?php

namespace AppBundle\Tests\AppBundle\TestClient;

use GuzzleHttp\Psr7;
use AppBundle\Tests\AppBundle\Kernel\KernelUtils;
use Psr\Http\Message\UriInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Request;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\ServerRequestFactory;

class Client
{
    protected $config = [];
    protected $customHeaders = [];
    protected $client;
    public function __construct(array $config = [])
    {

        $this->client = new \GuzzleHttp\Client($config);
        if (isset($config['headers'])) {
            $this->customHeaders = $config['headers'];
        }
    }

    public function createRequest($method, $uri = null, array $options = [])
    {
        if (function_exists('xdebug_enable')) {
            xdebug_stop_code_coverage(false);
        }
        $cookies = !empty($options['cookies']) ? $options['cookies'] : [];
        $options = $this->prepareDefaults($options);

        // Remove request modifying parameter because it can be done up-front.
        $headers = isset($options['headers']) ? $options['headers'] : [];
        $body = isset($options['body']) ? $options['body'] : null;
        $version = isset($options['version']) ? $options['version'] : '1.1';
        // Merge the URI into the base URI.
        $psrRequest = new Psr7\Request($method, $this->buildUri($uri, $options), $headers + $options['_conditional'] + $this->customHeaders, $body, $version);

        $m = (new \ReflectionObject($this->client))->getMethod('applyOptions');
        $m->setAccessible(true);
        $psrRequest =  $m->invokeArgs($this->client, [$psrRequest, &$options]);

        $server  = ServerRequestFactory::normalizeServer([
            'REQUEST_METHOD' => $method,
            'CONTENT_TYPE' => $psrRequest->getHeader('Content-Type'),
            'CONTENT_LENGTH' => $psrRequest->getHeader('Content-Length'),
            'SCRIPT_FILENAME' => '/app_dev.php',
            'REQUEST_URI' => $psrRequest->getUri()->getPath(),
            'REQUEST_TIME_FLOAT' => microtime(1),
            'REQUEST_TIME' => time()
        ]);

        parse_str($psrRequest->getUri()->getQuery(), $get);
        if (!empty($options['query'])) {
            $get = array_merge($get, $options['query']);
        }

        $psrServerRequest = new ServerRequest(
            $server,
            [],
            strval($psrRequest->getUri()),
            $psrRequest->getMethod(),
            $psrRequest->getBody(),
            $psrRequest->getHeaders(),
            [],
            $get
        );

        $psrServerRequest = $psrServerRequest
            ->withUri($psrRequest->getUri())
            //->withCookieParams([] ?: $_COOKIE)
            ->withQueryParams($get)
            //    ->withParsedBody($body ?: $_POST)
        ;
        $httpFoundationFactory = new HttpFoundationFactory();
        $symfonyRequest = $httpFoundationFactory->createRequest($psrServerRequest);
        
        foreach ($cookies as $cookie){
            $symfonyRequest->cookies->set($cookie->getname(), $cookie);
        }
        // simulate http auth
        if ($symfonyRequest->headers->has('authorization') && stripos($symfonyRequest->headers->get('authorization'), "Basic ")===0){
            list ($usr, $pwd) = explode(':', base64_decode(substr($symfonyRequest->headers->get('authorization'), 6)));
            $symfonyRequest->headers->set('PHP_AUTH_USER', $usr);
            $symfonyRequest->headers->set('PHP_AUTH_PW', $pwd);
        }
        if (function_exists('xdebug_enable')) {
            xdebug_start_code_coverage();
        }
        return $symfonyRequest;
    }

    private function buildUri($uri, array $config)
    {
        if (!isset($config['base_uri'])) {
            return $uri instanceof UriInterface ? $uri : new Psr7\Uri($uri);
        }

        return Psr7\Uri::resolve(Psr7\uri_for($config['base_uri']), $uri);
    }

    /**
     * Merges default options into the array.
     *
     * @param array $options Options to modify by reference
     *
     * @return array
     */
    private function prepareDefaults($options)
    {
        $defaults = $this->client->getConfig();

        if (!empty($defaults['headers'])) {
            // Default headers are only added if they are not present.
            $defaults['_conditional'] = $defaults['headers'];
            unset($defaults['headers']);
        }

        // Special handling for headers is required as they are added as
        // conditional headers and as headers passed to a request ctor.
        if (array_key_exists('headers', $options)) {
            // Allows default headers to be unset.
            if ($options['headers'] === null) {
                $defaults['_conditional'] = null;
                unset($options['headers']);
            } elseif (!is_array($options['headers'])) {
                throw new \InvalidArgumentException('headers must be an array');
            }
        }

        // Shallow merge defaults underneath options.
        $result = $options + $defaults;

        // Remove null values.
        foreach ($result as $k => $v) {
            if ($v === null) {
                unset($result[$k]);
            }
        }

        return $result;
    }

    protected function send(Request $request)
    {
        gc_collect_cycles();
        if (function_exists('xdebug_enable')) {
            xdebug_stop_code_coverage(false);
        }
        $kernel = KernelUtils::getKernel();
        $m = $kernel->getContainer()->get('doctrine')->getManager();
        if (!$m->isOpen()) {
            $kernel->shutdown();
        }
        $kernel->boot();
        if (function_exists('xdebug_enable')) {
            xdebug_start_code_coverage();
        }
        return $kernel->handle($request);
    }

    public function get($url = null, $options = [])
    {
        return $this->send($this->createRequest('GET', $url, $options));
    }

    public function head($url = null, array $options = [])
    {
        return $this->send($this->createRequest('HEAD', $url, $options));
    }

    public function delete($url = null, array $options = [])
    {
        return $this->send($this->createRequest('DELETE', $url, $options));
    }

    public function put($url = null, array $options = [])
    {
        return $this->send($this->createRequest('PUT', $url, $options));
    }

    public function patch($url = null, array $options = [])
    {
        return $this->send($this->createRequest('PATCH', $url, $options));
    }

    public function post($url = null, array $options = [])
    {
        return $this->send($this->createRequest('POST', $url, $options));
    }

    public function options($url = null, array $options = [])
    {
        return $this->send($this->createRequest('OPTIONS', $url, $options));
    }
}