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

sqlite3 $databaselocation "DELETE FROM submitted;"

machines=($(sqlite3 $databaselocation "SELECT ip_addr FROM machines;"))
passwords=($(sqlite3 $databaselocation "SELECT root_pw FROM machines;"))

globalCounter="0"

for i in ${machines[@]}
do
	# Flag check
	echo "Checking flag on ${machines[$globalCounter]}..."
	flag=($(sqlite3 $databaselocation "SELECT flag FROM flags WHERE ip_address='$i';"))
	echo "expected flag: $flag"
	received_flag=($(sshpass -p ${passwords[$globalCounter]} ssh -o StrictHostKeyChecking=no root@${machines[$globalCounter]} "cat /home/flag.txt"))
	echo "received flag: $received_flag"
	if [ "$flag" = "$received_flag" ]
	then
    		echo "[$(date +'%T')] OK"
		echo "Checking flag permissions..."
		permissions="644"
		echo "expected permissions: $permissions"
		received_permissions=($(sshpass -p ${passwords[$globalCounter]} ssh -o StrictHostKeyChecking=no root@${machines[$globalCounter]} "stat -c '%a' /home/flag.txt"))
		echo "received permissions: $received_permissions"
		if [ "$permissions" = "$received_permissions" ]
        	then
			echo "[$(date +'%T')] OK"
		else
			echo "Strings are not equal."
                	teamcode=($(sqlite3 $databaselocation "SELECT group_id FROM machines WHERE ip_addr='$i';"))
                	teamscore=($(sqlite3 $databaselocation "SELECT score FROM groups WHERE id='$teamcode';"))
                	newscore=$(($teamscore-10))
                	echo sqlite3 $databaselocation "UPDATE groups SET score='$newscore' WHERE id='$teamcode';"
                	#echo "<b>[$(date +'%T')]</b> Server <b style='color: red'>${machines[$globalCounter]}</b> is broken! The flags permissions are incorrect (expected to be $permissions). <b style='color: red'>10</b> points were retracted from the responsible team." >> /var/www/html/notifications.txt
		fi
	else
		echo "Strings are not equal."
		teamcode=($(sqlite3 $databaselocation "SELECT group_id FROM machines WHERE ip_addr='$i';"))
		teamscore=($(sqlite3 $databaselocation "SELECT score FROM groups WHERE id='$teamcode';"))
		newscore=$(($teamscore-10))
		echo sqlite3 $databaselocation "UPDATE groups SET score='$newscore' WHERE id='$teamcode';"
		#echo "<b>[$(date +'%T')]</b> Server <b style='color: red'>${machines[$globalCounter]}</b> is broken! The flag is incorrect. <b style='color: red'>10</b> points were retracted from the responsible team." >> /var/www/html/notifications.txt
	fi
	globalCounter=$[$globalCounter+1]
done
