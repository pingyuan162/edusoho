<?php

namespace Topxia\Api\Resource;

use Silex\Application;

class ThreadPost extends BaseResource
{
	public function filter($res)
    {
        $res['createdTime'] = date('c', $res['createdTime']);
        return $res;
    }
}