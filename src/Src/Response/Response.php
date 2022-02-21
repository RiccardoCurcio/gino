<?php

/**
 * Response object
 *
 * PHP version 8
 *
 * @category HttpResponse
 * @package  Response
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */

namespace Gino\Src\Response;

use Swoole\Http\Response as SwooleResponse;
use Swoole\Http2\Response as SwooleResponse2;
use \Gino\Src\Request\Request;


/**
 * Response class
 *
 * @category HttpResponse
 * @package  Response
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */
class Response
{
    use \Gino\Src\CorsHeaders\CorsHeaders;
    
    /**
     * Swoole response
     *
     * @var Swoole\Http\Response|Swoole\Http2\Response
     */
    private $response;

    /**
     * Costructor of Response class
     *
     * @param SwooleResponse|SwooleResponse2 $swooleResponse
     *
     * @return void
     */
    public function __construct(SwooleResponse|SwooleResponse2 $swooleResponse)
    {
        $this->response = $swooleResponse;
    }

    /**
     * Set ContentType
     *
     * @param string $contentType
     *
     * @return void
     */
    private function setHeaderContentType(string $contentType): void
    {
        $this->response->header("Content-Type", $contentType);
    }

    /**
     * Set HeadersFromArray
     *
     * @param array $contentType
     *
     * @return void
     */
    public function setHeaders(array $headers): void
    {
        array_walk($headers, fn ($value, $key) => $this->response->header($key, $value));
    }

    /**
     * Set response status code
     *
     * @param int $code
     *
     * @return void
     */
    private function setResponeStatusCode(int $code): void
    {
        $this->response->status($code);
    }

    /**
     * Generate json response
     *
     * @param array $content
     * @param int   $code
     *
     * @return void
     */
    public function json(
        array $content,
        int $code,
        string $contentType = "application/json",
        string $charset = null,
        array $headers = []
    ): void {

        $charset = $charset ?? (getenv('CHARSET') == false ? 'utf-8' : getenv('CHARSET'));

        $this->setHeaderContentType($contentType . "; charset=" . $charset);
        $this->setResponeStatusCode($code);
        $this->setHeaders($headers);
        $this->response->end(
            json_encode(
                $content,
                JSON_UNESCAPED_LINE_TERMINATORS |
                    JSON_UNESCAPED_UNICODE |
                    JSON_UNESCAPED_SLASHES |
                    JSON_HEX_TAG |
                    JSON_HEX_AMP |
                    JSON_HEX_APOS
            )
        );
    }

    /**
     * Generate xml response
     *
     * @param array $content
     * @param int   $code
     *
     * @return void
     */
    public function xml(
        array $content,
        int $code,
        string $contentType = "text/xml",
        string $charset = null,
        array $headers = []
    ): void {
        $charset = $charset ?? (getenv('CHARSET') == false ? 'utf-8' : getenv('CHARSET'));

        $this->setHeaderContentType($contentType . "; charset=" . $charset);
        $this->setResponeStatusCode($code);
        $this->setHeaders($headers);
        $this->response->end(
            $this->arrayToXml($content)
        );
    }

    /**
     * Generate Accept response
     *
     * @param array $content
     * @param int   $code
     *
     * @return void
     */
    public function response(Request $request, array $content, int $code, array $headers = []): void
    {
        $corsHeader = [];

        if (filter_var(getenv("CORSS_ORIGIN_RESOLVE"), FILTER_VALIDATE_BOOLEAN)) {
            $corsHeader = $this->getCorsHeaders();
        }

        $headers = $headers + $corsHeader;

        switch ($request->get('response-content-type')['type'] ?? null) {
            case 'json':
                $this->json(
                    $content,
                    $code,
                    $request->get('response-content-type')['content-type'],
                    getenv('CHARSET') == false ? null : getenv('CHARSET'),
                    $headers
                );
                break;
            case 'xml':
                $this->xml(
                    $content,
                    $code,
                    $request->get('response-content-type')['content-type'],
                    getenv('CHARSET') == false ? null : getenv('CHARSET'),
                    $headers
                );
                break;
            default:
                $this->json(
                    $content,
                    $code,
                    'application/json',
                    getenv('CHARSET') == false ? null : getenv('CHARSET'),
                    $headers
                );
                break;
        }
    }

    /**
     * Array to xml
     *
     * @param array $array
     * @param string|null $rootElement
     * @param string|null $xml
     * @return string|bool
     */
    private function arrayToXml(array $array, ?string $rootElement = null): string|bool
    {
        $_xml = new \SimpleXMLElement($rootElement !== null ? $rootElement : '<root/>');
        return $this->parser($array, $_xml)->asXML();
    }

    /**
     * Parse array for create xml
     *
     * @param mixed $arrayNode
     * @param \SimpleXMLElement $xml
     * @return \SimpleXMLElement
     */
    private function parser(mixed $arrayNode, \SimpleXMLElement $xml = null): \SimpleXMLElement
    {
        if (is_array($arrayNode)) {
            foreach ($arrayNode as $key => $value) {
                if (is_int($key)) {
                    if ($key == 0) {
                        $node = $xml;
                    } else {
                        $parent = $xml->xpath("..")[0];
                        $node = $parent->addChild($xml->getName());
                    }
                } else {
                    $node = $xml->addChild($key);
                }
                $this->parser($value, $node);
            }
        } else {
            $xml[0] = $arrayNode;
        }
        return $xml;
    }
}
