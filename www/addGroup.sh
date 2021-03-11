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
if [ -z "$1" ]
then
      echo -e "\e[31m[-]\e[0m No parameter was given. Provide a teamname."
	  exit 1
fi
teamName=$1
teamToken=$(openssl rand -hex 3)
echo -e "\e[96m[+]\e[0m The following team will be inserted: $teamName"
# Clear previous teams
echo -e "\e[96m[+]\e[0m Inserting team..."
sqlite3 $databaselocation "INSERT INTO groups (name, id, score) VALUES ('$teamName', '$teamToken', '0');"

echo -e "\e[96m[+]\e[0m Done!"
echo -e "\e[96m[+]\e[0m Your team token is $teamToken"
