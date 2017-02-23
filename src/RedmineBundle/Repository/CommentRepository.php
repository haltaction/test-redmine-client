<?php

namespace RedmineBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CommentRepository
 */
class CommentRepository extends EntityRepository
{
    /**
     * Return all comments with selected project ID
     *
     * @param $projectId
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getAllByProject($projectId)
    {
        return $this->createQueryBuilder('c')
            ->where('c.projectId = :projectId')
            ->orderBy('c.createdAt', 'DESC')
            ->setParameter('projectId', $projectId)
            ->getQuery();
    }
}
