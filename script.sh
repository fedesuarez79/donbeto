#!/bin/bash


# if no output from the remote ssh cmd, bail out
if [ -z "git pull origin master" ]; then
    echo "No results from remote repo listing (via SSH)"
    exit
fi

echo "git responde ok"
git add script*
git commit -m "commiteando script desde donbeto"
git push origin master

