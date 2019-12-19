<?php

/*
 * This file is part of the znmin/deployer.
 *
 * (c) jimmy.xie <jimmy.xie@znmin.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Znmin\Deployer\Adapters;

use Znmin\Deployer\Contracts\Adapter as AdapterContract;
use Znmin\Deployer\Exceptions\DeployException;

abstract class Adapter implements AdapterContract
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return false|mixed|string
     *
     * @throws DeployException
     */
    protected function getDeployPath()
    {
        if (! empty($this->config['deploy_path'])) {
            return $this->config['deploy_path'];
        }

        do {
            $deploy_path = realpath(($deploy_path ?? __DIR__).'/../');

            if ('/' == $deploy_path) {
                throw new DeployException('deploy path not defined.');
            }
        } while (! file_exists($deploy_path.'/.git'));

        return $deploy_path;
    }
}
