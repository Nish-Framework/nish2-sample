<?php
namespace Models\Services;


use Configs\Config;
use Doctrine\ORM\Query;
use Exceptions\EntityNotFoundException;
use Exceptions\NotAllowedException;
use Models\Entities\ORM\BaseEntity;
use Nish\Commons\Di;
use Nish\Commons\GlobalSettings;
use Nish\Logger\Logger;
use Nish\NishApplication;

class DefaultService
{
    public const ONLY_FLUSH = 'only_flush';

    /* @var \Doctrine\ORM\EntityManager */
    protected $entityManager;

    protected $crudService;
    protected $crudOperatorId;

    /* @var Logger */
    protected $logger;

    protected static $cacheLifeTime = 1800;

    public function __construct()
    {
        /* @var \Doctrine\ORM\EntityManager $entityManager */
        $this->entityManager = Di::get('ormEntityManager');
        $this->crudOrigin = GlobalSettings::get('crud_service');

        $this->crudOperatorId = GlobalSettings::get('crud_operator_id');

        $this->logger = NishApplication::getDefaultLogger();
    }

    public static function getCacheEntityRegion()
    {
        return 'default';
    }

    /**
     * @return mixed
     */
    public function getCrudService()
    {
        return $this->crudService;
    }

    /**
     * @return mixed|null
     */
    public function getCrudOperatorId()
    {
        return $this->crudOperatorId;
    }

    /**
     * @param $query
     * @param array $bind
     * @param bool $getAsArray
     * @param bool $useCache
     * @param null $limit
     * @param null $offset
     * @return int|mixed|string
     */
    public function query($query, array $bind = [], bool $getAsArray = false, $useCache = true, $limit = null, $offset = null)
    {

        $q = $this->entityManager->createQuery($query);

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

        $this->logger->info('Query: '.$q->getSQL(), ['Params' => $bind]);

        return $q->execute($bind, ($getAsArray ? Query::HYDRATE_ARRAY : Query::HYDRATE_OBJECT));
    }


    /**
     * @param $entityClass
     * @param null $id
     * @param array $params
     * @param bool $doFlush
     * @param array|null $checkOnUpdate
     * @return mixed
     * @throws EntityNotFoundException
     * @throws NotAllowedException
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Persistence\Mapping\MappingException
     */
    public function saveOrUpdateEntity($entityClass, $id = null, array $params = [], $doFlush = true, ?array $checkOnUpdate = null)
    {
        if (is_numeric($id) && $id != 0) {
            $entity = $entityClass::findById($id);

            if ($entity == null) {
                throw new EntityNotFoundException();
            }

            if (!empty($checkOnUpdate)) {
                foreach ($checkOnUpdate as $col => $val) {
                    if (is_array($val)) {
                        if ($val[0] == '!=') {
                            if ($entity->getAttribute($col) == $val[1]) {
                                throw new NotAllowedException('User is not allowed to delete due to field: '.$col);
                            }
                        } elseif ($val[0] == 'in') {
                            if (!in_array($entity->getAttribute($col), $val[1])) {
                                throw new NotAllowedException('User is not allowed to delete due to field: '.$col);
                            }
                        } elseif ($val[0] == 'not in') {
                            if (in_array($entity->getAttribute($col), $val[1])) {
                                throw new NotAllowedException('User is not allowed to delete due to field: '.$col);
                            }
                        }
                    } else {
                        if ($entity->getAttribute($col) != $val) {
                            throw new NotAllowedException('User is not allowed to delete due to field: '.$col);
                        }
                    }
                }
            }
        } else {
            $entity = new $entityClass();
        }

        $entity->setAttributes($params);

        if (is_numeric($id) && $id != 0) {
            return $this->updateEntity($entity, $doFlush);
        } else {
            return $this->createEntity($entity, $doFlush);
        }
    }

    /**
     * @param $entity
     * @param bool $doFlush
     * @return mixed
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Persistence\Mapping\MappingException
     */
    public function createEntity($entity, $doFlush = true)
    {
        $entity->setCreateOrigin($this->crudOrigin);
        $entity->setCreatedAt(time());
        $entity->setCreatedBy($this->crudOperatorId ?: 0);
        $entity->setIsDeleted(0);
        $entity->setDeletedAt(0);
        $entity->setUpdatedAt(0);

        $this->entityManager->persist($entity);

        if ($doFlush !== false) {
            $this->entityManager->flush();
            get_class($entity)::flushCache();

            if ($doFlush != self::ONLY_FLUSH) {
                $this->entityManager->clear();
            }
        }

        $this->logger->info('DB Entity Creation: '.(static::class), $entity->toArray());

        return $entity->getId();
    }

    /**
     * @param $entity
     * @param bool $doFlush
     * @return mixed
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Persistence\Mapping\MappingException
     */
    public function updateEntity($entity, $doFlush = true)
    {
        $entity->setUpdateOrigin($this->crudOrigin);
        $entity->setUpdatedAt(time());
        $entity->setUpdatedBy($this->crudOperatorId ?: 0);

        if ($doFlush !== false) {
            $this->entityManager->flush();
            get_class($entity)::flushCache();

            if ($doFlush != self::ONLY_FLUSH) {
                $this->entityManager->clear();
            }
        }


        $this->logger->info('DB Entity Update: '.(static::class), $entity->toArray());

        return $entity->getId();
    }

    /**
     * @param $entity
     * @param bool $doFlush
     * @param array|null $checkOnUpdate
     * @throws NotAllowedException
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Persistence\Mapping\MappingException
     */
    public function fakeDelete($entity, $doFlush = true, ?array $checkOnUpdate = null)
    {
        if (!empty($checkOnUpdate)) {
            foreach ($checkOnUpdate as $col => $val) {
                if (is_array($val)) {
                    if ($val[0] == '!=') {
                        if ($entity->getAttribute($col) == $val[1]) {
                            throw new NotAllowedException('User is not allowed to delete due to field: '.$col);
                        }
                    } elseif ($val[0] == 'in') {
                        if (!in_array($entity->getAttribute($col), $val[1])) {
                            throw new NotAllowedException('User is not allowed to delete due to field: '.$col);
                        }
                    } elseif ($val[0] == 'not in') {
                        if (in_array($entity->getAttribute($col), $val[1])) {
                            throw new NotAllowedException('User is not allowed to delete due to field: '.$col);
                        }
                    }
                } else {
                    if ($entity->getAttribute($col) != $val) {
                        throw new NotAllowedException('User is not allowed to delete due to field: '.$col);
                    }
                }
            }
        }

        $entity->setIsDeleted(1);
        $entity->setDeleteOrigin($this->crudOrigin);
        $entity->setDeletedAt(time());
        $entity->setDeletedBy($this->crudOperatorId ?: 0);


        if ($doFlush !== false) {
            $this->entityManager->flush();
            get_class($entity)::flushCache();

            if ($doFlush != self::ONLY_FLUSH) {
                $this->entityManager->clear();
            }
        }

        $this->logger->info('DB Entity FDeletion: '.(static::class), $entity->toArray());
    }


    /**
     * @param $entity
     * @param bool $doFlush
     * @param array|null $checkOnUpdate
     * @throws NotAllowedException
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Persistence\Mapping\MappingException
     */
    public function deleteFromDB($entity, $doFlush = true, ?array $checkOnUpdate = null)
    {
        if (!empty($checkOnUpdate)) {
            foreach ($checkOnUpdate as $col => $val) {
                if (is_array($val)) {
                    if ($val[0] == '!=') {
                        if ($entity->getAttribute($col) == $val[1]) {
                            throw new NotAllowedException('User is not allowed to delete due to field: '.$col);
                        }
                    } elseif ($val[0] == 'in') {
                        if (!in_array($entity->getAttribute($col), $val[1])) {
                            throw new NotAllowedException('User is not allowed to delete due to field: '.$col);
                        }
                    } elseif ($val[0] == 'not in') {
                        if (in_array($entity->getAttribute($col), $val[1])) {
                            throw new NotAllowedException('User is not allowed to delete due to field: '.$col);
                        }
                    }
                } else {
                    if ($entity->getAttribute($col) != $val) {
                        throw new NotAllowedException('User is not allowed to delete due to field: '.$col);
                    }
                }
            }
        }

        $this->entityManager->remove($entity);

        if ($doFlush !== false) {
            $this->entityManager->flush();
            get_class($entity)::flushCache();

            if ($doFlush != self::ONLY_FLUSH) {
                $this->entityManager->clear();
            }
        }

        $this->logger->info('DB Entity RDeletion: '.(static::class), $entity->toArray());
    }

    public function refresh(BaseEntity $entity) {
        $this->entityManager->refresh($entity);
    }

    /**
     * @param $entityClass
     * @param string $conditionsStr
     * @param array $bindParams
     * @param string $fields
     * @param int $currentPage
     * @param int $limit
     * @param false $getAsArray
     * @param bool $useCache
     * @param array|null $orderBy
     * @return array
     */
    protected function searchWithPaginationData($entityClass, $conditionsStr = '', array $bindParams = [], $fields = 't', $currentPage = 1, $limit = Config::PAGINATION_LIMIT, $getAsArray = false, $useCache = true, ?array $orderBy = null)
    {
        if ($currentPage <= 0) {
            $currentPage = 1;
        }

        if (is_numeric($limit) && $limit <= 0) {
            $limit = Config::PAGINATION_LIMIT;
        }

        if (is_numeric($limit)) {
            $offset = ($currentPage - 1) * $limit;
        } else {
            $limit = null;
            $offset = null;
        }


        $rowSet = $entityClass::selectBy($conditionsStr, $bindParams, $fields, $getAsArray, $useCache,$limit, $offset, $orderBy);

        if (is_numeric($limit)) {
            $totalCount = $entityClass::findCount($conditionsStr, $bindParams, $useCache);

            $pageCount = ceil($totalCount / $limit);
        } else {
            $totalCount = count($rowSet);
            $pageCount = 1;
        }


        return [
            'totalCount' => $totalCount,
            'currentPage' => $currentPage,
            'pageCount' => $pageCount,
            'offset' => $offset,
            'data' => $rowSet
        ];
    }
}