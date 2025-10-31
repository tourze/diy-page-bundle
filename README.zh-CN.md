# DIY Page Bundle - 装修/广告位管理包

[![PHP Version](https://img.shields.io/badge/php-8.1%2B-blue.svg)](https://www.php.net/)
[![Symfony Version](https://img.shields.io/badge/symfony-6.4%2B-green.svg)](https://symfony.com/)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen.svg)](#)
[![Code Coverage](https://img.shields.io/badge/coverage-95%25-brightgreen.svg)](#)

[English](README.md) | [中文](README.zh-CN.md)

一个功能完善的 Symfony 包，用于管理动态页面内容、广告位和装修系统。基于 Block、Element 
架构设计，支持表达式规则引擎、时间控制和访问统计。

## 目录

- [快速开始](#快速开始)
- [安装](#安装)
- [核心概念](#核心概念)
- [基础使用](#基础使用)
- [配置](#配置)
- [高级用法](#高级用法)
- [API 接口](#api-接口)
- [管理界面](#管理界面)
- [测试](#测试)
- [架构概览](#架构概览)
- [依赖要求](#依赖要求)
- [贡献指南](#贡献指南)
- [许可证](#许可证)

## 快速开始

3分钟快速开始使用 DIY Page Bundle：

```bash
# 1. 安装包
composer require tourze/diy-page-bundle

# 2. 创建数据库表
php bin/console doctrine:migrations:migrate

# 3. 加载示例数据（可选）
php bin/console doctrine:fixtures:load
```

```php
// 4. 在控制器或命令中创建第一个广告位
use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\Element;

$block = new Block();
$block->setCode('welcome_banner')
      ->setTitle('欢迎横幅')
      ->setValid(true);

$element = new Element();
$element->setTitle('欢迎来到我们的网站！')
        ->setThumb1('/images/welcome.jpg')
        ->setPath('/welcome')
        ->setValid(true)
        ->setBlock($block);

$entityManager->persist($block);
$entityManager->persist($element);
$entityManager->flush();
```

```php
// 5. 在模板中显示
$blocks = $rpcClient->call('GetDiyPageElementByCode', ['codes' => ['welcome_banner']]);
```

## 安装

```bash
composer require tourze/diy-page-bundle
```

## 核心概念

### Block（广告位）
页面中的内容容器，支持：
- 唯一标识符（code）
- 显示规则表达式
- 时间控制（开始/结束时间）
- 排序和优先级
- 自定义属性扩展

### Element（元素）
广告位中的具体内容项，支持：
- 图片和标题
- 跳转路径
- 自定义属性
- 显示控制
- 点击统计

### BlockAttribute（广告位属性）
广告位的键值对配置：
- 自定义广告位属性
- 配置参数
- 显示设置
- 可扩展的元数据

### VisitLog（访问日志）
记录用户访问行为，用于：
- 统计分析
- 用户行为追踪
- 数据驱动决策
- 个性化推荐

## 特性

- 🎨 **动态页面装修**：基于 Block-Element 架构的可视化页面装修系统
- 📱 **广告位管理**：支持多种类型广告位的创建和管理
- 🔧 **规则引擎**：支持表达式规则控制内容显示逻辑
- ⏰ **时间控制**：支持定时展示和时效性内容管理
- 📊 **访问统计**：自动记录用户访问行为并支持数据分析
- 🔄 **事件系统**：提供扩展点支持自定义数据格式化
- 💾 **缓存支持**：内置缓存机制提升性能
- 🗑️ **自动清理**：访问日志自动清理，可配置保留天数

## 基础使用

### 创建广告位

```php
use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\Element;

// 创建广告位
$block = new Block();
$block->setCode('homepage_banner')
      ->setTitle('首页横幅')
      ->setShowExpression('user.isVip or env.DEBUG')
      ->setBeginTime(new \DateTime('2024-01-01'))
      ->setEndTime(new \DateTime('2024-12-31'))
      ->setSortNumber(100);

// 添加元素
$element = new Element();
$element->setTitle('新年促销')
        ->setImageUrl('/images/banner.jpg')
        ->setJumpPath('/promotion/newyear')
        ->setSortNumber(1)
        ->setBlock($block);

$entityManager->persist($block);
$entityManager->persist($element);
$entityManager->flush();
```

### 获取广告位数据

```php
// 使用 JSON-RPC 接口获取数据
$response = $this->rpcClient->call('GetDiyPageElementByCode', [
    'codes' => ['homepage_banner', 'sidebar_ad']
]);

// 处理返回数据
foreach ($response as $code => $elements) {
    foreach ($elements as $element) {
        echo $element['title'];    // 元素标题
        echo $element['imageUrl']; // 图片URL
        echo $element['jumpPath']; // 跳转路径
    }
}
```

### 表达式规则示例

```php
// 基于用户属性
$block->setShowExpression('user.level >= 3');

// 基于环境变量
$block->setShowExpression('env.FEATURE_FLAG_ENABLED');

// 复合条件
$block->setShowExpression('user.isVip and datetime.now >= "2024-01-01"');

// 基于访问记录
$block->setShowExpression('not visitLog(user.id, "homepage_banner")');
```

## 配置

### 访问日志保留天数

```yaml
# config/packages/diy_page.yaml
parameters:
    env(DIY_PAGE_VISIT_LOG_PERSIST_DAY_NUM): 7  # 默认保留7天
```

## 高级用法

### 事件监听器

```php
use DiyPageBundle\Event\BlockDataFormatEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BlockDataFormatSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            BlockDataFormatEvent::class => 'onBlockDataFormat',
        ];
    }

    public function onBlockDataFormat(BlockDataFormatEvent $event)
    {
        $data = $event->getData();
        // 自定义数据处理
        $data['customField'] = 'customValue';
        $event->setData($data);
    }
}
```

### 表达式语言函数

本包提供了自定义的表达式函数：

- `visitLog(userId, blockCode)`: 获取特定用户和广告位的访问记录
- 可通过 `VisitLogFunctionProvider` 添加更多自定义函数

### 最佳实践

#### 1. 广告位编码规范
```php
// 使用语义化的编码
$block->setCode('homepage_top_banner');     // 首页顶部横幅
$block->setCode('product_list_sidebar');    // 产品列表侧边栏
$block->setCode('checkout_promotion');      // 结账页促销
```

#### 2. 性能优化
```php
// 批量获取多个广告位，减少查询次数
$codes = ['banner1', 'banner2', 'banner3'];
$response = $rpcClient->call('GetDiyPageElementByCode', ['codes' => $codes]);

// 使用缓存
$cacheKey = 'diy_page_' . $blockCode;
$elements = $cache->get($cacheKey, function() use ($blockCode) {
    return $this->elementRepository->findByBlockCode($blockCode);
});
```

## API 接口

### GetDiyPageElementByCode

根据广告位代码批量获取元素数据：

```php
$response = $rpcClient->call('GetDiyPageElementByCode', [
    'codes' => ['banner', 'sidebar'],
    'limit' => 10
]);

// 返回格式
[
    'banner' => [
        ['id' => 1, 'title' => '...', 'imageUrl' => '...'],
        // ...
    ],
    'sidebar' => [
        // ...
    ]
]
```

### GetOneDiyPageElement

获取单个元素详情：

```php
$response = $rpcClient->call('GetOneDiyPageElement', [
    'id' => 123
]);

// 返回格式
[
    'id' => 123,
    'title' => '新年促销',
    'imageUrl' => '/images/banner.jpg',
    'jumpPath' => '/promotion/newyear',
    'blockCode' => 'homepage_banner'
]
```

## 管理界面

包集成了 EasyAdmin，提供完整的后台管理界面：

1. **广告位管理**：创建、编辑、删除广告位
2. **元素管理**：管理广告位中的具体内容
3. **访问统计**：查看访问日志和统计数据

访问 `/admin` 即可使用管理界面。

## 测试

运行包的测试套件：

```bash
# 运行所有测试
./vendor/bin/phpunit packages/diy-page-bundle/tests

# 运行特定测试
./vendor/bin/phpunit packages/diy-page-bundle/tests/Entity/BlockTest.php
```

## 架构概览

```text
DiyPageBundle/
├── Entity/              # 领域模型
│   ├── Block.php       # 广告位实体
│   ├── BlockAttribute.php # 广告位属性
│   ├── Element.php     # 内容元素实体
│   └── VisitLog.php    # 访问日志实体
├── Repository/          # 数据访问层
│   ├── BlockRepository.php
│   ├── ElementRepository.php
│   └── VisitLogRepository.php
├── Service/            # 业务逻辑
│   └── AdminMenu.php   # 管理菜单服务
├── Event/              # 事件类
│   ├── BlockDataFormatEvent.php
│   └── ElementDataFormatEvent.php
├── Procedure/          # JSON-RPC 过程
│   ├── GetDiyPageElementByCode.php
│   └── GetOneDiyPageElement.php
├── ExpressionLanguage/ # 自定义表达式函数
│   └── Function/
│       └── VisitLogFunctionProvider.php
└── DataFixtures/       # 测试数据
    └── BasicBlockFixtures.php
```

## 依赖要求

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM 3.0+
- Expression Language 组件
- JSON-RPC Core 包
- 多个 Tourze 增强功能包

## 设计参考

- [产品经理必学的App广告位设计](http://www.woshipm.com/pd/2028587.html)
- [商家云 - 广告位管理方案](https://www.shangtaoyun.net/mnewsdetail-1006.html)

## 路线图

1. 针对地区设定不同的广告信息
2. 针对用户等级设定不同的广告信息
3. 不同时段不同的广告信息
4. 增加 A/B 测试支持
5. 完善统计分析功能
6. 支持更多元素类型（视频、轮播图等）
7. 增加预览功能

## 贡献指南

欢迎提交 Issue 和 Pull Request。请确保：

1. 代码符合 PSR-12 标准
2. 添加相应的单元测试
3. 更新相关文档

## 许可证

MIT License