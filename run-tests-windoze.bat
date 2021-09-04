#!/usr/bin/env bash

REM SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
REM SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
REM
REM SPDX-License-Identifier: CC0-1.0

npm run wp-env clean
npm run wp-env run phpunit "phpunit -c /var/www/html/wp-content/plugins/giglogadmin/phpunit.xml"
