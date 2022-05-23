#!/bin/sh

db='mysql'
app='php'

dbstatus=$(docker ps -q -f status=running -f name=^/${db}$)
appstatus=$(docker ps -q -f status=running -f name=^/${app}$)

if [ ! "${dbstatus}" ];
then
    echo "Container ${db} doesn't exist"
elif [ ! "${appstatus}" ];
then
    echo "Container ${app} doesn't exist"
else
    make reset
fi
# unset dbstatus
# unset appstatus