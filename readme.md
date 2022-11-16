---
lang: zh-tw
title: Laravel 8 自製 artisan 指令
description: 新增 Laravel 8 操作指令
---
###### tags: `Laravel` `artisan` `command` `指令`
# Laravel 8 自製指令

目的 : 自製指令 使用 artisan make:service 將自動新增 controller 、 service 、 repository 功能

## 新增 command 指令及所需檔案

```
php artisan make:command commandName
```

### 新增三個指令

```javascript=
php artisan make:command MakeService
php artisan make:command MakeRepository
php artisan make:command MakeController
```

### 新增三個預設檔案在專案底下

```javascript=
stubs\service.stub
stubs\repository.stub
stubs\cust.controller.stub
```

## 製作指令

### 製作 MakeService 指令

#### 打開指令檔案 app\Console\Commands\MakeService.php

> 將預設 use 註解或刪除 新增新的use

```javascript=
//use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
```

> 將原本 extends 更改為 GeneratorCommand

```javascript=
//class MakeService extends Command
class MakeService extends GeneratorCommand
```

> 修改指令

```javascript=
/**
 * 打 php artisan make:service 的名稱
 * 命令的名稱
 *
 * @var string
 */
protected $signature = 'make:service {name}';

/**
 * 命令說明 ( 隨自己喜歡 )
 *
 * @var string
 */
protected $description = '生成 service 物件類別';

/**
 * 生成類型
 *
 * @var string
 */
protected $type = 'Service';

/**
 * 獲取生成器的存根文件。
 *
 * @return string
 */
protected function getStub()
{
    // 對應上方新增檔案的名稱
    $stub = '/stubs/service.stub';
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
    return $rootNamespace.'\Service';
}

/**
 * 從輸入中獲取所需的類名，並添加 Service 。
 *
 * @return string
 */
protected function getNameInput()
{
    return trim($this->argument('name')).'Service';
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
        // 呼叫指令 make:custController ServerName
        $this->call('make:custController', ['name' => $repositoryClass]);
    }

    return [
        '{{ namespacedRepository }}' => 'App\Repository\\'.$repositoryClass.'Repository',
        '{{ repository }}' => class_basename($repositoryClass).'Repository',
        '{{ privateRepository }}' => lcfirst(class_basename($repositoryClass)).'Repository',
    ];
}

```

#### 打開存根檔案 service.stub

> 新增以下內容
```javascript=
<?php

namespace DummyNamespace;

use {{ namespacedRepository }};

class DummyClass
{
    private ${{ privateRepository }};

    public function __construct({{ repository }} ${{ privateRepository }})
    {
        $this->{{ privateRepository }} = ${{ privateRepository }};
    }

    public function returnIndex()
    {
        //
    }

    public function returnCreate()
    {
        //
    }

    public function returnStore($request)
    {
        //
    }

    public function returnShow($id)
    {
        //
    }

    public function returnEdit($id)
    {
        //
    }

    public function returnUpdate($request,$id)
    {
        //
    }
}

```


### 製作 MakeController 指令

#### 打開指令檔案 app\Console\Commands\MakeController.php

> 將預設 use 註解或刪除 新增新的use

```javascript=
//use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
```

> 將原本 extends 更改為 GeneratorCommand

```javascript=
//class MakeController extends Command
class MakeController extends GeneratorCommand
```

> 修改指令

```javascript=
/**
 * 打 php artisan make:controller 的名稱
 * 命令的名稱
 *
 * @var string
 */
protected $signature = 'make:custController {name}';

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
    $stub = '/stubs/cust.controller.stub';
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
        // 呼叫指令 make:custController ServerName
        $this->call('make:custController', ['name' => $repositoryClass]);
    }

        return [
            '{{ namespace }}' => 'App\Http\Controllers\\',
            '{{ service }}' => 'App\Service\\'.$repositoryClass.'Service',
            '{{ privateService }}' => lcfirst(class_basename($repositoryClass)).'Service',
            '{{ constructService }}' => class_basename($repositoryClass).'Service',

        ];
}

```

#### 打開存根檔案 cust.controller.stub

> 新增以下內容
```javascript=
<?php

namespace {{ namespace }};

use {{ rootNamespace }}Http\Controllers\Controller;
use {{ service }};
use Illuminate\Http\Request;

class {{ class }} extends Controller
{
    private ${{ privateService }};

    public function __construct({{ constructService }} ${{ privateService }})
    {
        $this->{{ privateService }} = ${{ privateService }};
    }

    public function index()
    {
        return $this->{{ privateService }}->returnIndex();
    }

    public function create()
    {
        return $this->{{ privateService }}->returnCreate();
    }

    public function store(Request $request)
    {
        return $this->{{ privateService }}->returnStore($request);
    }

    public function show($id)
    {
        return $this->{{ privateService }}->returnShow($id);
    }

    public function edit($id)
    {
        return $this->{{ privateService }}->returnEdit($id);
    }

    public function update(Request $request,$id)
    {
        return $this->{{ privateService }}->returnUpdate($request,$id);
    }
}

```


### 製作 MakeRepository 指令

#### 打開指令檔案 app\Console\Commands\MakeRepository.php

> 將預設 use 註解或刪除 新增新的use

```javascript=
//use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
```

> 將原本 extends 更改為 GeneratorCommand

```javascript=
//class MakeRepository extends Command
class MakeRepository extends GeneratorCommand
```

> 修改指令

```javascript=
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
    return $rootNamespace.'\Repository';
}

/**
 * 從輸入中獲取所需的類名，並添加 Controller 。
 *
 * @return string
 */
protected function getNameInput()
{
        return trim($this->argument('name')).'Repository';
}

```

#### 打開存根檔案 repository.stub

> 新增以下內容
```javascript=

<?php

namespace {{ namespace }};

class {{ class }}
{
    //
}

```