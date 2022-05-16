<?php

/**
 * 
 */

class PayQuotaModel extends MainModel
{
    public function transfer($params)
    {
        $user = $this->instancer->instanceUserByID($params['user']);
        $association = $this->instancer->instanceAssociationByID($params['association']);

        foreach ($user->getQuotas() as $quota)
            if ($quota->association->getID() == $association->getID()) {
                $user->payQuota($quota, $params['quantity']);

                break;
            }
        $this->controller->tools->getRedirect()->redirect($_SERVER['HTTP_REFERER']);
    }
}
