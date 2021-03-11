#!/bin/bash

# This script automatically kicks off battlegrounds. no parameters needed, only love
# Team Totally Hackers (HvA), 2020

databaselocation="/var/www/database/database.db"

echo -e '\e[95m                   __       ___ ___       ___  __   __   __             __   __ ' 
echo -e '\e[95m|__| \  /  /\     |__)  /\   |   |  |    |__  / _` |__) /  \ |  | |\ | |  \ /__`' 
echo -e '\e[95m|  |  \/  /~~\    |__) /~~\  |   |  |___ |___ \__> |  \ \__/ \__/ | \| |__/ .__/' 
echo -e '\e[95m                                                                                ' 
echo -e "\e[96mBy team Totally Hackers, 2020"
echo ""
# Clear previous teams
sqlite3 $databaselocation "UPDATE machines SET group_id='';"

machines=($(sqlite3 $databaselocation "SELECT ip_addr FROM machines;"))
teams=($(sqlite3 $databaselocation "SELECT id FROM groups;"))

# Randomize machines
echo -e "\e[96m[+]\e[0m Shuffling machines..."
machines=($(shuf -e ${machines[@]}))

machinesCount=${#machines[@]}
teamCount=${#teams[@]}

# Distributing machines over teams
echo -e "\e[96m[+]\e[0m Distributing machines over teams..."
machinesPerUser=$((machinesCount / teamCount))

globalCounter="0"

sqlQuery=""
for i in ${teams[@]}
do
	counter="0"
	while [ $counter -lt $machinesPerUser ]
	do
		sqlite3 $databaselocation "UPDATE machines SET group_id='$i' WHERE ip_addr='${machines[$globalCounter]}';"
		counter=$[$counter+1]
		globalCounter=$[$globalCounter+1]
	done
done

echo -e "\e[96m[+]\e[0m Setting gamestatus to active..."
sqlite3 $databaselocation "UPDATE status SET enabled='yes' WHERE gamestate='game';"

echo -e "\e[96m[+]\e[0m Done!"
