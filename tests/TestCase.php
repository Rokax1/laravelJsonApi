<?php

namespace Tests;

use CloudCreativity\LaravelJsonApi\Testing\MakesJsonApiRequests;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication,MakesJsonApiRequests;

    // public function json($method, $uri, array $data = [], array $headers = [])
    // {
    //     $files = $this->extractFilesFromDataArray($data);

    //     $content = json_encode($data);

    //     $headers = array_merge([
    //         'CONTENT_LENGTH' => mb_strlen($content, '8bit'),
    //         'CONTENT_TYPE' => 'application/vnd.api+json',
    //         'Accept' => 'application/vnd.api+json',
    //     ], $headers);

    //     return $this->call(
    //         $method,
    //         $uri,
    //         [],
    //         $this->prepareCookiesForJsonRequest(),
    //         $files,
    //         $this->transformHeadersToServerVars($headers),
    //         $content
    //     );
    // }
}
