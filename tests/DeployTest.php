<?php

/*
 * This file is part of the znmin/laravel-deployer.
 *
 * (c) jimmy.xie <jimmy.xie@znmin.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Znmin\Deployer\Tests;

use PHPUnit\Framework\TestCase;
use Znmin\Deployer\Adapters\ExpectAdapter;
use Znmin\Deployer\Deployer;

class DeployTest extends TestCase
{
    public function testExpectDeploy()
    {
        $config = [
            'username' => '',
            'password' => '',
            'remote' => 'origin',
            'branch' => 'master',
        ];

        $deployer = new Deployer(new ExpectAdapter($config));
        $deployer->run();

        $this->assertTrue(true);
    }
}
