<?php
namespace Models\Services;

use Exceptions\EntityExistsException;
use Exceptions\InvalidParameterException;
use Exceptions\NotAllowedException;
use Exceptions\UserNotFoundException;
use Models\Entities\ORM\User;
use Models\Services\SearchDTOs\UserSearchDTO;

class UserService extends DefaultService
{
    /**
     * @param UserSearchDTO $request
     * @return array
     */
    public function searchWithPagination(UserSearchDTO $request)
    {
        $conditions = [];
        $bind = [];

        $ids = null;

        if (is_numeric($request->getIdList())) {
            $ids = [$request->getIdList()];
        } else {
            $ids = $request->getIdList();
        }

        if (is_array($ids)) {
            $conditions[] = 't.id in (:ids)';
            $bind['ids'] = $ids;
        }

        if (!empty($request->getText())) {
            $search = preg_replace('/ {2,}/i', ' ', trim($request->getText()));

            if ($search != '') {
                $conditions[] = "( concat(t.first_name, ' ', t.last_name) like :search_key OR t.email like :search_key OR t.gsm like :search_key)";
                $bind['search_key'] = '%'.$search.'%';
            }
        }

        return $this->searchWithPaginationData(User::class, implode(' AND ', $conditions), $bind, 't', $request->getCurrentPage(), $request->getPageLimit(), $request->isArrayReturnRequired(), $request->isCacheUsageNeeded());
    }


    /**
     * @param null $id
     * @param array $params
     * @param bool $doFlush
     * @param array|null $checkOnUpdate
     * @throws EntityExistsException
     * @throws InvalidParameterException
     * @throws NotAllowedException
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Persistence\Mapping\MappingException
     * @throws \Exceptions\EntityNotFoundException
     * @throws \Nish\Exceptions\ContainerObjectNotFoundException
     */
    public function saveUser($id = null, array $params = [], $doFlush = true, ?array $checkOnUpdate = null)
    {
        if (empty($params['email'])) {
            throw new InvalidParameterException('E-mail required!');
        }

        if ($user = $this->userExists($params['email'])) {
            if (!is_numeric($id) || $id != $user->getId()) {
                throw new EntityExistsException('This e-mail address is in usage!');
            }
        }

        $this->saveOrUpdateEntity(User::class, $id, $params, $doFlush, $checkOnUpdate);
    }

    /**
     * @param $email
     * @param null $password
     * @return false|object
     * @throws \Nish\Exceptions\ContainerObjectNotFoundException
     */
    public function userExists($email, $password = null)
    {
        $conditions = ['email' => $email];

        if ($password != null) {
            $conditions['password'] = $password;
        }

        $user = User::findOneBy($conditions);

        if ($user) {
            return $user;
        }

        return false;
    }


    /**
     * @param $id
     * @param false $realDelete
     * @param array|null $checkOnUpdate
     * @throws InvalidParameterException
     * @throws NotAllowedException
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Persistence\Mapping\MappingException
     * @throws \Nish\Exceptions\ContainerObjectNotFoundException
     */
    public function deleteById($id, $realDelete = false, ?array $checkOnUpdate = null)
    {
        if (!is_numeric($id)) {
            throw new InvalidParameterException('Invalid id: '.$id);
        }

        $item = User::findById($id);

        if ($item != null) {
            if ($realDelete === true) {
                $this->deleteFromDB($item, $checkOnUpdate);
            } else {
                $this->fakeDelete($item, $checkOnUpdate);
            }
        }
    }

    /**
     * @param $username
     * @param $password
     * @return mixed
     * @throws UserNotFoundException
     * @throws \Nish\Exceptions\ContainerObjectNotFoundException
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password
    ) {
        $conditions = [
            't.email = :email',
            't.password = :password'
        ];
        $bind = [
            'email' => $username,
            'password' => md5($password),
        ];

        $resultSet = User::selectBy(implode(' AND ', $conditions), $bind, 't');

        if (empty($resultSet)) {
            throw new UserNotFoundException('User not found! username: '.$username);
        }

        return $resultSet[0];
    }

}