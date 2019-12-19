<?php

/*
 * This file is part of the znmin/deployer.
 *
 * (c) jimmy.xie <jimmy.xie@znmin.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Znmin\Deployer;

use Znmin\Deployer\Contracts\Adapter;

class Deployer
{
    /**
     * 部署适配器.
     *
     * @var Adapter
     */
    protected $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * 开始部署.
     */
    public function run()
    {
        $this->adapter->deploy();
    }
}
