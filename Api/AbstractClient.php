<?php
declare(strict_types = 1);

namespace Wilensky\AdyenBundle\Api;

/**
 * @author Gregg Wilensky <https://github.com/wilensky/>
 */
abstract class AbstractClient
{
    const RESOURCE_READ_TIMEOUT = 5;
    
    /** @var int Resource read timeout in seconds */
    private $readTimeout;
    
    /** @var string Once calculated authorization header */
    private $authHeader;
    
    /**
     * @param string $username
     * @param string $password
     * @param int $rrt
     * @throws \RuntimeException
     */
    public function __construct(string $username, string $password, int $rrt = self::RESOURCE_READ_TIMEOUT)
    {
        if (!$username || !$password) {
            throw new \RuntimeException('Username and password required to compose authorization token', 100);
        }
        
        $this->authHeader = 'Basic '.base64_encode($username.':'.$password);
        $this->readTimeout = $rrt;
    }
    
    /**
     * @return int
     */
    private function getReadTimeout(): int
    {
        return $this->readTimeout;
    }

    /**
     * @param string $url
     * @return resource
     */
    private function getConnectionHandler(string $url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->getReadTimeout());
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HEADER, 10);
        
        return $ch;
    }

    /**
     * @return string
     */
    private function getAuthorizationHeader(): string
    {
        return $this->authHeader;
    }
    
    /**
     * @param string $url
     * @return bool|string
     */
    public function get(string $url)
    {
        return $this->exec($this->getConnectionHandler($url));
    }

    /**
     * @param string $url
     * @param array $postFields
     * @return bool|string
     */
    public function post(string $url, array $postFields = [])
    {
        $ch = $this->getConnectionHandler($url);
        
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        return $this->exec($ch);
    }


    /**
     * @param resource $ch
     * @param array $headers
     * @return bool|mixed
     * @throws \Exception
     */
    protected function exec($ch, array $headers = [])
    {
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array_merge(
                [
                    'Content-Type' => 'application/json',
                    'Authorization' => $this->getAuthorizationHeader()
                ],
                $headers
            )
        );
        
        $exec = curl_exec($ch);
        $code = $this->getResponseCode($ch);

        if ($code !== 200) {
            throw new \Exception($exec, $code); //@FIXME: Throw ApiException
        }

        curl_close($ch);
        
        return $exec;
    }

    /**
     * HTTP response code from the server
     * @return int
     */
    protected function getResponseCode($ch): int
    {
        return (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    }
    
    /**
     * Returns cURL error string
     * @return string
     */
    protected function getCurlError($ch)
    {
        return curl_error($ch);
    }

    /**
     * Returns `Content-Type`
     * @return string
     */
    protected function getContentType($ch): string
    {
        return curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    }
}
