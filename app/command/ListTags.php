<?php

namespace app\command;

use app\service\GitPatchGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListTags extends Command
{
    protected static $defaultName = 'patch:list-tags';
    protected static $defaultDescription = '列出Git仓库中的标签';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setHelp('此命令用于列出Git仓库中的所有标签')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, '显示标签数量限制', 20);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $limit = (int)$input->getOption('limit');

        try {
            $generator = new GitPatchGenerator();
            
            $output->writeln("<info>正在获取Git标签...</info>");
            
            $tags = $generator->getRecentTags($limit);
            
            if (empty($tags)) {
                $output->writeln("<comment>未找到任何Git标签</comment>");
                return Command::SUCCESS;
            }
            
            $output->writeln("<info>找到 " . count($tags) . " 个标签:</info>");
            $output->writeln("");
            
            foreach ($tags as $index => $tag) {
                $output->writeln(sprintf("<comment>%2d.</comment> <info>%s</info>", $index + 1, $tag));
            }
            
            $output->writeln("");
            $output->writeln("<comment>使用示例:</comment>");
            $nextTag = isset($tags[1]) ? $tags[1] : 'next-tag';
            $output->writeln("php webman patch:generate {$tags[0]} {$nextTag}");
            
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $output->writeln("<error>❌ 执行失败: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }
    }
}