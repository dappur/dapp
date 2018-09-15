# dApp

This is the command line interface for the [Dappur PHP Framework](https://github.com/dappur/framework)

## Usage
---

#### new - Create a New Dappur Application
This command creates a new Dappur application in the specified folder using the composer `create-project` command.
- **Name** - The folder name for your new appliciation.
```
dappur new {Name}
```

#### controller - Create a New Controller
This command generated a new controller in `app/src/controller` as well as having the controller automatically added to the container dependencies with an entry in `app/src/bootstrap/controllers.php`
- **Name** - The name of your controller in `CamelCase` format.  This command supports unlimited nested controllers, i.e. `NewController\SubController`.
```
dappur controller {Name}
```

#### mc - Create a New Migration
This command created a new migration using the Phinx `phinx create` command.
- **Name** - The name of your migration in `CamelCase` format.
```
dappur mc {Name}
```

#### migrate - Run a Database Migration
This command created a new migration using the Phinx `phinx migrate` command.
- **Environment (Optional)** - Target the migration on a specific environment.
- **Target (Optional)** - Target a specific migration.
```
dappur migrate -e {Environment} -t {Target}
```

#### rollback - Rollback a Database Migration
This command created a new migration using the Phinx `phinx rollback` command.
- **Environment (Optional)** - Target the migration on a specific environment.
- **Target (Optional)** - Target a specific migration.
```
dappur rollback -e {Environment} -t {Target}
```

#### breakpoint - Manage Database Breakpoints
This command created a new migration using the Phinx `phinx breakpoint` command.
- **Environment (Optional)** - Target the breakpoint on a specific environment.
- **Target (Optional)** - Target the breakpoint on a specific migration.
- **Remove All (Optional)** - Remove all breakpoints.
```
dappur rollback -e {Environment} -t {Target}
```

#### server - Run the Built-In PHP Web Server
This command launches an instance of PHP's built-in web server, `php -S` defaulted to port 8181.
- **Port (Optional)** - Port to run the web server on.  Default is 8181.
```
dappur controller {Port}
```