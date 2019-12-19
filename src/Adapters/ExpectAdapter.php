<?php

/*
 * This file is part of the znmin/laravel-deployer.
 *
 * (c) jimmy.xie <jimmy.xie@znmin.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Znmin\Deployer\Adapters;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Znmin\Deployer\Exceptions\ExpectDeployException;

class ExpectAdapter extends Adapter
{
    protected $exit_code_map = [
        1 => 'Process ended abnormally.',
        2 => 'Process timeout',
        3 => 'Username not found',
        4 => 'Password error',
    ];

    /**
     * 执行部署
     *
     * @throws ExpectDeployException
     * @throws \Znmin\Deployer\Exceptions\DeployException
     */
    public function deploy()
    {
        $this->expectMustInstalled();

        $this->runDeploy();
    }

    /**
     * @throws ExpectDeployException
     * @throws \Znmin\Deployer\Exceptions\DeployException
     */
    protected function runDeploy()
    {
        $process = new Process($this->buildDeployCommand());
        $process->run();

        if (! $process->isSuccessful()) {
            if ($this->exit_code_map[$process->getExitCode()]) {
                throw new ExpectDeployException($this->exit_code_map[$process->getExitCode()]);
            }

            throw new ProcessFailedException($process);
        }
    }

    /**
     * @return mixed
     *
     * @throws ExpectDeployException
     */
    protected function getUsername()
    {
        return $this->config['username'] ?? '';
    }

    /**
     * @return mixed
     *
     * @throws ExpectDeployException
     */
    protected function getPassword()
    {
        return $this->config['password'] ?? '';
    }

    /**
     * @return mixed
     *
     * @throws ExpectDeployException
     */
    protected function getBranch()
    {
        if (! empty($this->config['branch'])) {
            return $this->config['branch'];
        }

        throw new ExpectDeployException('expect deploy branch not defined.');
    }

    /**
     * @return mixed
     *
     * @throws ExpectDeployException
     */
    protected function getRemote()
    {
        if (! empty($this->config['remote'])) {
            return $this->config['remote'];
        }

        throw new ExpectDeployException('expect deploy remote not defined.');
    }

    /**
     * 判断 expect 是否安装
     */
    protected function expectMustInstalled()
    {
        $process = Process::fromShellCommandline('/usr/bin/expect');
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ExpectDeployException('expect command not found');
        }
    }

    /**
     * 判断 expect 是否安装.
     *
     * @throws ExpectDeployException
     * @throws \Znmin\Deployer\Exceptions\DeployException
     */
    protected function buildDeployCommand()
    {
        return [
            // handle shell
            '/usr/bin/expect',
            __DIR__ . "/../../shells/deploy.sh",

            // params
            $this->getUsername(),
            $this->getPassword(),
            $this->getDeployPath(),
            $this->getBranch(),
            $this->getRemote(),
        ];
    }
}
