#!/bin/bash

# This script adds team to database. 1 parameter needed: teamname
# Team Totally Hackers (HvA), 2020

databaselocation="/var/www/database/database.db"

echo -e '\e[95m                   __       ___ ___       ___  __   __   __             __   __ ' 
echo -e '\e[95m|__| \  /  /\     |__)  /\   |   |  |    |__  / _` |__) /  \ |  | |\ | |  \ /__`' 
echo -e '\e[95m|  |  \/  /~~\    |__) /~~\  |   |  |___ |___ \__> |  \ \__/ \__/ | \| |__/ .__/' 
echo -e '\e[95m                                                                                ' 
echo -e "\e[96mBy team Totally Hackers, 2020"
echo ""

teamcode=$1
penalityscore=$2
reason=$3

teamname=($(sqlite3 $databaselocation "SELECT name FROM groups WHERE id='$teamcode';"))

if [[ -z $teamname && -z $teamcode && -z $penalityscore && $reason ]]
then
	echo "Does not exist"
	exit 1
fi

teamscore=($(sqlite3 $databaselocation "SELECT score FROM groups WHERE id='$teamcode';"))
newscore=$(($teamscore-$penalityscore))
sqlite3 $databaselocation "UPDATE groups SET score='$newscore' WHERE id='$teamcode';"
echo "<b>[$(date +'%T')]</b> team $teamname got a penality of <b style='color: red'>$penalityscore</b>. Reason: $reason" >> /var/www/html/notifications.txt
