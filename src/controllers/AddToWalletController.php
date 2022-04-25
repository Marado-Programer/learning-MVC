<?php

/**
 * 
 */

class AddToWalletController extends MainController
{
    protected function indexMain()
    {
        $this->loadModel('add-to-wallet/WalletModel');
        if ($params = checkArray($_POST['deposit'], 'user', 'quantity'))
            $this->model->deposit($params);
    }
}
