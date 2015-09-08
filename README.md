Drupal Sauron Monitoring
=======================
Sauron monitors your Drupal project and keep you in touch with your application update status.

Sauron monitoring is a complete rewrite of existing python app https://github.com/misterdoak/sauron

/!\ NOT READY FOR PRODUCTION YET /!\

INSTALL
--------

Use composer

    composer require "drupal-sauron/monitoring":"dev-master"

HOW TO
-------

Get project update status?

    ./sauron project:update-status <project>

Get project update status by email?

    ./sauron project:update-status <project> --report=mail

Checkout project before update status by email?

    ./sauron project:update-status <project> --checkout --report=mail
