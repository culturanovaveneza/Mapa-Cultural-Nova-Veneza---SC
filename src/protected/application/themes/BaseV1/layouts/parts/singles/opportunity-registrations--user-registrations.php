<?php
$params = $app->request();
$registrationsCount = $app->repo('Registration')->countOpportunityAndUserPaginated($entity, $app->user);
$registrations = $app->repo('Registration')->findByOpportunityAndUserPaginated($entity, $app->user, ($params->get('page')) ? $params->get('page'): 1  );

$pages = $registrationsCount / 5;
$pages = ceil($pages);
?>
<?php if ($registrations): ?>
    <table class="my-registrations">
        <caption><?php \MapasCulturais\i::_e("Minhas inscrições");?></caption>
        <thead>
            <tr>
                <th class="registration-id-col">
                    <?php \MapasCulturais\i::_e("Inscrição");?>
                </th>
                <th class="registration-agents-col">
                    <?php \MapasCulturais\i::_e("Agentes");?>
                </th>
                <th class="registration-status-col">
                    <?php \MapasCulturais\i::_e("Status");?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($registrations as $registration):
                $reg_args = ['registration' => $registration, 'opportunity' => $entity]; 
                ?>
                <tr>
                    <?php $this->applyTemplateHook('user-registration-table--registration', 'begin', $reg_args); ?>
                    <td class="registration-id-col">
                        <?php $this->applyTemplateHook('user-registration-table--registration--number', 'begin', $reg_args); ?>
                        <a href="<?php echo $registration->singleUrl ?>"><?php echo $registration->number ?></a>
                        <?php $this->applyTemplateHook('user-registration-table--registration--number', 'end', $reg_args); ?>
                    </td>
                    <td class="registration-agents-col">
                        <?php $this->applyTemplateHook('user-registration-table--registration--agents', 'begin', $reg_args); ?>
                        <p>
                            <span class="label"><?php \MapasCulturais\i::_e("Responsável");?></span><br>
                            <?php echo $registration->owner->name ?>
                        </p>
                        <?php
                        foreach ($app->getRegisteredRegistrationAgentRelations() as $def):
                            if (!$entity->useRegistrationAgentRelation($def))
                                continue;
                            ?>
                            <?php if ($agents = $registration->getRelatedAgents($def->agentRelationGroupName)): ?>
                                <p>
                                    <span class="label"><?php echo $def->label ?></span><br>
                                    <?php echo $agents[0]->name ?>
                                </p>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php $this->applyTemplateHook('user-registration-table--registration--agents', 'end', $reg_args); ?>
                    </td>
                    <td class="registration-status-col">
                        <?php $this->applyTemplateHook('user-registration-table--registration--status', 'begin', $reg_args); ?>
                        <?php if ($registration->status > 0): ?>
                            <?php \MapasCulturais\i::_e("Enviada em");?> <?php echo $registration->sentTimestamp ? $registration->sentTimestamp->format('d/m/Y à\s H:i'): ''; ?>.
                        <?php else: ?>
                            <?php \MapasCulturais\i::_e("Não enviada.");?><br>
                            <a class="btn btn-small btn-primary" href="<?php echo $registration->singleUrl ?>"><?php \MapasCulturais\i::_e("Editar e enviar");?></a>
                        <?php endif; ?>
                        <?php $this->applyTemplateHook('user-registration-table--registration--status', 'end', $reg_args); ?>
                    </td>
                    <?php $this->applyTemplateHook('user-registration-table--registration', 'end', $reg_args); ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php for($i = 1; $i <= $pages; $i++): ?>
        <?php
        $selected = "";
        if($params->get('page') == $i){
            $selected = "btn-primary";
        }elseif($params->get('page') == null && $i == 1){
            $selected = "btn-primary";
        }?>

        <a class="btn btn-small <?php echo $selected; ?>" href="<?php echo $entity->singleUrl ?>?page=<?php echo $i?>" ><?php echo $i; ?></a>
<!--        --><?php //?>
    <?php endfor; ?>
<?php endif; ?>
