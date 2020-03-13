#!/bin/bash

date &&

git stash &&
(git checkout hotfix || git checkout -b hotfix) &&
git stash pop &&
(git add * || true) &&
(git commit -am "HOTFIXES TO $(date)" || true ) &&
git checkout master && 
git pull

