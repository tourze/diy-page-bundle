# DIY Page Bundle - Dynamic Page & Advertisement Management

[![PHP Version](https://img.shields.io/badge/php-8.1%2B-blue.svg)](https://www.php.net/)
[![Symfony Version](https://img.shields.io/badge/symfony-6.4%2B-green.svg)](https://symfony.com/)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen.svg)](#)
[![Code Coverage](https://img.shields.io/badge/coverage-95%25-brightgreen.svg)](#)

[English](README.md) | [中文](README.zh-CN.md)

A comprehensive Symfony bundle for managing dynamic page content, advertisement blocks, and page 
decoration systems. Built on a Block-Element architecture with support for expression rule engine, 
time-based control, and visitor tracking.

## Table of Contents

- [Quick Start](#quick-start)
- [Installation](#installation)
- [Core Concepts](#core-concepts)
- [Basic Usage](#basic-usage)
- [Configuration](#configuration)
- [Advanced Usage](#advanced-usage)
- [API Endpoints](#api-endpoints)
- [Admin Interface](#admin-interface)
- [Testing](#testing)
- [Architecture Overview](#architecture-overview)
- [Dependencies](#dependencies)
- [Contributing](#contributing)
- [License](#license)

## Quick Start

Get started with DIY Page Bundle in 3 minutes:

```bash
# 1. Install the bundle
composer require tourze/diy-page-bundle

# 2. Create database tables
php bin/console doctrine:migrations:migrate

# 3. Load sample data (optional)
php bin/console doctrine:fixtures:load
```

```php
// 4. Create your first block in controller or command
use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\Element;

$block = new Block();
$block->setCode('welcome_banner')
      ->setTitle('Welcome Banner')
      ->setValid(true);

$element = new Element();
$element->setTitle('Welcome to Our Site!')
        ->setThumb1('/images/welcome.jpg')
        ->setPath('/welcome')
        ->setValid(true)
        ->setBlock($block);

$entityManager->persist($block);
$entityManager->persist($element);
$entityManager->flush();
```

```php
// 5. Display in your template
$blocks = $rpcClient->call('GetDiyPageElementByCode', ['codes' => ['welcome_banner']]);
```

## Installation

```bash
composer require tourze/diy-page-bundle
```

## Core Concepts

### Block (Advertisement Position)
Content containers within pages supporting:
- Unique identifier (code)
- Display rule expressions
- Time control (start/end time)
- Sorting and priority

### Element (Content Item)
Specific content items within blocks supporting:
- Images and titles
- Jump paths/URLs
- Custom attributes
- Display control
- Subtitle and description

### BlockAttribute (Block Attributes)
Key-value pairs for block configuration:
- Custom block properties
- Configuration parameters
- Display settings
- Extensible metadata

### VisitLog (Access Tracking)
User behavior tracking for:
- Statistical analysis
- User behavior insights
- Data-driven decisions

## Features

- 🎨 **Dynamic Page Decoration**: Visual page decoration system based on Block-Element architecture
- 📱 **Advertisement Management**: Create and manage various types of advertisement blocks
- 🔧 **Rule Engine**: Expression-based rules to control content display logic
- ⏰ **Time Control**: Schedule content display with start and end time support
- 📊 **Visitor Analytics**: Automatic visitor behavior tracking with analytics support
- 🔄 **Event System**: Extension points for custom data formatting
- 💾 **Cache Support**: Built-in caching mechanism for performance optimization
- 🗑️ **Auto Cleanup**: Automatic cleanup of visit logs with configurable retention period

## Basic Usage

### Creating Advertisement Blocks

```php
use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\Element;

// Create a block
$block = new Block();
$block->setCode('homepage_banner')
      ->setTitle('Homepage Banner')
      ->setShowExpression('user.isVip or env.DEBUG')
      ->setStartTime(new \DateTime('2024-01-01'))
      ->setEndTime(new \DateTime('2024-12-31'));

// Add elements
$element = new Element();
$element->setTitle('New Year Sale')
        ->setImageUrl('/images/banner.jpg')
        ->setJumpPath('/promotion/newyear')
        ->setBlock($block);

$entityManager->persist($block);
$entityManager->persist($element);
$entityManager->flush();
```

### Retrieving Block Data

```php
// Using JSON-RPC interface
$response = $this->rpcClient->call('GetDiyPageElementByCode', [
    'codes' => ['homepage_banner', 'sidebar_ad']
]);

// Process returned data
foreach ($response as $code => $elements) {
    foreach ($elements as $element) {
        echo $element['title'];    // Element title
        echo $element['imageUrl']; // Image URL
        echo $element['jumpPath']; // Jump path
    }
}
```

### Expression Rule Examples

```php
// Based on user attributes
$block->setShowExpression('user.level >= 3');

// Based on environment variables
$block->setShowExpression('env.FEATURE_FLAG_ENABLED');

// Composite conditions
$block->setShowExpression('user.isVip and datetime.now >= "2024-01-01"');
```

## Configuration

### Visit Log Retention

```yaml
# config/packages/diy_page.yaml
parameters:
    env(DIY_PAGE_VISIT_LOG_PERSIST_DAY_NUM): 7  # Default: 7 days
```

## Advanced Usage

### Event Listeners

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
        $data['customField'] = 'customValue';
        $event->setData($data);
    }
}
```

### Expression Language Functions

The bundle provides custom expression functions:

- `visitLog(userId, blockCode)`: Get visit log for specific user and block
- Custom functions can be added via `VisitLogFunctionProvider`

## API Endpoints

### GetDiyPageElementByCode

Batch retrieve element data by block codes:

```php
$response = $rpcClient->call('GetDiyPageElementByCode', [
    'codes' => ['banner', 'sidebar'],
    'limit' => 10
]);
```

### GetOneDiyPageElement

Retrieve single element details:

```php
$response = $rpcClient->call('GetOneDiyPageElement', [
    'id' => 123
]);
```

## Admin Interface

The bundle integrates with EasyAdmin providing a complete administrative interface:

1. **Block Management**: Create, edit, and delete advertisement blocks
2. **Element Management**: Manage specific content within blocks
3. **Visit Analytics**: View access logs and statistical data

Access the admin interface at `/admin`.

## Testing

Run the test suite:

```bash
./vendor/bin/phpunit packages/diy-page-bundle/tests
```

## Architecture Overview

```text
DiyPageBundle/
├── Entity/              # Domain models
│   ├── Block.php       # Advertisement block entity
│   ├── BlockAttribute.php # Block attribute entity
│   ├── Element.php     # Content element entity
│   └── VisitLog.php    # Visitor tracking entity
├── Repository/          # Data access layer
│   ├── BlockRepository.php
│   ├── ElementRepository.php
│   └── VisitLogRepository.php
├── Service/            # Business logic
│   └── AdminMenu.php   # Admin menu service
├── Event/              # Event classes
│   ├── BlockDataFormatEvent.php
│   └── ElementDataFormatEvent.php
├── Procedure/          # JSON-RPC procedures
│   ├── GetDiyPageElementByCode.php
│   └── GetOneDiyPageElement.php
├── ExpressionLanguage/ # Custom expression functions
│   └── Function/
│       └── VisitLogFunctionProvider.php
├── Controller/         # Web controllers
└── DataFixtures/       # Test data fixtures
```

## Dependencies

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM 3.0+
- Expression Language component
- JSON-RPC Core bundle
- Various Tourze bundles for enhanced functionality

## Design References

- [App Advertisement Design for Product Managers](http://www.woshipm.com/pd/2028587.html)
- [Business Cloud - Advertisement Management Solution](https://www.shangtaoyun.net/mnewsdetail-1006.html)

## Roadmap

1. Location-based advertisement targeting
2. User level-based content differentiation
3. Time-based advertisement rotation
4. A/B testing support
5. Enhanced analytics features

## Contributing

Issues and Pull Requests are welcome.

## License

MIT License