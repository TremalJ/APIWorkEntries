<?php

namespace App\Infraestructure\Repository;

use App\Domain\Entity\User;
use App\Domain\Entity\WorkEntry;
use App\Domain\RepositoryInterface\WorkEntryRepositoryInterface;
use App\Infraestructure\Hooks\validations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WorkEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkEntry[]    findAll()
 * @method WorkEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkEntryRepository extends ServiceEntityRepository  implements WorkEntryRepositoryInterface
{
    /** @var ClassMetadata */
    private $c;

    /** @var validations */
    private $validations;

    public function __construct(ManagerRegistry $registry,validations $validations)
    {
        parent::__construct($registry, WorkEntry::class);
        $this->c = $this->getEntityManager()->getClassMetadata(WorkEntry::class);
        $this->validations = $validations;
    }

    public function getAllWorksEntryByUserId(string $user_id): array
    {
        $queryBuilder = $this->createQueryBuilder('w');
        $queryBuilder->select('w')
            ->innerJoin(User::class, 'u', 'WITH', 'u.id = w.users')
            ->where('w.users = :users')->setParameter('users', $user_id)
            ->andWhere('w.deletedAt is NULL')
            ->orderBy('w.id', 'ASC');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function getWorkEntryById(string $id): array
    {
        $queryBuilder = $this->createQueryBuilder('w');
        $queryBuilder->select('w')
            ->where('w.id = :id')->setParameter('id', $id)
            ->andWhere('w.deletedAt is NULL')
            ->orderBy('w.id', 'ASC');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function getWorkEntryLastId(): array
    {
        $queryBuilder = $this->createQueryBuilder('w');
        $queryBuilder->select('max(w.id)');

        return $queryBuilder->getQuery()->getSingleResult();
    }

    public function createWorkEntry(array $workEntry, string $user_id): array
    {
        $workEntry = $this->checkFields($workEntry);
        $workEntry['created_at'] = $workEntry['updated_at'] = $this->validations->setTimezone();
        $workEntry['id'] = $this->getWorkEntryLastId()[1]+1;
        $workEntry['users_id'] = $user_id;

        try {
            $this->getEntityManager()->getConnection()->insert('work_entry', [
                'id' => $workEntry['id'],
                'created_at' => $workEntry['created_at'] ,
                'updated_at' => $workEntry['updated_at'],
                'deleted_at' => null,
                'start_date' => $workEntry['startDate'],
                'end_date' => $workEntry['endDate'],
                'users_id' => $workEntry['users_id'],
            ]);
        } catch (Exception $e) {
            echo 'Error [createWorkEntry]: '.$e->getMessage()."\n";
        }

        return $workEntry;
    }

    public function editWorkEntry(string $id, array $workEntry): array
    {
        $workEntry = $this->checkFields($workEntry);
        $workEntry['updated_at'] = $this->validations->setTimezone();
        $workEntry['id'] = $id;

        try {
            $this->getEntityManager()->getConnection()->update('work_entry', [
                'updated_at' => $workEntry['updated_at'],
                'start_date' => $workEntry['startDate'],
                'end_date' => $workEntry['endDate'],
            ], ['id' => $id]);
        } catch (Exception $e) {
            echo 'Error [editWorkEntry]: '.$e->getMessage()."\n";
        }

        return $workEntry;
    }

    public function deleteWorkEntryById(string $id, array $workEntry): ?bool
    {
        $workEntry = $this->checkFields($workEntry);
        $workEntry['deleted_at'] = $workEntry['updated_at'] = $this->validations->setTimezone();

        try {
            $this->getEntityManager()->getConnection()->update('work_entry', $workEntry, ['id' => $id]);
            return true;
        } catch (Exception $e) {
            echo 'Error [deleteWorkEntry]: '.$e->getMessage()."\n";
            return false;
        }
    }

    public function checkFields(array $work_entry): array
    {
        foreach ($work_entry as $key => $c) {
            if (!$this->c->hasField($key)) {
                unset($work_entry[$key]);
            }
        }

        return $work_entry;
    }
}
