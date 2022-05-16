<?php

/**
 * 
 */

class AddToWalletController extends MainController
{
    protected function indexMain()
    {
        $this->loadModel('add-to-wallet/Wallet');
        if ($params = checkArray($_POST['deposit'], 'user', 'quantity'))
            $this->model->deposit($params);
    }
}
