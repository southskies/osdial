#!/bin/bash
#
## Copyright (C) 2010  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
##
##     This file is part of OSDial.
##
##     OSDial is free software: you can redistribute it and/or modify
##     it under the terms of the GNU Affero General Public License as
##     published by the Free Software Foundation, either version 3 of
##     the License, or (at your option) any later version.
##
##     OSDial is distributed in the hope that it will be useful,
##     but WITHOUT ANY WARRANTY; without even the implied warranty of
##     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
##     GNU Affero General Public License for more details.
##
##     You should have received a copy of the GNU Affero General Public
##     License along with OSDial.  If not, see <http://www.gnu.org/licenses/>.
##


# The following condition allows us to put our functions at the end of the file.
[ "$1" = "--imports" ] && imports=1 || imports=0
if [ $imports -lt 1 ]; then
	#Import out functions from the end of the file.
	source $PWD/$0 --imports


	#####################################################################
	#
	#         GLOBAL VARIABLES AND CONFIGURATION
	#
	#

	# Looback interface.
	LO_IF="lo"
	LO_NET="127.0.0.0/8"
	LO_IP1="127.0.0.1"

	# Public interface.
	WAN_IF="eth0"
	WAN_NET="203.177.115.0/27"
	WAN_IP1="203.177.115.4"

	# Local interface.
	LAN_IF="eth1"
	LAN_NET="10.124.0.0/16"
	LAN_IP1="10.124.1.7"

	# VPN interface(s)
	VPN_IF="tun+"
	VPN_NET="10.0.0.0/8"

	# If ENABLE_GATEWAY is 1, your LAN stations will be able to route traffic out of this server
	# as if it was a router.  If you will not be using this server as a gateway, you should leave
	# it disabled.
	ENABLE_GATEWAY="0"

	# If USE_DSCP is 1, the system will use the newer DSCP method (instead of TOS) for QoS tagging
	# and packet prioritization.  DSCP is much better than TOS and currectly more widely used by
	# the higher end routers that your packets will eventually go through.  Thanks to some clever
	# bit-math, DSCP is backward compatibile with TOS, allowing older routers that only use TOS
	# to still utilize similar prioritization.  If you want to skip DSCP altogether and only use
	# TOS, set USE_DSCP to 0.
	USE_DSCP='1'

	# A bogon is an unallocated range of IP addresses.
	#Since they are not officially allocated, we will not accept any packets from them.
	BOGONS=(  0.0.0.0/8   5.0.0.0/8   10.0.0.0/8     14.0.0.0/8    23.0.0.0/8      31.0.0.0/8     36.0.0.0/7  39.0.0.0/8  42.0.0.0/8
	         49.0.0.0/8 100.0.0.0/6  104.0.0.0/7    106.0.0.0/8   127.0.0.0/8  169.254.0.0/16  172.16.0.0/12 176.0.0.0/7 179.0.0.0/8
                181.0.0.0/8 185.0.0.0/8 192.0.2.0/24 192.168.0.0/16 198.18.0.0/15 198.51.100.0/24 203.0.103.0/24 223.0.0.0/8 )

	# TTL is the maximum hops to allow a packet to travel.
	TTL="128"

	# A SYN attack consists of a large number of half-open packets which flood a host.
	# Here we can place limits on how many we will acknowledge in a given period.
	SYNOPT="--limit 10/second --limit-burst 10"


	# There can be a lot of logging in a firewall.
	# Here we place limits on the frequency that we log events.
	LOGOPT="--limit 30/minute --limit-burst 10"


	# The location of iptables.
	IPT='/sbin/iptables'
	#IPT='echo /sbin/iptables'


	# Do not remove the following line.
	FW_LoadTables


	#####################################################################
	#
	#         DEFINABLE RULESETS
	#
	#

	######### QoS / Prioritizing Packets (DSCP / TOS) ###################
	#FW_QOS "LABEL"      "PROTOCOL" "NETWORK"  "PORTS"       "PRIORITY"
	FW_QOS  "IAX2"       "udp"      "$WAN_NET" "4679"        "CRITICAL"
	FW_QOS  "RTP-media"  "udp"      "$WAN_NET" "10000:20000" "CRITICAL"
	FW_QOS  "SIP"        "udp"      "$WAN_NET" "5060:5070"   "HIGH"
	FW_QOS  "HTTP"       "tcp"      "$WAN_NET" "80"          "MIDHIGH"
	FW_QOS  "HTTPS"      "tcp"      "$WAN_NET" "443"         "MIDHIGH"
	FW_QOS  "FTP-data"   "tcp"      "$WAN_NET" "20"          "MIDHIGH"
	FW_QOS  "FTP"        "tcp"      "$WAN_NET" "21"          "MIDLOW"
	FW_QOS  "SSH"        "tcp"      "$WAN_NET" "22"          "MID"
	FW_QOS  "DNS"        "udp"      "$WAN_NET" "53"          "MID"
	FW_QOS  "OpenVPN"    "udp"      "$WAN_NET" "1194:1195"   "MID"
	FW_QOS  "SMTP"       "tcp"      "$WAN_NET" "25"          "LOW"
	FW_QOS  "POP3"       "tcp"      "$WAN_NET" "110"         "LOW"
	FW_QOS  "IMAP"       "tcp"      "$WAN_NET" "143"         "LOW"
	FW_QOS  "All Others" "all"      "$WAN_NET" ""            "NORMAL"


	######### Trusted (Local) Interfaces ################################
	#FW_TrustInterface "LABEL" "INTERFACE"
	FW_TrustInterface  "LO"    "$LO_IF"
	FW_TrustInterface  "LAN"   "$LAN_IF"
	FW_TrustInterface  "VPN"  "$VPN_IF"


	######### Trusted (Public) IP Addresses, Networks, or Hostnames #####
	#FW_TrustHost "LABEL"           "IP|NETWORK|HOSTNAME"
	FW_TrustHost  "LO"              "$LO_NET"
	FW_TrustHost  "LAN"             "$LAN_NET"
	FW_TrustHost  "VPN"            "$VPN_NET"
	FW_TrustHost  "CCSG 1"          "24.73.199.62"
	FW_TrustHost  "CCSG 2"          "67.78.177.146"
	FW_TrustHost  "AccelaSanSay 1"  "69.94.226.174"
	FW_TrustHost  "AccelaSanSay 2"  "66.194.8.2"
	FW_TrustHost  "Xcast 1"         "38.102.250.50"
	FW_TrustHost  "Xcast 2"         "38.102.250.60"
	FW_TrustHost  "Bandwidth 1"     "216.82.224.202"
	FW_TrustHost  "Bandwidth 2"     "216.82.225.202"
	FW_TrustHost  "Bandwidth 3"     "67.231.8.110"
	FW_TrustHost  "Bandwidth 4"     "4.55.17.2"


	# Do not remove the following line.
	FW_TrustNS


	######### Blocked Hosts - ALL Ports #################################
	#FW_BlockHost  "LABEL"      "IP|NETWORK|HOSTNAME"  "DEST_NET"
	FW_BlockHost   "BadGuy1"    "222.111.222.111"      "$WAN_NET"


	######### Blocked Ports - ALL Hosts #################################
	#FW_BlockPort  "LABEL"  "PORTS"     "PROTCOL"  "DEST_NET"
	FW_BlockPort   "NETBIOS"  "137:139"   "all"      "$WAN_NET"
	FW_BlockPort   "MS-DS"    "445"       "all"      "$WAN_NET"
	FW_BlockPort   "MS-SQL-S" "1433"      "all"      "$WAN_NET"
	FW_BlockPort   "Postgre"  "2049"      "all"      "$WAN_NET"
	FW_BlockPort   "Postgre"  "5432"      "all"      "$WAN_NET"
	FW_BlockPort   "VNC"      "5900:5910" "all"      "$WAN_NET"
	FW_BlockPort   "CVSUP"    "5999"      "all"      "$WAN_NET"
	FW_BlockPort   "X11"      "6063"      "all"      "$WAN_NET"
	FW_BlockPort   "OBIEE"    "9704"      "all"      "$WAN_NET"
	FW_BlockPort   "NetBus"   "20034"     "all"      "$WAN_NET"
	FW_BlockPort   "Bad1"     "27444"     "all"      "$WAN_NET"
	FW_BlockPort   "Bad2"     "27665"     "all"      "$WAN_NET"
	FW_BlockPort   "Bad3"     "31335"     "all"      "$WAN_NET"
	FW_BlockPort   "Bad4"     "31337"     "all"      "$WAN_NET"


	######### Blocked Ports on a Given Host   ###########################
	#FW_BlockHostPort  "LABEL"          "IP|NETWORK|HOSTNAME"  "PORT"  "PROTOCOL"  "DEST_NET"
	FW_BlockHostPort   "BadUser/NoWeb"  "222.111.222.111"      "80"    "tcp"       "$WAN_NET"


	######### Opened Ports - ANY Host ###################################
	# Open In/Out to specific ports.
	#FW_OpenPort "LABEL"      "PORTS"        "PROTCOL"  "DEST_NET"
	FW_OpenPort  "RTP media"  "10000:20000"  "udp"      "$WAN_NET"


	######### Opened Ports to a Given Host    ###########################
	#FW_OpenHostPort  "LABEL"          "IP|NETWORK|HOSTNAME"  "PORT"  "PROTOCOL"  "DEST_NET"
	FW_OpenHostPort   "HomeSSH"        "67.1.2.43"            "22"    "tcp"       "$WAN_NET"




	#####################################################################
	#
	#         KERNEL / ADVANCED SYSCTL SETTINGS
	#
	#

	# Kernel - General Tweaks
	#   Make sure forwarding is on.
	[ -e /proc/sys/net/ipv4/ip_forward ] && echo 1 > /proc/sys/net/ipv4/ip_forward || :
	#   Doubling current limit for ip_conntrack
	[ -e /proc/sys/net/ipv4/ip_conntrack_max ] && echo 16384 > /proc/sys/net/ipv4/ip_conntrack_max || :
	#   Log 'odd' IP addresses (excludes 0.0.0.0 & 255.255.255.255)
	[ -e /proc/sys/net/ipv4/conf/all/log_martians ] && echo 0 > /proc/sys/net/ipv4/conf/all/log_martians || :
	#   Keep packet fragments in memory for 8 seconds
	[ -e /proc/sys/net/ipv4/ipfrag_time ] && echo 15 > /proc/sys/net/ipv4/ipfrag_time || :
	# Turn off dynamic TCP/IP address hacking, if DSL/Cable has issue, set to '1'.
	[ -e /proc/sys/net/ipv4/ip_dynaddr ] && echo 0 > /proc/sys/net/ipv4/ip_dynaddr || :

	# Kernel - TCP Tweaks
	#   Turn off TCP Timestamping in kernel
	[ -e /proc/sys/net/ipv4/tcp_timestamps ] && echo 0 > /proc/sys/net/ipv4/tcp_timestamps || :
	#   Set TCP Re-Ordering value in kernel to '5'
	[ -e /proc/sys/net/ipv4/tcp_reordering ] && echo 5 > /proc/sys/net/ipv4/tcp_reordering || :
	#   Turn off TCP ACK in kernel
	[ -e /proc/sys/net/ipv4/tcp_sack ] && echo 0 > /proc/sys/net/ipv4/tcp_sack || :
	#   Turn off TCP Window Scaling in kernel
	[ -e /proc/sys/net/ipv4/tcp_window_scaling ] && echo 0 > /proc/sys/net/ipv4/tcp_window_scaling || :
	#   Set Keepalive timeout to 30 seconds
	[ -e /proc/sys/net/ipv4/tcp_keepalive_time ] && echo 1800 > /proc/sys/net/ipv4/tcp_keepalive_time || :
	#   Set FIN timeout to 30 seconds
	[ -e /proc/sys/net/ipv4/tcp_fin_timeout ] && echo 10 > /proc/sys/net/ipv4/tcp_fin_timeout || :
	#   Set TCP retry count to 3
	[ -e /proc/sys/net/ipv4/tcp_retries1 ] && echo 3 > /proc/sys/net/ipv4/tcp_retries1 || :
	#   Turn off ECN notification in kernel
	[ -e /proc/sys/net/ipv4/tcp_ecn ] && echo 0 > /proc/sys/net/ipv4/tcp_ecn || :

	# Kernel - SYN Tweaks
	#   Turn on SYN cookies protection in kernel
	[ -e /proc/sys/net/ipv4/tcp_syncookies ] && echo 1 > /proc/sys/net/ipv4/tcp_syncookies || :
	#   Set SYN ACK retry attempts to '3'
	[ -e /proc/sys/net/ipv4/tcp_synack_retries ] && echo 2 > /proc/sys/net/ipv4/tcp_synack_retries || :
	#   Set SYN backlog buffer to '64'
	[ -e /proc/sys/net/ipv4/tcp_max_syn_backlog ] && echo 2048 > /proc/sys/net/ipv4/tcp_max_syn_backlog || :
	#   Set SYN retry attempts to '6'
	[ -e /proc/sys/net/ipv4/tcp_syn_retries ] && echo 6 > /proc/sys/net/ipv4/tcp_syn_retries || :

	# Kernel - Routing Tweaks
	#   Turn on source address verification in kernel
	[ -e /proc/sys/net/ipv4/conf/all/rp_filter ] && for f in /proc/sys/net/ipv4/conf/*/rp_filter; do echo 1 > $f; done || :
	#   Turn off source routes in kernel
	[ -e /proc/sys/net/ipv4/conf/all/accept_source_route ] && for f in /proc/sys/net/ipv4/conf/*/accept_source_route; do echo 0 > $f; done || :

	# Kernel - ICMP/IGMP Tweaks
	#   ICMP Dead Error Messages protection
	[ -e /proc/sys/net/ipv4/icmp_ignore_bogus_error_responses ] && echo 1 > /proc/sys/net/ipv4/icmp_ignore_bogus_error_responses || :
	#   ICMP Broadcasting protection
	[ -e /proc/sys/net/ipv4/icmp_echo_ignore_broadcasts ] && echo 1 > /proc/sys/net/ipv4/icmp_echo_ignore_broadcasts || :
	#   IGMP Membership 'overflow' protection, set to >5 if box is a router.
	[ -e /proc/sys/net/ipv4/igmp_max_memberships ] && echo 1 > /proc/sys/net/ipv4/igmp_max_memberships || :



	#
	#
	#         END OF SETTINGS - DO NOT EDIT BELOW
	#
	#####################################################################
	#FW_FinishChains
	exit 0;
fi










# USER FUNCTIONS:

#  FW_TrustInterface  "LABEL"  "INTERFACE"
#    LABEL:     A name describing the interface.
#    INTERFACE: The interface device name.
#
#    Example:
#      FW_TrustInterface  "LAN"  "$LAN_IF"
#
FW_TrustInterface() {
	label=$1; int=$2
	$IPT -A trust_in  -i $int -j ACCEPT -m comment --comment "Trusted Interface: $label"
	$IPT -A trust_out -o $int -j ACCEPT -m comment --comment "Trusted Interface: $label"
}



#  FW_TrustHost  "LABEL"  "HOST"
#    LABEL: A name describing the interface.
#    HOST:  The IP-address of the host.
#
#    Example:
#      FW_TrustHost  "CCSG"  "67.78.177.146"
#
FW_TrustHost() {
	label=$1; host=$2
	$IPT -A trust_in -s $host -j ACCEPT -m comment --comment "Trusted Host: $label"
	$IPT -A trust_out -d $host -j ACCEPT -m comment --comment "Trusted Host: $label"
}



#  FW_BlockHost   "LABEL"  "HOST"  "DEST_HOST"
#    LABEL:     A name describing the host.
#    HOST:      The IP-address of the host.
#    DEST_HOST: Usually the public subnet or public IP, ie "$WAN_NET" or "$WAN_IP1"
#
#    Example:
#      FW_BlockHost   "BadGuy"  "222.111.222.111"  "$WAN_NET"
#
FW_BlockHost() {
	label=$1; host=$2; dest_host=$3
	[ -z "$dest_host" ] && dest_host=$WAN_NET || :
	$IPT -A block_in -s $host -d $dest_host -j DROP -m comment --comment "Blocked host: $label"
	$IPT -A block_out -d $host -s $dest_host -j DROP -m comment --comment "Blocked host: $label"
}




#  FW_BlockPort  "LABEL"  "PORT"  "PROTOCOL"  "DEST_HOST"
#    LABEL:     A name describing the port.
#    PORT:      The port to block, single or ranged, ie "80" or "100:200".
#    PROTOCOL:  all|tcp|udp, defaults to "all"
#    DEST_HOST: Usually the public subnet or public IP, ie "$WAN_NET" or "$WAN_IP1"
#
#    Example:
#      FW_BlockPort   "BlockSMTP"  "25"  "tcp"  "$WAN_NET"
#
FW_BlockPort() {
	label=$1; port=$2; protocol=$3; dest_host=$4
	FW_BlockHostPort "$label" "0/0" "$port" "$protocol" "$dest_host"
}


#  FW_BlockHostPort  "LABEL"  "HOST"  "PORT"  "PROTOCOL"  "DEST_HOST"
#    LABEL:     A name describing the host/port.
#    HOST:      The IP-address of the host.
#    PORT:      The port to block, single or ranged, ie "80" or "100:200".
#    PROTOCOL:  all|tcp|udp, defaults to "all"
#    DEST_HOST: Usually the public subnet or public IP, ie "$WAN_NET" or "$WAN_IP1"
#
#    Example:
#      FW_BlockHostPort  "Bad User, Blocked Web"  "222.111.222.111"  "80"  "tcp"  "$WAN_NET"
#
FW_BlockHostPort() {
	label=$1; host=$2; port=$3; protocol=$4; dest_host=$5
	[ -z "$host" ] && host="0/0" || :
	[ -z "$dest_host" ] && dest_host=$WAN_NET || :
	[ -z "$protocol" ] && protocol="all" || :
	[ "$host" = "0/0" ] && btype="Port" || btype="Host/Port"
	fn_comment="Blocked $btype: $label"
	if [ "$protocol" = "all" ]; then
		$IPT -A block_in  -p tcp -s $host -d $dest_host --dport $port -j DROP -m comment --comment "$fn_comment"
		$IPT -A block_in  -p udp -s $host -d $dest_host --dport $port -j DROP -m comment --comment "$fn_comment"
		$IPT -A block_out -p tcp -d $host -s $dest_host --sport $port -j DROP -m comment --comment "$fn_comment"
		$IPT -A block_out -p udp -d $host -s $dest_host --sport $port -j DROP -m comment --comment "$fn_comment"
	else
		$IPT -A block_in  -p $protocol -s $host -d $dest_host --dport $port -j DROP -m comment --comment "$fn_comment"
		$IPT -A block_out -p $protocol -d $host -s $dest_host --sport $port -j DROP -m comment --comment "$fn_comment"
	fi
}




#  FW_PortForward  "LABEL"  "EXT_HOST"  "EXT_PORT"  "INT_HOST"  "INT_PORT"  "PROTOCOL"
#    LABEL:     A name describing the port-forward.
#    EXT_HOST:  The external IP-address/Network to be forward.
#    EXT_PORT:  The external port to forward, single only, ie "80".
#    INT_HOST:  The internal IP-address to foward to.
#    INT_PORT:  The internal port where the forward will arrive, single ONLY, ie "80".
#    PROTOCOL:  all|tcp|udp, defaults to "all"
#
#    Example:
#      FW_PortForward  "ForwardWeb"  "$WAN_IP1"  "80"  "192.168.0.10"  "80"  "tcp"
#
FW_PortForward() {
        label=$1; ext_host=$2; ext_port=$3; int_host=$4; int_port=$5; protocol=$6
	[ -z "$protocol" ] && protocol="all" || :
	fn_comment="($protocol) $ext_host.$ext_port -> $int_host.$int_port  : Port Forward: $label"
        $IPT -A FORWARD -p $protocol -d $int_host --dport $int_port -j ACCEPT -m comment --comment "Input: Accept: $fn_comment"
        $IPT -t nat -A PREROUTING -p $protocol -d $ext_host --dport $ext_port -j DNAT --to $int_host:$int_port \
		-m comment --comment "Prerouting: DNAT: $fn_comment"
}



#  FW_OpenPort  "LABEL"  "PORT"  "PROTOCOL"  "DEST_HOST"
#    LABEL:     A name describing the port.
#    PORT:      The port(s) to open, single or ranged, ie "80" or "100:200".
#    PROTOCOL:  all|tcp|udp, defaults to "all"
#    DEST_HOST: Usually the public subnet or public IP, ie "$WAN_NET" or "$WAN_IP1"
#
#    Example:
#      FW_OpenPort  "AllowIAX2 from ALL"  "4569"  "udp"  "$WAN_NET"
#
FW_OpenPort() {
	label=$1; port=$2; protocol=$3; dest_host=$4
	FW_OpenHostPort "$label" "0/0" "$port" "$protocol" "$dest_host"
}


#  FW_OpenHostPort  "LABEL"  "HOST"  "PORT"  "PROTOCOL"  "DEST_HOST"
#    LABEL:     A name describing the host/port.
#    HOST:      The IP-address (or hostname) of the host.
#    PORT:      The port(s) to open, single or ranged, ie "80" or "100:200".
#    PROTOCOL:  all|tcp|udp, defaults to "all"
#    DEST_HOST: Usually the public subnet or public IP, ie "$WAN_NET" or "$WAN_IP1"
#
#    Example:
#      FW_OpenHostPort  "OpenWebForAnAgent"  "67.100.100.234"  "80"  "tcp"  "$WAN_NET"
#
FW_OpenHostPort() {
	label=$1; host=$2; port=$3; protocol=$4; dest_host=$5
	[ -z "$host" ] && host="0/0" || :
	[ -z "$dest_host" ] && dest_host=$WAN_NET || :
	[ -z "$protocol" ] && protocol="all" || :
	[ "$host" = "0/0" ] && btype="Port" || btype="Host/Port"
	fn_comment="Open $btype: $label"
	if [ "$protocol" = "all" ]; then
		$IPT -A open_in  -p tcp -s $host -d $dest_host --dport $port -j ACCEPT -m comment --comment "$fn_comment"
		$IPT -A open_in  -p udp -s $host -d $dest_host --dport $port -j ACCEPT -m comment --comment "$fn_comment"
		$IPT -A open_out -p tcp -d $host -s $dest_host --sport $port -j ACCEPT -m comment --comment "$fn_comment"
		$IPT -A open_out -p udp -d $host -s $dest_host --sport $port -j ACCEPT -m comment --comment "$fn_comment"
	else
		$IPT -A open_in  -p $protocol -s $host -d $dest_host --dport $port -j ACCEPT -m comment --comment "$fn_comment"
		$IPT -A open_out -p $protocol -d $host -s $dest_host --sport $port -j ACCEPT -m comment --comment "$fn_comment"
	fi
}


FW_FinishChains() {
	$IPT -A trust_in  -j RETURN
	$IPT -A trust_out -j RETURN
	$IPT -A block_in  -j RETURN
	$IPT -A block_out -j RETURN
	$IPT -A open_in   -j RETURN
	$IPT -A open_out  -j RETURN
}


#  FW_QOS "LABEL" "PROTOCOL" "EXT_HOST" "PORT(s)" "QOS"
#    LABEL:    A name describing the port.
#    PROTOCOL: all|tcp|udp
#    EXT_HOST: Usually the public subnet, ie "$WAN_NET"
#    PORT:     The port the QOS is being applied too, single or ranged, ie "80" or "10000:20000"
#    QOS:      The variable hold the QOS priority level, CRITICAL HIGH MIDHIGH MID MIDLOW LOW NORMAL
#
#    Example:
#      FW_QOS  "RTP-media"  "udp"  "$WAN_NET"  "10000:20000"  "CRITICAL"
#
FW_QOS() {
	label=$1; protocol=$2; host=$3; port=$4; qosname=$5
	[ -z "$host" ] && host=$WAN_NET || :
	[ -z "$qosname" ] && qosname='NORMAL' || :

	# Set bit defaults for dscp / tos.
	if [ "$USE_DSCP" = "1" ]; then
		QOSTYPE='DSCP'; QOSOPT='--set-dscp-class'
		CRITICAL='EF';   HIGH='AF41'; MIDHIGH='CS4';  MID='CS3';  MIDLOW='CS2';  LOW='CS1';  NORMAL='BE'
	else
		QOSTYPE='TOS';  QOSOPT='--set-tos'
		CRITICAL='0x10'; HIGH='0x08'; MIDHIGH='0x08'; MID='0x04'; MIDLOW='0x04'; LOW='0x02'; NORMAL='0x00'
	fi

	# Convert name to class/bit QOS type.
	qos=$NORMAL
	[ "$qosname" = "CRITICAL" ] && qos=$CRITICAL || :
	[ "$qosname" = "HIGH" ] &&     qos=$HIGH || :
	[ "$qosname" = "MIDHIGH" ] &&  qos=$MIDHIGH || :
	[ "$qosname" = "MID" ] &&      qos=$MID || :
	[ "$qosname" = "MIDLOW" ] &&   qos=$MIDLOW || :
	[ "$qosname" = "LOW" ] &&      qos=$LOW || :
	[ "$qosname" = "NORMAL" ] &&   qos=$NORMAL || :

	opt_src=""
	opt_dst=""
	if [ -n "$port" ]; then
		opt_src="--sport $port"
		opt_dst="--dport $port"
	fi
	fn_comment_src="$label src $port $QOSTYPE to $qosname $qos"
	fn_comment_dst="$label drc $port $QOSTYPE to $qosname $qos"
	$IPT -t mangle -A POSTROUTING -p $protocol $opt_src -j $QOSTYPE $QOSOPT $qos -m comment --comment "Set: $fn_comment_src"
	$IPT -t mangle -A POSTROUTING -p $protocol $opt_dst -j $QOSTYPE $QOSOPT $qos -m comment --comment "Set: $fn_comment_dst"
	$IPT -t mangle -A POSTROUTING -p $protocol $opt_src -j RETURN -m comment --comment "Return: $fn_comment_src"
	$IPT -t mangle -A POSTROUTING -p $protocol $opt_dst -j RETURN -m comment --comment "Return: $fn_comment_dst"
	$IPT -t mangle -A PREROUTING -p $protocol $opt_src -j $QOSTYPE $QOSOPT $qos -m comment --comment "Set: $fn_comment_src"
	$IPT -t mangle -A PREROUTING -p $protocol $opt_dst -j $QOSTYPE $QOSOPT $qos -m comment --comment "Set: $fn_comment_dst"
	$IPT -t mangle -A PREROUTING -p $protocol $opt_src -j RETURN -m comment --comment "Return: $fn_comment_src"
	$IPT -t mangle -A PREROUTING -p $protocol $opt_dst -j RETURN -m comment --comment "Return: $fn_comment_dst"
}


FW_TrustNS() {
	# Auto-Detect Nameservers
	while read s1 s2 s3; do
		if [ "$s1" = "nameserver" ]; then
			$IPT -A dns_in  -p tcp ! --syn -s $s2 -j ACCEPT -m comment --comment "Auto-Detected Nameserver"
			$IPT -A dns_out -p tcp ! --syn -d $s2 -j ACCEPT -m comment --comment "Auto-Detected Nameserver"
			$IPT -A dns_in  -p udp -s $s2 -j ACCEPT -m comment --comment "Auto-Detected Nameserver"
			$IPT -A dns_out -p udp -d $s2 -j ACCEPT -m comment --comment "Auto-Detected Nameserver"
			$IPT -A dns_in  -p udp -s $s2 -j ACCEPT -m comment --comment "Auto-Detected Nameserver"
			$IPT -A dns_out -p udp -d $s2 -j ACCEPT -m comment --comment "Auto-Detected Nameserver"
		fi
	done < /etc/resolv.conf
}




# FUNCTIONS
FW_LoadTables() {

	# Flush and delete 'filter' table.
	$IPT -t filter -F > /dev/null 2>&1
	$IPT -t filter -X > /dev/null 2>&1
	$IPT -t filter -Z > /dev/null 2>&1

	# Flush and delete 'mangle' table.
	$IPT -t mangle -F > /dev/null 2>&1
	$IPT -t mangle -X > /dev/null 2>&1
	$IPT -t mangle -Z > /dev/null 2>&1

	# Flush and delete 'nat' table.
	$IPT -t nat -F > /dev/null 2>&1
	$IPT -t nat -X > /dev/null 2>&1
	$IPT -t nat -Z > /dev/null 2>&1

	#Default Policy
	$IPT -P INPUT DROP > /dev/null 2>&1
	$IPT -P FORWARD DROP > /dev/null 2>&1
	$IPT -P OUTPUT ACCEPT > /dev/null 2>&1

	# Create our required chains.
	for i in firewall_in firewall_out tcp_checks syn_flood fragments bad_flags icmp_checks bogon_in bogon_out trust_in trust_out block_in block_out open_in open_out dns_in dns_out; do
		$IPT -N $i
	done


	$IPT -A tcp_checks -p tcp --sport 20 --dport 1023:65535 ! --syn -m state --state RELATED -j ACCEPT
	$IPT -A tcp_checks -p tcp --sport 22 --dport 513:65535 ! --syn -m state --state RELATED -j ACCEPT
	$IPT -A tcp_checks -p tcp -j bad_flags
	$IPT -A tcp_checks -p tcp --syn -j syn_flood
	$IPT -A tcp_checks -p tcp -f -j fragments

	# Limit SYN packets to values in global $SYNOPT
	$IPT -A syn_flood -p tcp --syn -m limit $SYNOPT -j RETURN
	$IPT -A syn_flood -j LOG --log-prefix "SYNFLOODDROP:" -m limit $LOGOPT
	$IPT -A syn_flood -j DROP

	# Drop Excessive Fragmentation
	$IPT -A fragments -p tcp -m limit --limit 5/minute -j RETURN
	$IPT -A fragments -j LOG --log-prefix "FRAGDROP:" -m limit $LOGOPT
	$IPT -A fragments -j DROP

	# Chain to detect and drop illegal TCP packets.
	$IPT -A bad_flags -p tcp --tcp-flags ACK,FIN FIN -j DROP             -m comment --comment "Bad tcp-flags: ---|ack|FIN|---|---|---"
	$IPT -A bad_flags -p tcp --tcp-flags ACK,PSH PSH -j DROP             -m comment --comment "Bad tcp-flags: ---|ack|---|---|---|PSH"
	$IPT -A bad_flags -p tcp --tcp-flags ACK,URG URG  -j DROP            -m comment --comment "Bad tcp-flags: ---|ack|---|---|URG|---"
	$IPT -A bad_flags -p tcp --tcp-flags FIN,RST FIN,RST -j DROP         -m comment --comment "Bad tcp-flags: ---|---|FIN|RST|---|---"
	$IPT -A bad_flags -p tcp --tcp-flags SYN,FIN SYN,FIN  -j DROP        -m comment --comment "Bad tcp-flags: SYN|---|FIN|---|---|---"
	$IPT -A bad_flags -p tcp --tcp-flags SYN,RST SYN,RST -j DROP         -m comment --comment "Bad tcp-flags: SYN|---|---|RST|---|---"
	$IPT -A bad_flags -p tcp --tcp-flags ALL ALL -j DROP                 -m comment --comment "Bad tcp-flags: SYN|ACK|FIN|RST|URG|PSH"
	$IPT -A bad_flags -p tcp --tcp-flags ALL NONE -j DROP                -m comment --comment "Bad tcp-flags: syn|ack|fin|rst|urg|psh"
	$IPT -A bad_flags -p tcp --tcp-flags ALL FIN,URG,PSH -j DROP         -m comment --comment "Bad tcp-flags: syn|ack|FIN|rst|URG|PSH"
	$IPT -A bad_flags -p tcp --tcp-flags ALL SYN,ACK,FIN,RST,URG -j DROP -m comment --comment "Bad tcp-flags: SYN|ACK|FIN|RST|URG|psh"

	# Chain to limit or deny ICMP responses.
	$IPT -A icmp_checks -p icmp -d $WAN_NET -s $WAN_NET -j ACCEPT
	$IPT -A icmp_checks -p icmp --icmp-type  0 -j ACCEPT -m comment --comment "Echo-Reply (0)"
	$IPT -A icmp_checks -p icmp --icmp-type  3 -j ACCEPT -m comment --comment "Destination-Unreachable (3)"
	$IPT -A icmp_checks -p icmp --icmp-type 11 -j ACCEPT -m comment --comment "Time-Exceeded (11)"
	$IPT -A icmp_checks -p icmp --icmp-type  8 -m limit --limit 5/s -j ACCEPT -m comment --comment "Echo (8), Limit to 5/second"
	$IPT -A icmp_checks -j DROP

	## Chains for BOGONS
	for i in ${BOGONS[@]}; do
		$IPT -A bogon_in -s $i -j DROP
		$IPT -A bogon_out -d $i -j DROP
		$IPT -t nat -A PREROUTING -i $WAN_IF -s $i -j DROP
		$IPT -t nat -A POSTROUTING -o $WAN_IF -d $i -j DROP
	done
	$IPT -t nat -A PREROUTING -i $WAN_IF -m addrtype --src-type MULTICAST -j DROP
	$IPT -t nat -A PREROUTING -i $WAN_IF -m pkttype --pkt-type broadcast -j DROP
	$IPT -t nat -A PREROUTING -i $WAN_IF -s $LAN_NET -j DROP
	$IPT -t nat -A POSTROUTING -o $WAN_IF -m addrtype --dst-type MULTICAST -j DROP
	$IPT -t nat -A POSTROUTING -o $WAN_IF -d $LAN_NET -j DROP


	# INPUT chain rules.
	$IPT -A INPUT -i ! $WAN_IF -j trust_in
	$IPT -A INPUT -i $WAN_IF   -j firewall_in

	#$IPT -A firewall_in -j trust_in
	$IPT -A firewall_in -i $WAN_IF -m state --state RELATED -j ACCEPT
	$IPT -A firewall_in -i $WAN_IF -m state --state ESTABLISHED -j ACCEPT
	$IPT -A firewall_in -i $WAN_IF -m state --state INVALID -j DROP
	$IPT -A firewall_in -i $WAN_IF -p icmp -j icmp_checks
	$IPT -A firewall_in -i $WAN_IF -p tcp -j tcp_checks
	$IPT -A firewall_in -i $WAN_IF -j dns_in
	$IPT -A firewall_in -i $WAN_IF -j bogon_in
	$IPT -A firewall_in -i $WAN_IF -j block_in
	$IPT -A firewall_in -i $WAN_IF -j open_in



	# OUTPUT chain rules.
	$IPT -A OUTPUT -o ! $WAN_IF -j trust_out
	$IPT -A OUTPUT -o $WAN_IF   -j firewall_out

	$IPT -A firewall_out -o $WAN_IF -m state --state RELATED -j ACCEPT
	$IPT -A firewall_out -o $WAN_IF -m state --state ESTABLISHED -j ACCEPT
	$IPT -A firewall_out -o $WAN_IF -m state --state NEW -j ACCEPT
	$IPT -A firewall_out -o $WAN_IF -m state --state INVALID -j DROP
	$IPT -A firewall_out -o $WAN_IF -p icmp -j icmp_checks
	$IPT -A firewall_out -o $WAN_IF -p tcp -j tcp_checks
	$IPT -A firewall_out -o $WAN_IF -j dns_out
	$IPT -A firewall_out -o $WAN_IF -j bogon_out
	$IPT -A firewall_out -o $WAN_IF -j block_out
	$IPT -A firewall_out -o $WAN_IF -j open_out



	# FORWARD / POSTROUTING / SNAT
	if [ "$ENABLE_GATEWAY" = "1" ]; then
		# Setup forwards before masquerading.
		$IPT -A FORWARD -d 0/0 -s $LAN_NET -o $WAN_IF -j ACCEPT -m comment --comment "Forward: Accept: (all) $LAN_NET -> 0/0 VIA [$WAN_IF]  : Forward before Masquerade"
		$IPT -A FORWARD -d 0/0 -s $VPN_NET -o $WAN_IF -j ACCEPT -m comment --comment "Forward: Accept: (all) $VPN_NET -> 0/0 VIA [$WAN_IF]  : Forward before Masquerade"
		$IPT -A FORWARD -d 0/0 -s $LAN_NET -o $VPN_IF -j ACCEPT -m comment --comment "Forward: Accept: (all) $LAN_NET -> 0/0 VIA [$VPN_IF]  : Forward before Masquerade"
		$IPT -A FORWARD -d 0/0 -s $VPN_NET -o $LAN_IF -j ACCEPT -m comment --comment "Forward: Accept: (all) $VPN_NET -> 0/0 VIA [$LAN_IF]  : Forward before Masquerade"
		$IPT -A FORWARD -d $LAN_NET -j ACCEPT -m comment --comment "Forward: Accept: (all) 0/0 -> $LAN_NET  : Forward before Masquerade"
		$IPT -A FORWARD -d $VPN_NET -j ACCEPT -m comment --comment "Forward: Accept: (all) 0/0 -> $VPN_NET  : Forward before Masquerade"

		# Masquerade Outgoing Traffic
		$IPT -t nat -A POSTROUTING -o $WAN_IF -j MASQUERADE -m comment --comment "Postrouting: Masquerade: (all) 0/0 -> 0/0 VIA [$WAN_IF]  : Masquerade Outgoing Traffic"
		# Allow traffic from WAN_NET to go outbound
		$IPT -t nat -A POSTROUTING -s $WAN_NET -d 0/0 -j ACCEPT -m comment --comment "Postrouting: Accept: (all) $WAN_NET -> 0/0  : Allow traffic from WAN_NET to go outbound"
	fi
}
