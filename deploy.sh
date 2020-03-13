#!/bin/bash

# Expects one argument, the branch to deploy: master, dev

date &&

git stash &&
(git checkout hotfix || git checkout -b hotfix) &&
git stash pop &&
(git add * || true) &&
(git commit -am "HOTFIXES TO $(date)" || true ) &&
git checkout $1 && 
git pull

