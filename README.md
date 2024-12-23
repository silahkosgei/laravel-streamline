# iankibet/streamline

## Overview

A laravel package that makes it possible to bind service/streamline class to frontend vue component

## Installation

```sh
composer require iankibet/laravel-streamline
```

## Config

Streamline uses a config file to determine the namespace of the service/streamline classes. To publish the config file, run the following command:

```sh
php artisan vendor:publish --tag=laravel-streamline
```

Here is how the config file looks like:
    
```php
return [
    'class_namespace' => 'App\\Streams',
    'class_postfix' => 'Stream',
    'route' => 'api/streamline',
    'middleware' => ['auth:sanctum'],
    'guest_streams' => [
        'auth/auth'
    ]
];
```

Modify the values to suit your application.

### ```class_namespace```

This is the namespace where the stream classes are located. The default value is `App\Streams`.

### ```class_postfix```

This is the postfix that is added to the stream class to easily identify streamline . The default value is `Stream`. For example, if the Stream name is `User`, the stream class will be `UsersStream`.

## Implementation
To use, first import the Stream and extend it in yur class as show below:

```php
use iankibet\Streamline\Stream;

class TasksStreamline extends Stream
{

}
```

### Validation
To validate, use Validate attribute as shown below:

```php
use iankibet\Streamline\Component;
use iankibet\Streamline\Validate;

// in the method

#[Validate([
        'name' => 'required|string',
        'description' => 'required|string'
    ])]
    public function addTask()
    {
        // code here
        $data = $this->only(['name', 'description']);
    }
}
```

### Authorization

To authorize, use Permission attribute as shown below:

```php
use iankibet\Streamline\Component;
use iankibet\Streamline\Permission;

// in the method

#[Permission('create-task')]

    public function addTask()
    {
        // code here
        $data = $this->only(['name', 'description']);
    }
}
```

### Testing the component

To test the component, use the following command: Replace `TasksStreamline` with the name of your component.

```sh
php artisan streamline:test TasksStreamline
```
