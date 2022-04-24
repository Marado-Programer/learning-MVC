<?php

/**
 * 
 */

class AddToWalletController extends MainController
{
    protected function indexMain()
    {
        $this->loadModel('addToWallet/WalletModel');
        if ($params = checkArray($_POST['deposit'], 'user', 'quantity'))
            $this->model->deposit($params);
    }
}
