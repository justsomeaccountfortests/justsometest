#!/snap/bin/pwsh

# later toegevoegd door Tigo voor de projectgroep:
# Om dit script te laten werken moet de vmware powerCLI-tools zijn geinstalleerd in powershell. Dat kan gewoon in de powershell versie voor linux :)

/usr/bin/echo -e '\e[95m                   __       ___ ___       ___  __   __   __             __   __ ' 
/usr/bin/echo -e '\e[95m|__| \  /  /\     |__)  /\   |   |  |    |__  / _` |__) /  \ |  | |\ | |  \ /__`' 
/usr/bin/echo -e '\e[95m|  |  \/  /~~\    |__) /~~\  |   |  |___ |___ \__> |  \ \__/ \__/ | \| |__/ .__/' 
/usr/bin/echo -e '\e[95m                                                                                ' 
/usr/bin/echo -e "\e[96mBy team Totally Hackers, 2020"
/usr/bin/echo ""


if (-not $args[0])
{
	/usr/bin/echo -e "\e[31m[-]\e[0m You have to enter an IP-address"
	exit
} 

Set-PowerCLIConfiguration -InvalidCertificateAction Ignore -Confirm:$false

Connect-VIServer -Server 192.168.178.2 -User root -Password #!!WACHTWOORD!!#
$inputIP = ($args[0] | Select-String -Pattern "\d{1,3}(\.\d{1,3}){3}" -AllMatches).Matches.Value

echo $inputIP

$vm = Get-VM | Where-Object -FilterScript { $_.Guest.Nics.IPAddress -contains $inputIP }

Set-VM -VM $vm -SnapShot 'bare' -Confirm:$false
