# Mars Framework

A PHP web framework designed for rapid application development.

## Features

- Simple routing system
- MVC architecture support
- Easy configuration
- Efficient request handling

## Installation

```bash
composer require webdev1404/mars-framework
```

## Quick Start

Create a basic route:

```php
<?php
require 'vendor/autoload.php';

use Mars\Framework\Router;

$router = new Router();

$router->get('/', function() {
    return 'Hello, Mars!';
});

$router->run();
```

## Documentation

For detailed documentation, visit the [official docs](https://github.com/webdev1404/mars-framework).

## License

This project is licensed under the MIT License.

## Support

For issues and feature requests, please visit the [GitHub repository](https://github.com/webdev1404/mars-framework/issues).
