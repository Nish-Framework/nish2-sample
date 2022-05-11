<?php
namespace Models\Entities\ORM;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Query;
use Nish\Commons\Di;
use Nish\PrimitiveBeast;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\VarDumper;

/** @ORM\MappedSuperclass */
abstract class BaseEntity extends PrimitiveBeast
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $create_service;

    /**
     * @ORM\Column(type="integer")
     */
    protected $created_by;

    /**
     * @ORM\Column(type="integer")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="string")
     */
    protected $update_service;

    /**
     * @ORM\Column(type="integer")
     */
    protected $updated_by;

    /**
     * @ORM\Column(type="integer")
     */
    protected $updated_at;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $is_deleted;

    /**
     * @ORM\Column(type="string")
     */
    protected $delete_service;

    /**
     * @ORM\Column(type="integer")
     */
    protected $deleted_by;

    /**
     * @ORM\Column(type="integer")
     */
    protected $deleted_at;

    protected static $cacheLifeTime = 1800;

    public function __construct()
    {

    }

    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * @param $arr
     * @return static
     */
    public static function createFromArray($arr)
    {
        if (empty($arr)) {
            return (new static());
        }

        $entity = new static();
        $entity->setAttributes($arr);

        return $entity;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $name
     * @return null
     */
    public function getAttribute($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }

        return null;
    }

    /**
     * @param $name
     * @param $value
     */
    public function setAttribute($name, $value)
    {
        if (array_key_exists($name, get_object_vars($this))) {
            $this->$$name = $value;
        }
    }

    /**
     * @param array $attrValues
     */
    public function setAttributes(array $attrValues = [])
    {
        foreach ($attrValues as $name => $value) {
            if (array_key_exists($name, get_object_vars($this))) {
                call_user_func([$this, 'set'.str_replace(' ', '', ucwords(str_replace('_', ' ', $name)))], $value);
            }
        }
    }


    /**
     * @param mixed $create_origin
     */
    public function setCreateOrigin($create_origin): void
    {
        $this->create_origin = $create_origin;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * @param mixed $created_by
     */
    public function setCreatedBy($created_by): void
    {
        $this->created_by = $created_by;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at): void
    {
        $this->created_at = $created_at;
    }



    /**
     * @param mixed $update_origin
     */
    public function setUpdateOrigin($update_origin): void
    {
        $this->update_origin = $update_origin;
    }

    /**
     * @return mixed
     */
    public function getUpdatedBy()
    {
        return $this->updated_by;
    }

    /**
     * @param mixed $updated_by
     */
    public function setUpdatedBy($updated_by): void
    {
        $this->updated_by = $updated_by;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt($updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return mixed
     */
    public function getIsDeleted()
    {
        return $this->is_deleted;
    }

    /**
     * @param mixed $is_deleted
     */
    public function setIsDeleted($is_deleted): void
    {
        $this->is_deleted = $is_deleted;
    }

    /**
     * @param mixed $delete_origin
     */
    public function setDeleteOrigin($delete_origin): void
    {
        $this->delete_origin = $delete_origin;
    }

    /**
     * @return mixed
     */
    public function getDeletedBy()
    {
        return $this->deleted_by;
    }

    /**
     * @param mixed $deleted_by
     */
    public function setDeletedBy($deleted_by): void
    {
        $this->deleted_by = $deleted_by;
    }

    /**
     * @return mixed
     */
    public function getDeletedAt()
    {
        return $this->deleted_at;
    }

    /**
     * @param mixed $deleted_at
     */
    public function setDeletedAt($deleted_at): void
    {
        $this->deleted_at = $deleted_at;
    }

    /**
     * @param callable|resource|string|true|null $output A line dumper callable, an opened stream, an output path or true to return the dump
     * @return string|null
     * @throws \ErrorException
     */
    public function dumpHtml($output = null)
    {
        $dumper = new HtmlDumper();
        return $dumper->dump((new VarCloner())->cloneVar($this), $output );
    }

    /**
     * @param callable|resource|string|true|null $output A line dumper callable, an opened stream, an output path or true to return the dump
     * @return string|null
     * @throws \ErrorException
     */
    public function dumpCli($output = null)
    {
        $dumper = new CliDumper();
        return $dumper->dump((new VarCloner())->cloneVar($this), $output );
    }

    /**
     * @param false $indexedById
     * @param bool $getAsArray
     * @param string $fields
     * @param bool $useCache
     * @param null $limit
     * @param null $offset
     * @param array|null $orderBy
     * @param array|null $groupBy
     * @return array|static
     */
    public static function findAll($indexedById = false, bool $getAsArray = false, string $fields = 't', $useCache = true, $limit = null, $offset = null, ?array $orderBy = null, ?array $groupBy = null)
    {
        if ($fields == null) $fields = 't';

        $resultList = self::selectBy('', [], $fields, $getAsArray, $useCache, $limit, $offset, $orderBy, $groupBy);

        if (!$indexedById) {
            return $resultList;
        } else {
            $indexedResultList = [];

            foreach ($resultList as $entity) {
                $indexedResultList[($getAsArray ? $entity['id'] : $entity->getId())] = $entity;
            }

            return $indexedResultList;
        }
    }

    /**
     * @param $id
     * @param false $useCache
     * @return mixed|BaseEntity|object|static|null
     * @throws \Nish\Exceptions\ContainerObjectNotFoundException
     */
    public static function findById($id, $useCache = false)
    {
        if ($useCache) {
            $resultList = self::selectBy('t.id = :id', ['id' => $id]);

            if (!empty($resultList)) {
                return $resultList[0];
            } else {
                return null;
            }
        } else {
            /* @var \Doctrine\ORM\EntityManager $entityManager */
            $entityManager = Di::get('ormEntityManager');
            $repo = $entityManager->getRepository(static::class);

            return $repo->find($id);
        }
    }

    /**
     * @param array $conditions
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return object[]
     * @throws \Nish\Exceptions\ContainerObjectNotFoundException
     */
    public static function findBy(array $conditions, array $orderBy = null, $limit = null, $offset = null)
    {
        /* @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = Di::get('ormEntityManager');
        $repo = $entityManager->getRepository(static::class);

        if (!array_key_exists('is_deleted', $conditions)) {
            $conditions['is_deleted'] = 0;
        }

        return $repo->findBy($conditions, $orderBy, $limit, $offset);
    }

    /**
     * @param array $conditions
     * @param array|null $orderBy
     * @return object|null
     * @throws \Nish\Exceptions\ContainerObjectNotFoundException
     */
    public static function findOneBy(array $conditions, array $orderBy = null)
    {
        /* @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = Di::get('ormEntityManager');
        $repo = $entityManager->getRepository(static::class);

        if (!array_key_exists('is_deleted', $conditions)) {
            $conditions['is_deleted'] = 0;
        }

        return $repo->findOneBy($conditions, $orderBy);
    }

    /**
     * @param string $conditions
     * @param array $bind
     * @param string $fields
     * @param bool $getAsArray
     * @param bool $useCache
     * @param null $limit
     * @param null $offset
     * @param array|null $orderBy
     * @param array|null $groupBy
     * @return int|mixed|string
     * @throws \Nish\Exceptions\ContainerObjectNotFoundException
     */
    public static function selectBy(string $conditions = '', array $bind = [], string $fields = 't', bool $getAsArray = false, $useCache = true, $limit = null, $offset = null, ?array $orderBy = null, ?array $groupBy = null)
    {
        if (strpos($conditions, 'is_deleted') === false) {
            $conditions = 't.is_deleted = 0' . ($conditions == '' ? '' : " AND ($conditions)");
        }

        $query = 'SELECT '.$fields.' FROM ' . static::class . ' t WHERE ' . $conditions;

        if (!empty($groupBy)) {
            $query .= ' GROUP BY '.implode(',', $groupBy);
        }

        if (!empty($orderBy)) {
            $query .= ' ORDER BY '.implode(',', $orderBy);
        }

        /* @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = Di::get('ormEntityManager');

        $q = $entityManager->createQuery($query);

        if ($useCache) {
            if (!is_numeric($useCache)) {
                $useCache = self::$cacheLifeTime;
            }

            $q->enableResultCache($useCache);
            //$q->setResultCacheId(static::getCacheEntityRegion().'_selectBy');
            $q->setCacheRegion(static::getCacheEntityRegion());
        } else {
            $q->disableResultCache();
        }

        if (is_numeric($limit) && $limit > 0) {
            $q->setMaxResults($limit);
        }

        if (is_numeric($offset) && $offset > 0) {
            $q->setFirstResult($offset);
        }

        self::getDefaultLogger()->info('Query: '.$q->getSQL(), ['Params' => $bind]);

        return $q->execute($bind, ($getAsArray ? Query::HYDRATE_ARRAY : Query::HYDRATE_OBJECT));
    }

    /**
     * @param string $conditions
     * @param array $bind
     * @throws \Nish\Exceptions\ContainerObjectNotFoundException
     */
    public static function deleteBy(string $conditions = '', array $bind = [])
    {

        $query = 'DELETE FROM ' . static::class . ' t WHERE ' . $conditions;

        /* @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = Di::get('ormEntityManager');

        $q = $entityManager->createQuery($query);

        self::getDefaultLogger()->info('Query: '.$q->getSQL(), ['Params' => $bind]);

        $q->execute($bind);
    }

    /**
     * @param string $conditions
     * @param array $bind
     * @param bool $useCache
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Nish\Exceptions\ContainerObjectNotFoundException
     */
    public static function findCount(string $conditions = '', array $bind = [], $useCache = true)
    {
        if (strpos($conditions, 'is_deleted') === false) {
            $conditions = 't.is_deleted = 0' . ($conditions == '' ? '' : " AND ($conditions)");
        }

        $query = 'SELECT COUNT(t.id) as totalCount FROM ' . static::class . ' t WHERE ' . $conditions;

        /* @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = Di::get('ormEntityManager');

        $q = $entityManager->createQuery($query);

        if (!empty($bind)) {
            $q->setParameters($bind);
        }

        if ($useCache) {
            if ($useCache && !is_numeric($useCache)) {
                $useCache = self::$cacheLifeTime;
            }
            $q->enableResultCache($useCache);
            //$q->setResultCacheId(static::class.'_count');
            $q->setCacheRegion(static::getCacheEntityRegion());

        } else {
            $q->disableResultCache();
        }

        self::getDefaultLogger()->info('Query: '.$q->getSQL(), ['Params' => $bind]);

        return $q->getSingleScalarResult();
    }

    /**
     * @throws \Nish\Exceptions\ContainerObjectNotFoundException
     */
    public static function flushCache()
    {
        /* @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = Di::get('ormEntityManager');


        $cache = $entityManager->getCache();
        if ($cache) {
            $cache->evictEntityRegion(static::class);
        }
    }

    public static abstract function getCacheEntityRegion();
}