#!/bin/bash

date &&

(git checkout hotfix || git checkout -b hotfix) &&
(git add * || true) &&
(git commit -am "HOTFIXES TO $(date)" || true ) &&
git checkout master && 
git pull

