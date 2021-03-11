#!/bin/bash
# Tigo Bakker, 2020
# Get all users and IP-adresses for openvpn road-warrior configs, and return in JSON-format
output=""
output+="["
output+=$(journalctl -u openvpn-server@server | grep "primary virtual IP for" | awk -F 'for ' '{print $2}' | sed 's/\/.*://' | sort | uniq | sed "s/ /', 'ip_addr': '/g" | while read line; 
do 
	echo -n "{'user': '$line'}," | sed "s/'/\"/g"; 
done)
output=$(echo -n $output | sed -zr 's/,([^,]*$)/\1/')
output+="]"
echo $output > /var/www/users.json