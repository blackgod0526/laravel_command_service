<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeController extends GeneratorCommand
{
	/**
	 * 打 php artisan make:controller 的名稱
	 * 命令的名稱
	 *
	 * @var string
	 */
	protected $signature = 'make:customController {name}';

	/**
	 * 命令說明 ( 隨自己喜歡 )
	 *
	 * @var string
	 */
	protected $description = '生成 Controller 物件類別';

	/**
	 * 生成類型
	 *
	 * @var string
	 */
	protected $type = 'Controller';

	/**
	 * 獲取生成器的存根文件。
	 *
	 * @return string
	 */
	protected function getStub()
	{
		// 對應上方新增檔案的名稱
		$stub = '/stubs/custom.controller.stub';
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
			: __DIR__.$stub;
	}

	/**
	 * 獲取類別的默認命名空間。
	 *
	 * @param  string  $rootNamespace
	 * @return string
	 */
	protected function getDefaultNamespace($rootNamespace)
	{
		return $rootNamespace.'\Http\Controllers';
	}

	/**
	 * 從輸入中獲取所需的類名，並添加 Controller 。
	 *
	 * @return string
	 */
	protected function getNameInput()
	{
		return trim($this->argument('name')).'Controller';
	}

	/**
	 * 使用給定名稱構建類。
	 *
	 * @param  string  $name
	 * @return string
	 *
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	protected function buildClass($repository)
	{
		$replace = [];

		if ($this->argument('name')) {
			$replace = $this->buildRepositoryReplacements();
		}

		return str_replace(
			array_keys($replace), array_values($replace), parent::buildClass($repository)
		);
	}

	/**
	 * 構建存儲庫替換值。
	 *
	 * @return array
	 */
	protected function buildRepositoryReplacements()
	{
		$repositoryClass = $this->argument('name');

		if (! class_exists($repositoryClass)) {
			// 呼叫指令 make:repository ServerName
			$this->call('make:repository', ['name' => $repositoryClass]);
			// 呼叫指令 make:customController ServerName
			$this->call('make:customController', ['name' => $repositoryClass]);
		}

			return [
				'{{ namespace }}' => 'App\Http\Controllers\\',
				'{{ service }}' => 'App\Service\\'.$repositoryClass.'Service',
				'{{ privateService }}' => lcfirst(class_basename($repositoryClass)).'Service',
				'{{ constructService }}' => class_basename($repositoryClass).'Service',

			];
	}

}
