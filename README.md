# Switchboard

Switchboard is a application for coordinating between local environments and
third-party hosts. Switchboard gets information about remote sites, sets up
local sites, and can be used to synchronize content between the local and
remote.

Switchboard uses Drush for validation and input. All commands can respond with
Drush format (default), or with JSON output using option ````--json````.

Switchboard currently supports operations on both Acquia and Pantheon sites.
Switchboard is not intended as a replacement for either Acquia Drush commands or
Pantheon's Terminus; those tools are designed for performing remote site
operations.

Switchboard is being actively developed to support
[Kalabox](http://www.kalamuna.com/products/kalabox/), an integrated workflow
solution for Drupal developers.

Development of Switchboard is being generously sponsored by
[Kalamuna](http://www.kalamuna.com).

Travis CI status: [<img src="https://travis-ci.org/fluxsauce/switchboard.svg?branch=master">](https://travis-ci.org/fluxsauce/switchboard)

## Requirements

* [Drush](https://github.com/drush-ops/drush) 5.1 or higher
* PHP 5.3.3 or higher
* [Composer](http://getcomposer.org) - a PHP dependency manager.
    * [Requests](https://github.com/rmccue/Requests) (installed using Composer)
    * [Propel](https://github.com/propelorm/Propel) (installed using Composer)

## Installation

Switchboard should be only be installed and updated using git and Composer.

The easiest way to install Composer for *nix (including Mac):

````
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
````

More detailed installation instructions for multiple platforms can be found in
the [Composer Documentation](http://getcomposer.org/doc/00-intro.md).

Once Composer is installed...

````
# Download Switchboard.
git clone https://github.com/fluxsauce/switchboard.git $HOME/.drush/switchboard
# Download dependencies.
cd $HOME/.drush/switchboard
composer update --no-dev
# Set up database.
vendor/propel/propel1/generator/bin/propel-gen
vendor/propel/propel1/generator/bin/propel-gen insert-sql
# Clear Drush's cache.
drush cc drush
````

### Updates

````
cd $HOME/.drush/switchboard
git pull
composer update --no-dev
vendor/propel/propel1/generator/bin/propel-gen
vendor/propel/propel1/generator/bin/propel-gen insert-sql
drush cc drush
````

## Documentation

Switchboard uses Drush's internal documentation system. To get a list of all
available Switchboard commands:

````
drush --filter=switchboard
````

For a JSON list of available commands:

````
drush help --format=json --filter=switchboard
````

## Demo

````
# Login to Pantheon, a Provider.
drush sw-auth-login pantheon user@example.com password
# List available sites from a Provider.
drush sw-site-list pantheon
# Get information about a remote site.
drush sw-site-info pantheon nameofsite
# List remote site environments.
drush sw-site-env-list pantheon nameofsite
# Download the latest backup from the dev environment to current directory.
drush sw-site-env-backup-dl pantheon nameofsite dev db .
# Create a local project; site_id is shown in sw-site-info.
drush sw-project-create nameofproject \
  --site_id=1 \
  --code_path=/srv/site/code \
  --files_path=/srv/site/files \
  --database_host=127.0.0.1 \
  --database_port=3306 \
  --database_username=dbuser \
  --database_password=dbpass \
  --database_name=dbname \
  --hostname=site.tld \
  --username=user
# List available projects.
drush sw-project-list
# Get information about a project.
drush sw-project-info nameofproject
# Update a field in a project.
drush sw-project-update nameofproject --ssh_port=22
# See the new information.
drush sw-project-info nameofproject
# Check out project locally.
drush sw-project-vcs-clone nameofproject
# Import database to project.
drush sw-project-db-import nameofproject ./path/to/db.tar.gz
# Get files from remote dev site via rsync.
drush sw-project-files-rsync nameofproject dev down
````

## API

API documentation is generated with [ApiGen](https://github.com/apigen/apigen)
and can be viewed at http://fluxsauce.github.io/switchboard/

To generate the documentation, use the following:

````
export SWITCHBOARD_ROOT=~/Projects/switchboard
export SWITCHBOARD_DOCS=~/Projects/switchboard-docs
export APIGEN_ROOT=/Applications/MAMP/bin/php/php5.3.14/lib/php/data/ApiGen
apigen --title "Switchboard" \
  --source $SWITCHBOARD_ROOT \
  --destination $SWITCHBOARD_DOCS \
  --exclude *vendor* \
  --php no \
  --template-config $APIGEN_ROOT/templates/bootstrap/config.neon
````
