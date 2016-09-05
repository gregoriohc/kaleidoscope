<?php

namespace Gregoriohc\Kaleidoscope;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\Serializer\SerializerAbstract;

/**
 * @method \Illuminate\Database\Connection getConnection()
 */
trait Fractalizable
{
    /**
     * @var SerializerAbstract
     */
    protected static $serializer;

    /**
     * @var \League\Fractal\TransformerAbstract|\Closure
     */
    protected static $transformer;

    /**
     * @return array
     */
    public function toArray()
    {
        $manager = new Manager();

        $manager->setSerializer(new ArraySerializer());

        $resource = new Item($this, $this->getTransformer());

        return $manager->createData($resource)->toArray();
    }

    /**
     * @param $query
     *
     * @return EloquentBuilder
     */
    public function newEloquentBuilder($query)
    {
        $builder = new EloquentBuilder($query);

        /* @var Model $this */
        $builder->setFractalizableModel($this);

        return $builder;
    }

    /**
     * @return QueryBuilder
     */
    protected function newBaseQueryBuilder()
    {
        $conn = $this->getConnection();

        $grammar = $conn->getQueryGrammar();

        $builder = new QueryBuilder($conn, $grammar, $conn->getPostProcessor());

        /* @var Model $this */
        $builder->setFractalizableModel($this);

        return $builder;
    }

    /**
     * @param SerializerAbstract|null $serializer
     *
     * @return $this
     */
    public static function setSerializer(SerializerAbstract $serializer)
    {
        static::$serializer = $serializer;
    }

    /**
     * @return SerializerAbstract
     */
    public static function getSerializer()
    {
        return static::$serializer ?: new JsonApiSerializer();
    }

    /**
     * @param \League\Fractal\TransformerAbstract|\Closure|null $transformer
     *
     * @return $this
     */
    public static function setTransformer($transformer)
    {
        static::$transformer = $transformer;
    }

    /**
     * @return \League\Fractal\TransformerAbstract|\Closure
     */
    public static function getTransformer()
    {
        return static::$transformer ?: function (Model $model) {
            $attributes = $model->attributesToArray();

            return array_merge($attributes, $model->relationsToArray());
        };
    }

    /**
     * @return string
     */
    public function getResourceKey()
    {
        return str_replace('\\', '', Str::snake(Str::plural(class_basename($this))));
    }

    /**
     * @return string
     */
    public function getPaginatorLinksBasePath()
    {
        $path = function_exists('config') ? trim(config('fractalizable.links_base_path'), '/') : '';

        if (!empty($path)) {
            $path .= '/';
        }

        return $path;
    }
}
