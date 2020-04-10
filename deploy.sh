#!/bin/bash

# Expects one argument, the branch to deploy: master, dev

date &&

git stash save "HOTFIXES TO $(date)" &&
git checkout $1 && 
git fetch --all &&
git reset --hard &&
composer install &&
./poorman migrate

