databaselocation="/var/www/database/database.db"

echo -e '\e[95m                   __       ___ ___       ___  __   __   __             __   __ '
echo -e '\e[95m|__| \  /  /\     |__)  /\   |   |  |    |__  / _` |__) /  \ |  | |\ | |  \ /__`'
echo -e '\e[95m|  |  \/  /~~\    |__) /~~\  |   |  |___ |___ \__> |  \ \__/ \__/ | \| |__/ .__/'
echo -e '\e[95m                                                                                '
echo -e "\e[96mBy team Totally Hackers, 2020"
echo ""

machines=($(sqlite3 $databaselocation "SELECT ip_addr FROM machines;"))
passwords=($(sqlite3 $databaselocation "SELECT root_pw FROM machines;"))

globalCounter="0"

for i in ${machines[@]}
do
        # Flag check
        echo "Checking http on ${machines[$globalCounter]}..."
        echo ===RESPONSE===
	curl -i ${machines[$globalCounter]}
	echo
	echo ==============
	globalCounter=$[$globalCounter+1]
done
