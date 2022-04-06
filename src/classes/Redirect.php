<?php

/**
 * redirect to a certian page
 */

class Redirect
{
    protected $defaultPage;
    protected $useDefault;

    public function __construct($defaultPage = null, $useDefault = false)
    {
        $this->defaultPage = $defaultPage;
        $this->useDefault = $useDefault;
    }

    final protected function redirect($page = null)              /* improve */
    {
        var_dump($this->defaultPage);
        if (!isset($page) && $this->useDefault)
            $page = $this->defaultPage;

        echo '<meta http-equiv="Refresh" content="0; url=' . $page . '" />';
        echo '<script type="text/javascript">window.location.href = "' . $page . '";</script>';
        // header('location: '. $page);
	}

    // redirect based on the url from GET
    final protected function gotoPage($page = null)              /* improve */
    {
        if (isset($_GET['url']) && !empty($_GET['url']) && !$page)
            $page = urldecode($_GET['url']);

        if ($page) {
            $this->redirect($page);

			return;
        }
    }
}