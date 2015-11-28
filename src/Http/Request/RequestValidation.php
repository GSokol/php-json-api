<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 11/28/15
 * Time: 10:37 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NilPortugues\Api\JsonApi\Http\Request;

use NilPortugues\Api\JsonApi\Http\Factory\RequestFactory;
use NilPortugues\Api\JsonApi\JsonApiSerializer;
use NilPortugues\Api\JsonApi\Server\Errors\InvalidAttributeError;
use NilPortugues\Api\JsonApi\Server\Errors\InvalidParameterError;
use NilPortugues\Api\JsonApi\Server\Errors\Error;

/**
 * Class RequestValidation.
 */
trait RequestValidation
{
    /**
     * @var Error[]
     */
    private $queryParamErrorBag = [];

    /**
     * @return Error[]
     */
    protected function getQueryParamsErrors()
    {
        return $this->queryParamErrorBag;
    }

    /**
     * @param JsonApiSerializer $serializer
     *
     * @return bool
     */
    protected function hasValidQueryParams(JsonApiSerializer $serializer)
    {
        $apiRequest = $this->apiRequest();
        $this->validateQueryParamsTypes($serializer, $apiRequest->getFields(), 'Fields');
        $this->validateQueryParamsTypes($serializer, $apiRequest->getIncludedRelationships(), 'Include');

        return empty($this->queryParamErrorBag);
    }

    /**
     * @return Request
     */
    protected function apiRequest()
    {
        return RequestFactory::create();
    }

    /**
     * @param JsonApiSerializer $serializer
     * @param array             $fields
     * @param                   $paramName
     */
    private function validateQueryParamsTypes(JsonApiSerializer $serializer, array $fields, $paramName)
    {
        if (!empty($fields)) {
            $transformer = $serializer->getTransformer();
            $validateFields = array_keys($fields);

            foreach ($validateFields as $key => $field) {
                $mapping = $transformer->getMappingByAlias($field);
                if (null !== $mapping) {
                    $properties = array_merge(
                        array_combine($mapping->getProperties(), $mapping->getProperties()),
                        $mapping->getAliasedProperties()
                    );

                    $invalidProperties = array_diff($fields[$field], $properties);
                    foreach ($invalidProperties as $extraField) {
                        $this->queryParamErrorBag[] = new InvalidAttributeError($extraField, $field);
                    }
                    unset($validateFields[$key]);
                }
            }

            if (false === empty($validateFields)) {
                foreach ($validateFields as $field) {
                    $this->queryParamErrorBag[] = new InvalidParameterError(strtolower($paramName), $field);
                }
            }
        }
    }
}
