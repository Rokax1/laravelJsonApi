<?php

namespace App\JsonApi\Categories;

use App\Models\Category;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{

    /**
     * @var string
     */
    protected $resourceType = 'categories';

    /**
     * @param Category $resource
     *      the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param Category $resource
     *      the domain record being serialized.
     * @return array
     */
    public function getAttributes($category)
    {
        return [
            'name'=>$category->name,
            'slug'=>$category->slug,
            // 'createdAt' => $category->created_at,
            // 'updatedAt' => $category->updated_at,
        ];
    }

    public function getRelationships($category, $isPrimary, array $includeRelationships)
    {


        return [
            'articles' => [

                self::SHOW_RELATED => true,
                self::SHOW_SELF => true,
                self::SHOW_DATA => isset($includeRelationships['articles']),
                self::DATA => function () use ($category) {
                    return $category->articles;
                }
            ]

        ];
    }
}
