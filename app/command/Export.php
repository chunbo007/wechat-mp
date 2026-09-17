<?php

namespace app\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use support\think\Db;

class Export extends Command
{

    protected static $defaultName = 'db:export';
    protected static $defaultDescription = '导出数据库表结构';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::OPTIONAL, '导出目录');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('开始导出数据库表结构...');
        $exportDir = base_path() . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'table_structures';
        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0777, true);
            $output->writeln("<comment>创建目录：{$exportDir}</comment>");
        }
        
        // 清理目录中的旧文件
        $files = glob($exportDir . DIRECTORY_SEPARATOR . '*.sql');
        if (!empty($files)) {
            $deleteCount = 0;
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                    $deleteCount++;
                }
            }
            $output->writeln("<comment>清理旧文件：删除了 {$deleteCount} 个文件</comment>\n");
        }

        try {
            // 1. 获取所有表名
            $tables = Db::query("SHOW TABLES");
            
            if (empty($tables)) {
                $output->writeln("<comment>数据库中没有找到任何表</comment>");
                return Command::SUCCESS;
            }
            
            $tableCount = count($tables);
            $output->writeln("<info>找到 {$tableCount} 个表，开始导出...</info>\n");
            
            $successCount = 0;
            
            // 2. 遍历每个表
            foreach ($tables as $tableInfo) {
                // SHOW TABLES 返回的是关联数组，键名格式为 'Tables_in_数据库名'
                // 获取第一个值作为表名
                $tableName = current($tableInfo);
                
                // 3. 获取建表语句
                $createTableResult = Db::query("SHOW CREATE TABLE `{$tableName}`");
                
                if (empty($createTableResult)) {
                    $output->writeln("<error>✗ 获取表结构失败：{$tableName}</error>");
                    continue;
                }
                
                $createSql = $createTableResult[0]['Create Table'];
                
                // 去除 AUTO_INCREMENT=数字 部分
                $createSql = preg_replace('/\s+AUTO_INCREMENT=\d+/i', '', $createSql);
                
                $createSql .= ";\n";
                
                // 4. 保存到文件
                $filename = $exportDir . DIRECTORY_SEPARATOR . $tableName . '.sql';
                file_put_contents($filename, $createSql);
                
                $successCount++;
                $output->writeln("<info>✓ 导出成功：{$tableName}</info>");
            }

            $output->writeln("\n<info>========================================</info>");
            $output->writeln("<info>导出完成！成功导出 {$successCount}/{$tableCount} 个表</info>");
            $output->writeln("<info>保存路径：{$exportDir}</info>");
            $output->writeln("<info>========================================</info>");
            
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $output->writeln("<error>错误：{$e->getMessage()}</error>");
            $output->writeln("<error>堆栈跟踪：{$e->getTraceAsString()}</error>");
            return Command::FAILURE;
        }
    }

}
