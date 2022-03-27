<?php

namespace App\Infraestructure\Repository;

use App\Domain\RepositoryInterface\UserRepositoryInterface;
use App\Domain\Entity\User;
use App\Infraestructure\Hooks\validations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\DBAL\Exception;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    /** @var ClassMetadata */
    private $c;

    /** @var validations */
    private $validations;

    public function __construct(ManagerRegistry $registry, validations $validations)
    {
        parent::__construct($registry, User::class);
        $this->c = $this->getEntityManager()->getClassMetadata(User::class);
        $this->validations = $validations;
    }

    public function getAllUsers(): array
    {
        $queryBuilder = $this->createQueryBuilder('u');
        $queryBuilder->select('u.id, u.createdAt, u.updatedAt, u.deletedAt, u.user_name, u.email')
            ->where('u.deletedAt is NULL')
            ->orderBy('u.id', 'ASC');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function getUserById(string $id): array
    {
        $queryBuilder = $this->createQueryBuilder('u');
        $queryBuilder->select('u.id, u.createdAt, u.updatedAt, u.deletedAt, u.user_name, u.email')
            ->where('u.id = :id')->setParameter('id', $id)
            ->andWhere('u.deletedAt is NULL')
            ->orderBy('u.id', 'ASC');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function getUserByEmail(string $email): ?array
    {
        $queryBuilder = $this->createQueryBuilder('u');
        $queryBuilder->select('u.id, u.email')
            ->andWhere('u.email = :email')->setParameter('email', $email);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function getUserLastId(): array
    {
        $queryBuilder = $this->createQueryBuilder('u');
        $queryBuilder->select('max(u.id)');

        return $queryBuilder->getQuery()->getSingleResult();
    }

    public function createUser(array $user): array
    {
        $user = $this->checkFields($user);
        $user['created_at'] = $user['updated_at'] = $this->validations->setTimezone();
        $user['id'] = $this->getUserLastId()[1]+1;

        try {
            $this->getEntityManager()->getConnection()->insert('users', $user);
        } catch (Exception $e) {
            echo 'Error [createUser]: '.$e->getMessage()."\n";
        }

        return $user;
    }

    public function editUser(string $id, array $user): array
    {
        $user = $this->checkFields($user);
        $user['updated_at'] = $this->validations->setTimezone();

        try {
            $this->getEntityManager()->getConnection()->update('users', $user, ['id' => $id]);
        } catch (Exception $e) {
            echo 'Error [editUser]: '.$e->getMessage()."\n";
        }

        return $user;
    }

    public function deleteUserById(string $id, array $user): ?bool
    {
        $user = $this->checkFields($user);
        $user['deleted_at'] = $user['updated_at'] = $this->validations->setTimezone();

        try {
            $this->getEntityManager()->getConnection()->update('users', $user, ['id' => $id]);
            return true;
        } catch (Exception $e) {
            echo 'Error [deleteUser]: '.$e->getMessage()."\n";
            return false;
        }
    }

    public function checkFields(array $user): array
    {
        foreach ($user as $key => $c) {
            if (!$this->c->hasField($key)) {
                unset($user[$key]);
            }
        }

        return $user;
    }
}
