<?php

namespace Opifer\RedirectBundle\Tests\TestData;

use Opifer\RedirectBundle\Model\Redirect as BaseRedirect;

class Redirect extends BaseRedirect
{
    /**
     * Added an ID setter for testing purposes
     *
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
