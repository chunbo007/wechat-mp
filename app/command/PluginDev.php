<?php

namespace app\command;

use app\service\PluginLifecycleService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PluginDev extends Command
{
    protected static $defaultName = 'plugin:dev';
    protected static $defaultDescription = '管理本地开发插件的菜单、计划任务和数据字典';

    protected function configure(): void
    {
        $this->setHelp(<<<'HELP'
本地开发插件管理命令，只处理 manifest/install.php 声明的公共资源。

示例：
  php webman plugin:dev install wechat
  php webman plugin:dev uninstall wechat_free

install 会同步菜单、计划任务和数据字典；uninstall 只移除这些公共资源，
不会执行 database/uninstall.sql，不会删除插件源码、前端资源或业务表。
HELP)
            ->addArgument('action', InputArgument::REQUIRED, '操作：install 或 uninstall')
            ->addArgument('plugin', InputArgument::REQUIRED, '插件代码，例如 wechat 或 wechat_free');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $action = strtolower(trim((string)$input->getArgument('action')));
        $pluginCode = trim((string)$input->getArgument('plugin'));
        if (!in_array($action, ['install', 'uninstall'], true)) {
            $output->writeln('<error>操作仅支持 install 或 uninstall</error>');
            return Command::INVALID;
        }
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $pluginCode)) {
            $output->writeln('<error>插件代码无效</error>');
            return Command::INVALID;
        }

        $manifestFile = base_path() . '/plugin/' . $pluginCode . '/manifest/install.php';
        if (!is_file($manifestFile)) {
            $output->writeln("<error>插件安装清单不存在: {$manifestFile}</error>");
            return Command::FAILURE;
        }

        try {
            $lifecycle = new PluginLifecycleService();
            $result = $action === 'install'
                ? $lifecycle->installManifest($manifestFile, $pluginCode)
                : $lifecycle->removeManifestResources($manifestFile, $pluginCode);
            $output->writeln("<info>{$pluginCode} 本地开发资源{$action}完成</info>");
            $output->writeln(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln('<error>执行失败: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
