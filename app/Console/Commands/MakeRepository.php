<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeRepository extends GeneratorCommand
{
	/**
	 * 打 php artisan make:repository 的名稱
	 * 命令的名稱
	 *
	 * @var string
	 */
	protected $signature = 'make:repository {name}';

	/**
	 * 命令說明 ( 隨自己喜歡 )
	 *
	 * @var string
	 */
	protected $description = '生成 Repository 物件類別';

	/**
	 * 生成類型
	 *
	 * @var string
	 */
	protected $type = 'Repository';

	/**
	 * 獲取生成器的存根文件。
	 *
	 * @return string
	 */
	protected function getStub()
	{
		// 對應上方新增檔案的名稱
		$stub = '/stubs/repository.stub';
		return $this->resolveStubPath($stub);
	}

	/**
	 * 解析存根的完全限定路徑
	 *
	 * @param  string  $stub
	 * @return string
	 */
	protected function resolveStubPath($stub)
	{
		return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
			? $customPath
			: __DIR__ . $stub;
	}

	/**
	 * 獲取類別的默認命名空間。
	 *
	 * @param  string  $rootNamespace
	 * @return string
	 */
	protected function getDefaultNamespace($rootNamespace)
	{
		return $rootNamespace . '\Repository';
	}

	/**
	 * 從輸入中獲取所需的類名，並添加 Controller 。
	 *
	 * @return string
	 */
	protected function getNameInput()
	{
		return trim($this->argument('name')) . 'Repository';
	}
}
