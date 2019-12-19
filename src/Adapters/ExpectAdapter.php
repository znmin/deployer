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

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Znmin\Deployer\Exceptions\ExpectDeployException;

class ExpectAdapter extends Adapter
{
    protected $exit_code_map = [
        1 => 'Process ended abnormally.',
        2 => 'Process timeout.',
        3 => 'Username not found.',
        4 => 'Password error.',
        5 => 'Not found deploy directory.',
    ];

    /**
     * 执行部署.
     *
     * @throws ExpectDeployException
     * @throws \Znmin\Deployer\Exceptions\DeployException
     */
    public function deploy()
    {
        $this->expectMustInstalled();

        $this->deployPathMustExists();

        if ($this->loginIsSuccessful()) {
            $this->runUserDeploy();
        } else {
            $this->runLocalDeploy();
        }
    }

    /**
     * 执行指定用户部署.
     *
     * @throws ExpectDeployException
     * @throws \Znmin\Deployer\Exceptions\DeployException
     */
    protected function runUserDeploy()
    {
        $process = new Process($this->buildUserDeployCommand());
        $process->run();

        if (! $process->isSuccessful()) {
            if ($this->exit_code_map[$process->getExitCode()]) {
                throw new ExpectDeployException($this->exit_code_map[$process->getExitCode()]);
            }

            throw new ProcessFailedException($process);
        }
    }

    /**
     * 执行本地部署命令.
     *
     * @throws ExpectDeployException
     * @throws \Znmin\Deployer\Exceptions\DeployException
     */
    protected function runLocalDeploy()
    {
        $process = Process::fromShellCommandline($this->buildLocalDeployCommand());
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ExpectDeployException('username or password error');
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
     * 判断 expect 是否安装.
     *
     * @throws ExpectDeployException
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
     * 判断部署目录是否存在.
     *
     * @throws \Znmin\Deployer\Exceptions\DeployException
     * @throws ExpectDeployException
     */
    protected function deployPathMustExists()
    {
        $process = Process::fromShellCommandline('cd '.$this->getDeployPath());
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ExpectDeployException('deploy path not found.');
        }
    }

    /**
     * 判断部署用户是否登录成功
     *
     * @throws ExpectDeployException
     */
    protected function loginIsSuccessful()
    {
        $process = new Process([
            '/usr/bin/expect',
            __DIR__.'/../../shells/login.sh',
            $this->getUsername(),
            $this->getPassword(),
        ]);
        $process->run();

        return $process->isSuccessful();
    }

    /**
     * 构建指定用户部署命令.
     *
     * @throws ExpectDeployException
     * @throws \Znmin\Deployer\Exceptions\DeployException
     */
    protected function buildUserDeployCommand()
    {
        return [
            // handle shell
            '/usr/bin/expect',
            __DIR__.'/../../shells/user_deploy.sh',

            // params
            $this->getUsername(),
            $this->getPassword(),
            $this->getDeployPath(),
            $this->getBranch(),
            $this->getRemote(),
        ];
    }

    /**
     * 构建本地用户部署命令.
     *
     * @throws ExpectDeployException
     * @throws \Znmin\Deployer\Exceptions\DeployException
     */
    protected function buildLocalDeployCommand()
    {
        return "cd /test{$this->getDeployPath()} && git pull {$this->getRemote()} {$this->getBranch()}";
    }
}
