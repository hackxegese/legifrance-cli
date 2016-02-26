#!/bin/bash

set -e

code=${1}
repo=${2-output}

if [[ ! -e $repo ]]
then
    git init $repo
    git commit --allow-empty -m 'Initial commit'
fi

cd $repo
rm -rf ???*
cd - >/dev/null

date=$(date '+%Y%m%d')
./bin/legifrance dump --code=$code --date=$date $repo

cd $repo
git add -A >/dev/null
git commit -q -m "Version du $(date '+%d/%m/%Y')"
git push
cd - >/dev/null
