# dApp

This is the command line interface for the [Dappur PHP Framework](https://github.com/dappur/framework)

## Pre-Requisites
[Composer](https://getcomposer.org/) - Dependency manager is required in order to use the Dappur PHP Framework.  [Installation Instructions](https://getcomposer.org/doc/00-intro.md)

[Phinx](https://phinx.org/) - Phinx is required in order to utilize the database migrations.  It is recommended that you install Phinx globally via composer by running:

    $ composer global require robmorgan/phinx

## Installation

    $ composer global require dappur/dapp

## Usage
### `new`
This command creates a new Dappur application in the specified folder using the composer `create-project` command.
- **name** - The folder name for your new appliciation.
- **--theme -t (Optional)** - Install a custom frontend theme from a git repo. Default: `git@github.com:dappur/theme-dappur.git`
- **--dashboard -d (Optional)** - Install a custom dashboard theme from a git repo. Default: `git@github.com:dappur/theme-AdminLTE.git`
- **--vagrant (Optional)** - If set, runs `vagrant up` when installation is complete. Default: `false`
```
$ dapp new name (--theme=THEME_REPO --dashboard=THEME_REPO --vagrant)
```

### `theme`
This command allows you to install official and custom themes from git repositories.
- **url (Optional)** - Git repo url for the theme that you wish to install.  If no url is set, you will be presented a list of official themes to install.
- **--download-only (Optional)** - If set, the theme will be copied into the view folder, but the database will not be updated.
```
$ dapp theme (url --download-only)
```

### `controller`
This command generated a new controller class in `app/src/Controller` as well as having the controller automatically added to the container dependencies with an entry in `app/src/bootstrap/controllers.php`
- **name** - The name of your controller class in `PascalCase` format.  This command also supports generating nested class names, i.e. `NewController\SubController`.
```
$ dapp controller name
```

### `app`
This command generated a new App class template in `app/src/App` using the class name that you specify.
- **name** - The name of your class in `PascalCase` format.  This command also supports generating nested class names, i.e. `NewController\SubController`.
```
$ dapp app name
```

### `middleware`
This command generated a new App class template in `app/src/Middleware` using the class name that you specify.
- **name** - The name of your class in `PascalCase` format.  This command also supports generating nested class names, i.e. `MyMiddleware\SubMiddleware`.
```
$ dapp middleware name
```

### `twigex`
This command generated a new App class template in `app/src/TwigExtensions` using the class name that you specify.
- **name** - The name of your class in `PascalCase` format.  This command also supports generating nested class names, i.e. `MainExtension\SubExtension`.
```
$ dapp twigex name
```

### `server`
This command launches an instance of PHP's built-in web server, `php -S` defaulted to port 8181.
- **port (Optional)** - Port to run the web server on.  Default is 8181.
```
$ dapp server (port=PORT)
```