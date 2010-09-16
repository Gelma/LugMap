#!/bin/bash

git pull origin master

for file in $(/bin/ls -1 db/*)
do
	echo $file
done
