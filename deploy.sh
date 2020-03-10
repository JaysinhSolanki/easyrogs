#!/bin/bash

date >> ~/easyrogs-deploy.log

((git checkout hotfix || git checkout -b hotfix) &&
(git add * || true) &&
git commit -am "HOTFIXES TO $(date)" &&
git checkout master && 
git pull) >> ~/easyrogs-deploy.log

