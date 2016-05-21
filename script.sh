#!/bin/bash

#si no responde git salir
comando=$(git pull origin master)
if [ -z "$comando" ]; then
	if [ -z "$comando" ]; then
    		echo "git pull no responde"
    		exit
	fi
fi
echo "git pull ok"

git add script*
git commit -m "commiteando script desde donbeto"
comando2=$(git push origin master)
if [ -z "$comando2" ]; then
        if [ -z "$comando2" ]; then
                echo "git push no responde"
                exit
        fi
fi
echo "git push ok"


