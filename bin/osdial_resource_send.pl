#!/usr/bin/perl

# This script is to be run on the web-server, sending out multicast
# beacons containing the IP address of its associated multicast
# interface.  Which you allow a workstation recieving those requests
# to correctly identify where to point the browser.
package main;

use strict;
use OSDial;
use Getopt::Long;
use IO::Interface::Simple;
use IO::Socket::Multicast;
use Data::Validate::IP qw(is_private_ipv4 is_public_ipv4);
use Net::Address::IP::Local;
use LWP::UserAgent;
use Digest::MD5 qw(md5_hex);
use Proc::Exists qw(pexists);
$|++;

my $bgpid;

sub handlesig {
	my ($sig) = @_;
	if ($bgpid>0 and pexists($bgpid)) {
		kill('QUIT',$bgpid);
		waitpid($bgpid, 0);
	}	
	exit 0;
}

$SIG{'TERM'}  = \&handlesig;
$SIG{'HUP'}  = \&handlesig;
$SIG{'INT'}  = \&handlesig;
$SIG{'QUIT'}  = \&handlesig;

my $use_multicast=0;
my $mcast_dest='226.1.1.2:2001'; 

my $DB=0;

GetOptions('multicast!' => \$use_multicast, 'debug!' => \$DB);

my $IPs;
my $interfaces;

my $bgservice={};

# Get hostname / domain / label
my $hostname = `hostname -s`;
chomp($hostname);
my $domain = `hostname -d`;
chomp($domain);
my $label = $ARGV[0];

# Get CPU info;
my $cpucnt = 0;
open(CPU,"/proc/cpuinfo");
while (my $inp = <CPU>) {
	$cpucnt++ if ($inp =~ /^processor/);
}
close(CPU);

$cpucnt = $cpucnt + 2;

($IPs, $interfaces) = initinterfaces();

my $loopcnt=0;
while (1) {
	my $error = 0;
	foreach my $int (keys %{$interfaces}) {
		# Get loadavg info;
		print STDERR $int . ': Get loadavg.'."\n" if ($DB);
		my $loadstr = `sar -q 1 1 | grep Average | awk '{ print (\$2)"/"(\$3)" "(\$4)" "(\$5)" "(\$6) }'`;
		chomp($loadstr);
		my($loadprocs,$load1,$load5,$load10) = split(/ /, $loadstr);

		#Get memory info
		print STDERR $int . ': Get meminfo.'."\n" if ($DB);
		my %meminfo;
		my @mem = split(/\n|\s+/,`free | grep -E 'Mem|Swap'`);
		$meminfo{MemTotal} = $mem[1];
		$meminfo{MemFree} = $mem[3];
		$meminfo{Cached} = $mem[6];
		$meminfo{SwapTotal} = $mem[8];
		$meminfo{SwapFree} = $mem[10];
		$meminfo{SwapCached} = 0;
		my $mempct = sprintf('%3.2f%%',((($meminfo{MemTotal} - ($meminfo{MemFree} + $meminfo{Cached})) / $meminfo{MemTotal}) * 100));
		my $swpused = ($meminfo{SwapTotal} - ($meminfo{SwapFree} + $meminfo{SwapCached}));

		print STDERR $int . ': Get cpuinfo.'."\n" if ($DB);
		my $cpupcts = `sar -u 1 2 | grep Average | awk '{ print (\$3)" "(\$5)" "(\$8)" "(100 - \$8) }'`;
		chomp($cpupcts);
		my($ucpupct,$scpupct,$icpupct,$cpupct) = split(/ /, $cpupcts);
		my $ucpupct = $ucpupct + 0;
		my $scpupct = $scpupct + 0;
		my $icpupct = $icpupct + 0;
		my $cpupct = $cpupct + 0;
		my $ucpupct = sprintf('%3.2f%%',$ucpupct);
		my $scpupct = sprintf('%3.2f%%',$scpupct);
		my $icpupct = sprintf('%3.2f%%',$icpupct);
		my $cpupct = sprintf('%3.2f%%',$cpupct);

		my $timestamp = `date +\%Y\%m\%d\%H\%M\%S`;
		chomp $timestamp;

		my $sret = $interfaces->{$int}->sql_query("SELECT server_ip,server_description,server_profile,sys_perf_log,server_domainname FROM servers WHERE server_ip='" . $IPs->{$int} . "';");
		if (defined($sret->{server_ip}) and $sret->{server_ip} ne '') {
			$label = $sret->{server_description} if ($label eq "");
			if ($sret->{server_domainname} eq '') {
				print STDERR $int . ': Updated server record with domainname: '.$domain."\n" if ($DB);
				my $sql = sprintf("UPDATE servers SET server_domainname='%s' WHERE server_ip='%s';",$interfaces->{$int}->mres($domain), $interfaces->{$int}->mres($IPs->{$int}));
				$interfaces->{$int}->sql_execute($sql);
			}
		}
		$label = $hostname if ($label eq "");

		my %kvh;
		$kvh{ip} = $IPs->{$int};
		$kvh{timestamp} = $timestamp;
		$kvh{host} = $hostname;
		$kvh{domain} = $domain;
		$kvh{label} = $label;
		$kvh{load_one} = $load1;
		$kvh{load_five} = $load5;
		$kvh{load_ten} = $load10;
		$kvh{load_procs} = $loadprocs;
		$kvh{cpu_count} = $cpucnt;
		$kvh{cpu_pct} = $cpupct;
		$kvh{mem_total} = $meminfo{MemTotal};
		$kvh{mem_free} = ($meminfo{MemFree} + $meminfo{Cached});
		$kvh{mem_pct} = $mempct;
		$kvh{swap_used} = $swpused;
		my($t,$procs) = split(/\//, $loadprocs);
		my @kvary;
		foreach my $k (sort keys %kvh) {
			if ($use_multicast) {
				push @kvary, $k . '=' . $kvh{$k};
			} else {
				my $ke = $k;
				$ke = 'server_' . $k if ($k eq 'ip' or $k eq 'timestamp');
				push @kvary, $ke . "='" . $kvh{$k} . "'";
			}
		}
		if ($use_multicast) {
			my $mess = join('&',@kvary);
			$interfaces->{$int}->send($mess) || $error++;
		} else {
			my $sql = 'UPDATE server_stats SET ' . join(',',@kvary) . " WHERE server_ip='" . $IPs->{$int} . "';";
			$interfaces->{$int}->sql_execute($sql);
			# AIO and DIALER servers run AST_update, which already updates the server performance table.
			if ($sret->{sys_perf_log} eq 'Y' and $sret->{server_profile} ne 'AIO' and $sret->{server_profile} ne 'DIALER') {
				$sql = sprintf("INSERT INTO server_performance SET start_time=NOW(),server_ip='%s',sysload='%s',freeram='%s',usedram='%s',processes='%s',cpu_user_percent='%s',cpu_system_percent='%s',cpu_idle_percent='%s' ON DUPLICATE KEY UPDATE start_time=addtime(start_time,1);",$IPs->{$int},int($kvh{load_one}),int($kvh{mem_free}/1024),int(($kvh{mem_total}-$kvh{mem_free})/1024),$procs,int($ucpupct),int($scpupct),int($icpupct));
				$interfaces->{$int}->sql_execute($sql);
			}
		}
	}
	if ($use_multicast) {
		($IPs, $interfaces) = initinterfaces() if ($error);
	} else {
		print STDERR "Pid $bgpid still open.\n" if ($bgpid>0 and pexists($bgpid));
		sleep(5);
		($IPs, $interfaces) = initinterfaces() if ($loopcnt%60==0);
	}
	$loopcnt++;
}

sub initinterfaces {
	print STDERR 'Starting initinterfaces function.'."\n" if ($DB);
	my %IPs;
	my %NETMASKs;
	print STDERR 'Building list of system interfaces.'."\n" if ($DB);
	my @ints = IO::Interface::Simple->interfaces;
	foreach my $int (@ints) {
		#if ($int =~ /^e|^b/) {
		if ($int !~ /^lo|^tun|^vbox|^vir|^vif|^bnx|^tmp|^bond/) {
			my $ip = IO::Interface::Simple->new($int);
			$IPs{$int} = $ip->address if ($ip->address);
			$NETMASKs{$int} = $ip->netmask if ($ip->netmask);
		}
	}

	my %interfaces;
	my $sql_int='';
	my $private_int='';
	my $public_int='';
	my $first_int='';
	my $tmposdial;
	$tmposdial = OSDial->new('DB'=>$DB) unless ($use_multicast);
	foreach my $int (sort keys %IPs) {
		if ($use_multicast) {
			$interfaces{$int} = IO::Socket::Multicast->new(Proto=>'udp',PeerAddr=>$mcast_dest);
			$interfaces{$int}->mcast_if($int);
		} else {
			$sql_int = $int if ($sql_int eq '' and Net::Address::IP::Local->connected_to($tmposdial->{VARDB_server}) eq $IPs{$int});
			$private_int = $int if ($private_int eq '' and is_private_ipv4($IPs{$int}));
			$public_int = $int if ($public_int eq '' and is_public_ipv4($IPs{$int}));
			$first_int = $int if ($first_int eq '');
		}
	}
	unless ($use_multicast) {
		my $use_int = $first_int;
		$use_int = $private_int if ($private_int ne '');
		$use_int = $sql_int if ($sql_int ne '');
		$interfaces{$use_int} = $tmposdial->clone();
		my $sret = $interfaces{$use_int}->sql_query("SELECT SQL_NO_CACHE count(*) AS fndsvr FROM server_stats WHERE server_ip='" . $IPs{$use_int} . "';");
		$interfaces{$use_int}->sql_execute("INSERT INTO server_stats SET server_ip='" . $IPs{$use_int} . "';") if ($sret->{fndsvr} == 0);
		$interfaces{$use_int}->sql_execute("DELETE FROM server_stats WHERE server_ip='" . $IPs{$first_int} . "';") if ($use_int eq $private_int and $first_int ne '' and $first_int ne $private_int);
		$interfaces{$use_int}->sql_execute("DELETE FROM server_stats WHERE server_ip='" . $IPs{$private_int} . "';") if ($use_int eq $sql_int and $private_int ne '' and $private_int ne $sql_int);
		if ($public_int ne '') {
			print STDERR 'Found a public_interface, assigning it to the server record (if its blank).'."\n" if ($DB);
			my $sql = sprintf("UPDATE servers SET server_public_ip='%s' WHERE server_ip='%s' AND server_public_ip='';",$interfaces{$use_int}->mres($IPs{$public_int}), $interfaces{$use_int}->mres($IPs{$use_int}));
			$interfaces{$use_int}->sql_execute($sql);
		}
		print STDERR 'Check if server as a public IP yet.'."\n" if ($DB);
		my $sret2 = $interfaces{$use_int}->sql_query(sprintf("SELECT count(*) AS pubcnt FROM servers WHERE server_ip='%s' AND server_public_ip!='';",$IPs{$use_int}));
		if ($sret2->{'pubcnt'}==0) {
			my $intmd5 = md5_hex($IPs{$use_int});
			my $pthost=$IPs{$use_int};
			my $ptport='13425';
			print STDERR 'Started HTTP Daemon for remote IP communication verification.'."\n" if ($DB);
			my $wsvr = OSDialPublicIPTest->new($ptport);
			$wsvr->host($pthost);
			$bgpid = $wsvr->background({setsid=>1});
			sleep 1;

			print STDERR 'Registering md5 key with HTTP Daemon.'."\n" if ($DB);
			my $lwpua = LWP::UserAgent->new(agent=>'OSDialAddrClient/1.0');
			my $lwpres = $lwpua->request(HTTP::Request->new(GET=>'http://'.$pthost.':'.$ptport.'/regkey?req='.$intmd5));

			print STDERR 'Contacting remote server at osdial.org and giving them the port to connect back to this HTTP Daemon.'."\n" if ($DB);
			my $lwpua2 = LWP::UserAgent->new(agent=>'OSDialAddrClient/1.0');
			my $lwpres2 = $lwpua2->request(HTTP::Request->new(GET=>'http://osdial.org/getaddr.php?port='.$ptport));
			if ($lwpres2->is_success) {
				print STDERR 'Content: '.$lwpres2->decoded_content."\n" if ($DB);
				my ($retip,$retmd5key) = split(/\|/,$lwpres2->decoded_content);
				if (is_public_ipv4($retip)) {
					print STDERR 'Remote IP is usable and outbound connections originate from it: '.$retip."\n" if ($DB);
					if ($intmd5 eq $retmd5key) {
						print STDERR 'Key Exchange has been validated, NAT is allowing us to take inbound connections: '.$retmd5key."\n" if ($DB);
						print STDERR 'Marking NATed Public IP as the server IP.'."\n" if ($DB);
						my $sql = sprintf("UPDATE servers SET server_public_ip='%s' WHERE server_ip='%s' AND server_public_ip='';",$retip, $interfaces{$use_int}->mres($IPs{$use_int}));
						$interfaces{$use_int}->sql_execute($sql);
					}
				}
				if (!is_public_ipv4($retip) or $intmd5 ne $retmd5key) {
					print STDERR 'IP or Key Exchange Failed, disabling remote IP lookup for this server: '.$retip.'|'.$retmd5key."\n" if ($DB);
					my $sql = sprintf("UPDATE servers SET server_public_ip='.' WHERE server_ip='%s' AND server_public_ip='';",$interfaces{$use_int}->mres($IPs{$use_int}));
					$interfaces{$use_int}->sql_execute($sql);
				}
			}
			print STDERR 'Signalled QUIT to HTTP Daemon.'."\n" if ($DB);
			kill('QUIT',$bgpid);
			waitpid($bgpid, 0);
		}
	}

	print STDERR "osdial_resource_send: (Re)Init called...SUCCESS.\n" if ($use_multicast);
	return (\%IPs, \%interfaces);
}


{
	package OSDialPublicIPTest;

	use HTTP::Server::Simple::CGI;
	use base qw(HTTP::Server::Simple::CGI);
	use Data::Dumper;

	sub new {
		my($proto, $port) = @_;
		my $class = ref($proto) || $proto;
		my $self = {port=>$port};
		$self->{dispatch} = {'/regkey' => \&resp_regkey, '/getkey' => \&resp_getkey};
		$self->{md5key} = '';
		bless( $self, $class );
		return $self;
	}

	sub handle_request {
		my ($self, $cgi) = @_;
		my $path = $cgi->path_info();
		my $handler = %{$self->{dispatch}}->{$path};
		if (ref($handler) eq "CODE") {
			print "HTTP/1.0 200 OK\r\n";
			$handler->($self,$cgi);
		} else {
			print "HTTP/1.0 404 Not found\r\n";
			print $cgi->header, $cgi->start_html('Not found'), $cgi->h1('Not found'), $cgi->end_html;
		}
	}

	sub resp_getkey {
		my ($self, $cgi) = @_;
		return if !ref $cgi;
		print STDERR 'getkey'."\n" if ($DB);
		print $cgi->header, $self->{'md5key'};
	}

	sub resp_regkey {
		my ($self, $cgi) = @_;
		return if !ref $cgi;
		my $req = $cgi->param('req');
		print STDERR 'regkey: '.$req."\n" if ($DB);
		$self->{'md5key'} = $req;
		print $cgi->header, $cgi->start_html("OK"), $cgi->h1("OK"), $cgi->end_html;
	}

	sub print_banner {
		my ($self) = @_;
	}

}



### PHP script for verifying a two-way IP connection.
### Should be placed on remote server, /getaddr.php.
#<?php
#$remip = $_SERVER["REMOTE_ADDR"];
#if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
#	$remip = $_SERVER['HTTP_CLIENT_IP'];
#} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
#	$remip = $_SERVER['HTTP_X_FORWARDED_FOR'];
#}
#if (!isset($_GET['port'])) {
#	echo $remip;
#} else {
#	$remport = $_GET['port'];
#	$remurl='http://'.$remip.':'.$remport.'/getkey';
#	$ch = curl_init($remurl);
#	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
#	$curl_content = curl_exec($ch);
#	curl_close($ch);
#	echo $remip.'|'.$curl_content;
#}
#?>


1;
