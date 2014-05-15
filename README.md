# Switchboard

Switchboard is an application for coordinating between local development and
third-party hosts.

## Requirements

* Drush 5.1 or higher - https://github.com/drush-ops/drush
* PHP 5.3.3 or higher
* [Requests](https://github.com/rmccue/Requests) (installed using Composer)

## Installation

Switchboard should be installed and updated using git and Composer.

[Composer](http://getcomposer.org) is a dependency manager for PHP.

The easiest way to install Composer for *nix (including Mac):

    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer

More detailed installation instructions for multiple platforms can be found in
the [Composer Documentation](http://getcomposer.org/doc/00-intro.md).

### Normal installation

    # Download Switchboard.
    git clone https://github.com/fluxsauce/switchboard.git $HOME/.drush/switchboard
    # Download dependencies.
    cd $HOME/.drush/switchboard
    composer update --no-dev
    # Clear Drush's cache.
    drush cc drush

## Documentation

Switchboard uses Drush's internal documentation system. To get a list of all
available Switchboard commands:

    drush --filter=switchboard

For a JSON list of available commands:

    drush help --format=json --filter=switchboard
