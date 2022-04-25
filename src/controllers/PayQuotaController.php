<?php

/**
 * 
 */

class PayQuotaController extends MainController
{
    protected function indexMain()
    {
        $this->loadModel('pay-quota/PayQuotaModel');

        if ($params = checkArray($_POST['payQuota'], 'user', 'association', 'quantity'))
            $this->model->transfer($params);
    }
}
