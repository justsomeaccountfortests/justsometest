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
# Clear submitted flags
sqlite3 $databaselocation "DELETE FROM submitted;"

machines=($(sqlite3 $databaselocation "SELECT ip_addr FROM machines;"))
passwords=($(sqlite3 $databaselocation "SELECT root_pw FROM machines;"))

flags=()

echo -e "\e[96m[+]\e[0m Generating flags..."
for m in ${machines[@]}
do
	flag=$(openssl rand -hex 8)
	flags+=($flag)
done

# Distributing machines over teams
echo -e "\e[96m[+]\e[0m Distributing flags over machines..."

globalCounter="0"

for i in ${flags[@]}
do
	echo "Updating ${machines[$globalCounter]}..."
	sshpass -p ${passwords[$globalCounter]} ssh -o StrictHostKeyChecking=no root@${machines[$globalCounter]} "echo $i > /home/flag.txt"
	sqlite3 $databaselocation "UPDATE flags SET flag='$i' WHERE ip_address='${machines[$globalCounter]}';"
	globalCounter=$[$globalCounter+1]
done

echo -e "\e[96m[+]\e[0m Sending notification..."
echo "<b>A new round has started!</b> All flags have been renewed" >> /var/www/html/notifications.txt
echo -e "\e[96m[+]\e[0m Done!"
