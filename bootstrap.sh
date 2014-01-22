#!/bin/bash

composer install --prefer-source
npm install -g forever
cd /vagrant/core/frontRender/ && npm install

