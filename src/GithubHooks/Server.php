<?php

namespace GithubHooks;

/**
 * Class Server
 * @package GithubHooks
 */
class Server
{
    /**
     * @var array Recommended Reason Phrases
     */
    protected $recommendedReasonPhrases = array(
        // INFORMATIONAL CODES
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        // SUCCESS CODES
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        // REDIRECTION CODES
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy', // Deprecated
        307 => 'Temporary Redirect',
        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        // SERVER ERROR
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    );

    /**
     * @var array
     */
    protected $githubCdrs = array(
        '204.232.175.64/27',
        '192.30.252.0/22'
    );

    /**
     * @var \StdClass
     */
    protected $payload;

    /**
     * @var bool
     */
    protected $validateOrigin = true;

    /**
     * @var string
     */
    protected $origin;

    /**
     * @var array
     */
    protected $repositories = array();

    public function __construct()
    {
        if (!$this->isValidOrigin($this->getOrigin())) {
            $this->close(403);
        }

        if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->close(405);
        }

        if (!$this->getPayload()) {
            $this->close(400);
        }
    }

    /**
     * @return string|null
     */
    protected function getOrigin()
    {
        if ($this->origin === null) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
                $this->origin = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $this->origin = $_SERVER['REMOTE_ADDR'];
            }
        }

        return $this->origin;
    }

    /**
     * @param $ip
     * @return bool
     */
    protected function isValidOrigin($ip)
    {
        $ipu = explode('.', $ip);

        foreach ($ipu as &$v) {
            $v = str_pad(decbin($v), 8, '0', STR_PAD_LEFT);
        }

        $ipu = join('', $ipu);

        foreach ($this->githubCdrs as $cidr) {
            $parts = explode('/', $cidr);
            $ipc = explode('.', $parts[0]);

            foreach ($ipc as &$v) $v = str_pad(decbin($v), 8, '0', STR_PAD_LEFT); {
                $ipc = substr(join('', $ipc), 0, $parts[1]);
                $ipux = substr($ipu, 0, $parts[1]);
                $result = ($ipc === $ipux);
            }

            if ($result) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param bool $validateOrigin
     */
    public function setValidateOrigin($validateOrigin)
    {
        $this->validateOrigin = (bool) $validateOrigin;
    }

    /**
     * @return bool
     */
    public function getValidateOrigin()
    {
        return $this->validateOrigin;
    }

    /**
     * @return mixed|null
     */
    public function getPayload()
    {
        if ($this->payload === null && isset($_POST['payload'])) {
            $this->payload = json_decode($_POST['payload']);
        }

        return $this->payload;
    }

    /**
     * @param $id
     * @param Repository $repository
     * @throws \RuntimeException
     */
    public function addRespitory($id, Repository $repository)
    {
        if ($this->getRepository($id)) {
            throw new \RuntimeException("Repository with id {$id} already exists");
        }

        $this->repositories[$id] = $repository;
    }

    /**
     * @param $id
     * @return Repository|null
     */
    public function getRepository($id)
    {
        return isset($this->repositories[$id]) ? $this->repositories[$id] : null;
    }

    /**
     * @param $url
     * @return Repository|null
     */
    public function getRepositoryByUrl($url)
    {
        /* @var $repository Repository */
        foreach ($this->repositories as $repository) {
            if ($repository->getUrl() == $url) {
                return $repository;
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public function getRepositories()
    {
        return $this->repositories;
    }

    /**
     * @param int $statusCode
     * @return string
     */
    protected function getReasonPhrase($statusCode)
    {
        return isset($this->recommendedReasonPhrases[$statusCode]) ? $this->recommendedReasonPhrases[$statusCode] : 'Unknown reason';
    }

    /**
     * @param int $statusCode
     * @param mixed $reasonPhrase
     */
    protected function close($statusCode = 200, $reasonPhrase = null)
    {
        $payload = array(
            'status' => $statusCode,
            'reason' => $reasonPhrase ? $reasonPhrase : $this->getReasonPhrase($statusCode)
        );

        header("HTTP/1.1 {$statusCode} ".$this->getReasonPhrase($statusCode));
        header("Content-Type: Application/Json");
        echo json_encode($payload);

        exit();
    }

    public function resolve($closeOnFinish = true)
    {
        if (!isset($this->payload->repository) || !isset($this->payload->repository->url)) {
            $this->close(400);
        }

        $repository = $this->getRepositoryByUrl($this->payload->repository->url);
        if (!$repository) {
            $this->close(404, 'Unknown repository');
        }

        $repository->resolveHooks($this->payload);

        if ($closeOnFinish) {
            $this->close(200);
        }
    }
}