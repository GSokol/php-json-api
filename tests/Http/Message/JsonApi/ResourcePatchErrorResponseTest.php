<?php

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 8/1/15
 * Time: 12:28 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NilPortugues\Tests\Api\Http\Message\JsonApi;

use NilPortugues\Api\Http\Message\JsonApi\ResourcePatchErrorResponse;

class ResourcePatchErrorResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testResponse()
    {
        $json = json_encode([]);
        $response = new ResourcePatchErrorResponse($json);

        $this->assertEquals(409, $response->getStatusCode());
        $this->assertEquals(['application/vnd.api+json'], $response->getHeader('Content-type'));
    }
}