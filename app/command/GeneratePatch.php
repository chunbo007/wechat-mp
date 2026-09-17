<?php

namespace app\command;

use app\service\GitPatchGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class GeneratePatch extends Command
{
    protected static $defaultName = 'patch:generate';
    protected static $defaultDescription = '生成Git版本间的补丁包';

    /**
     * @return void
     */
    protected function configure()
    {
        $helpText = "此命令用于生成两个Git标签版本之间的补丁包，或生成从指定版本到最新提交的补丁包\n\n" .
                   "使用示例:\n" .
                   "  # 生成从最新标签到最新提交的补丁包\n" .
                   "  php webman patch:generate\n\n" .
                   "  # 生成从指定标签到最新提交的补丁包\n" .
                   "  php webman patch:generate v1.0.1\n\n" .
                   "  # 生成从指定标签到最新提交的补丁包（显式指定）\n" .
                   "  php webman patch:generate v1.0.1 --latest\n\n" .
                   "  # 生成两个指定标签之间的补丁包\n" .
                   "  php webman patch:generate v1.0.1 v1.0.2\n\n" .
                   "  # 生成指定插件的补丁包\n" .
                   "  php webman patch:generate --plugin wechat\n\n" .
                   "  # 生成指定插件从指定版本到最新提交的补丁包\n" .
                   "  php webman patch:generate v1.0.0 --plugin wechat\n\n" .
                   "  # 生成指定插件两个版本之间的补丁包\n" .
                   "  php webman patch:generate v1.0.0 v1.0.1 --plugin wechat";
        
        $this->setHelp($helpText)
            ->addArgument('from-version', InputArgument::OPTIONAL, '起始版本标签（可选，如果不提供则使用最新的标签）')
            ->addArgument('to-version', InputArgument::OPTIONAL, '目标版本标签（可选，如果不提供则使用最新提交）')
            ->addOption('latest', 'l', InputOption::VALUE_NONE, '使用最新提交作为目标版本')
            ->addOption('output', 'o', InputOption::VALUE_OPTIONAL, '输出目录路径', null)
            ->addOption('plugin', 'p', InputOption::VALUE_OPTIONAL, '指定插件名称，生成插件补丁包', null);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    /**
     * 递归删除目录
     * @param string $dir
     */
    protected function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fromVersion = $input->getArgument('from-version');
        $toVersion = $input->getArgument('to-version');
        $useLatest = $input->getOption('latest');
        $customOutput = $input->getOption('output');
        $pluginName = $input->getOption('plugin');

        // 确定仓库路径
        $repoPath = base_path();
        $isPluginPatch = !empty($pluginName);

        if ($isPluginPatch) {
            $pluginPath = base_path() . '/plugin/' . $pluginName;
            if (!is_dir($pluginPath)) {
                $output->writeln("<error>❌ 插件目录不存在: {$pluginPath}</error>");
                return Command::FAILURE;
            }
            // $repoPath = $pluginPath;
            $output->writeln("<comment>生成插件补丁: {$pluginName}</comment>");
        }

        // 创建Git补丁生成器（传入仓库路径）
        $generator = new GitPatchGenerator($repoPath, $pluginName);
        
        // 如果没有提供起始版本，则使用最新的标签
        if (empty($fromVersion)) {
            $recentTags = $generator->getRecentTags(1);
            if (empty($recentTags)) {
                $output->writeln("<error>❌ 没有找到任何Git标签，请先创建标签</error>");
                return Command::FAILURE;
            }
            $fromVersion = $recentTags[0];
            $output->writeln("<comment>未指定起始版本，使用最新标签: {$fromVersion}</comment>");
        }
        
        // 如果没有提供目标版本或使用--latest选项，则使用最新提交
        if (empty($toVersion) || $useLatest) {
            try {
                $latestCommitInfo = $generator->getLatestCommitInfo();
                $toVersion = 'commit-' . substr($latestCommitInfo['hash'], 0, 8); // 使用提交哈希的前8位
                $output->writeln("<comment>未指定目标版本，使用最新提交: {$toVersion}</comment>");
                $output->writeln("<comment>提交信息: {$latestCommitInfo['message']}</comment>");
                $output->writeln("<comment>提交作者: {$latestCommitInfo['author_name']}</comment>");
            } catch (\Exception $e) {
                $output->writeln("<error>❌ 无法获取最新提交信息: {$e->getMessage()}</error>");
                return Command::FAILURE;
            }
        }

        $output->writeln("<info>开始生成补丁包...</info>");
        $output->writeln("<comment>从版本: {$fromVersion}</comment>");
        $output->writeln("<comment>到版本: {$toVersion}</comment>");

        try {
            // 确定输出路径
            if ($customOutput) {
                $outputPath = rtrim($customOutput, '/') . '/' . $toVersion;
            } else {
                $outputPath = runtime_path() . '/patches/' . $toVersion;
            }

            // 自动清理已存在的目录
            if (is_dir($outputPath)) {
                $this->removeDirectory($outputPath);
            }

            $output->writeln("<info>正在生成补丁包...</info>");
            
            // 生成补丁包
            $result = $generator->generatePatch($fromVersion, $toVersion, $outputPath);
            
            if ($result['success']) {
                $output->writeln("<info>✅ 补丁包生成成功!</info>");
                $output->writeln("<comment>输出路径: {$result['output_path']}</comment>");
                $output->writeln("<comment>ZIP文件: {$result['zip_file']}</comment>");
                $output->writeln("<comment>变更文件数: " . count($result['files_copied']) . "</comment>");
                
                if (isset($result['patch_info']['sql_changes_count'])) {
                    $output->writeln("<comment>SQL变更数: {$result['patch_info']['sql_changes_count']}</comment>");
                }
                
                $output->writeln("<info>补丁包详情:</info>");
                $output->writeln(json_encode($result['patch_info'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                
                return Command::SUCCESS;
            } else {
                $output->writeln("<error>❌ 补丁包生成失败: {$result['message']}</error>");
                return Command::FAILURE;
            }

        } catch (\Exception $e) {
            $output->writeln("<error>❌ 执行失败: {$e->getMessage()}</error>");
            $output->writeln("<comment>详细错误信息:</comment>");
            $output->writeln($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}