<?php
namespace MapasCulturais\Repositories;
use MapasCulturais\Traits;

class Registration extends \MapasCulturais\Repository{
    /**
     *
     * @param \MapasCulturais\Entities\Project $project
     * @param \MapasCulturais\Entities\User $user
     * @return \MapasCulturais\Entities\Registration[]
     */
    function findByProjectAndUser(\MapasCulturais\Entities\Project $project, $user){
        if($user->is('guest') || !$project->id){
            return array();
        }

        $dql = "
            SELECT
                r
            FROM
                MapasCulturais\Entities\Registration r
                LEFT JOIN  MapasCulturais\Entities\RegistrationAgentRelation rar WITH rar.owner = r
            WHERE
                r.project = :project AND
                (
                    r.owner IN (:agents) OR
                    rar.agent IN (:agents)
                )";

        $q = $this->_em->createQuery($dql);

        $q->setParameters(array(
            'project' => $project,
            'agents' => $user->agents ? $user->agents->toArray() : array(-1)
        ));

        return $q->getResult();
    }

    /**
     *
     * @param \MapasCulturais\Entities\User $user
     * @param mixed $status = all all|sent|Entities\Registration::STATUS_*|array(Entities\Registration::STATUS_*, Entities\Registration::STATUS_*)
     * @return \MapasCulturais\Entities\Registration[]
     */
    function findByUser($user, $status = 'all'){
        if($user->is('guest')){
            return array();
        }

        $status_where = "";
        if($status === 'all'){
            $status = false;
        }else if($status === 'sent'){
            $status = false;
            $status_where = "r.status > 0 AND";
        }else if(is_int($status)){
            $status_where = "r.status = :status AND";
        }else if(is_array($status)){
            $status_where = "r.status IN (:status) AND";
        }

        $dql = "
            SELECT
                r
            FROM
                MapasCulturais\Entities\Registration r
                LEFT JOIN  MapasCulturais\Entities\RegistrationAgentRelation rar WITH rar.owner = r
            WHERE
                $status_where
                (
                    r.owner IN (:agents) OR
                    rar.agent IN (:agents)
                )";

        $q = $this->_em->createQuery($dql);
        $q->setParameter('agents', $user->agents ? $user->agents->toArray() : array(-1));

        if( $status !== false ){
            $q->setParameter('status', $status);
        }

        \MapasCulturais\App::i()->log->debug($dql);

        return $q->getResult();
    }
}