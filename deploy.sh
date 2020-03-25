#!/bin/bash

# Expects one argument, the branch to deploy: master, dev

date &&

git stash save "HOTFIXES TO $(date)" &&
git checkout $1 && 
git pull &&
composer install &&
./poorman migrate

