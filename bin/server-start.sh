#!/bin/bash

loc=`dirname $BASH_SOURCE`;

source $loc/.local-beetl;
php -S localhost:8080 $loc/../web/index.php
