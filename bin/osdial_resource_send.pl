#!/usr/bin/perl

# This script is to be run on the web-server, sending out multicast
# beacons containing the IP address of its associated multicast
# interface.  Which you allow a workstation recieving those requests
# to correctly identify where to point the browser.

use strict;
use OSDial;
use Getopt::Long;
use IO::Interface::Simple;
use IO::Socket::Multicast;
$|++;

my $use_multicast=0;
my $mcast_dest='226.1.1.2:2001'; 

my $DB=0;

GetOptions('multicast!' => \$use_multicast, 'debug!' => \$DB);

my $IPs;
my $interfaces;

# Get hostname / domain / label
my $hostname = `hostname -s`;
chomp($hostname);
my $domain = `hostname -d`;
chomp($domain);
my $label = $ARGV[0];
$label = $hostname if ($label eq "");

# Get CPU info;
my $cpucnt = 0;
open(CPU,"/proc/cpuinfo");
while (my $inp = <CPU>) {
	$cpucnt++ if ($inp =~ /^processor/);
}
close(CPU);


($IPs, $interfaces) = initinterfaces();

while (1) {
	my $error = 0;
	foreach my $int (keys %{$interfaces}) {
		# Get loadavg info;
		my $load1 = 0;
		my $load5 = 0;
		my $load10 = 0;
		my $loadprocs = '';
		my $loadlastpid = '';
		open(CPU,"/proc/loadavg");
		while (my $inp = <CPU>) {
			chomp($inp);
			($load1,$load5,$load10,$loadprocs,$loadlastpid) = split(/ /,$inp);
		}
		close(CPU);

		#Get memory info
		my %meminfo;
		open(MEM,"/proc/meminfo");
		while (my $inp = <MEM>) {
			chomp($inp);
			$inp =~ s/ kB$//;
			my($k,$v) = split(/:\s*/,$inp);
			$meminfo{$k} = $v;
		}
		close(MEM);
		my $mempct = sprintf('%3.2f%%',((($meminfo{MemTotal} - ($meminfo{MemFree} + $meminfo{Cached})) / $meminfo{MemTotal}) * 100));
		my $swpused = ($meminfo{SwapTotal} - ($meminfo{SwapFree} + $meminfo{SwapCached}));

		my $cpupct = `sar -u 1 5 | grep Average | awk '{ print 100 - \$8 }'`;
		chomp($cpupct);
		my $cpupct = sprintf('%3.2f%%',$cpupct);

		my $timestamp = `date +\%Y\%m\%d\%H\%M\%S`;
		chomp $timestamp;
		my $timestampSQL = `date "+\%Y-\%m-\%d \%H:\%M:\%S"`;
		chomp $timestampSQL;

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
		}
	}
	if ($use_multicast) {
		($IPs, $interfaces) = initinterfaces() if ($error);
	} else {
		sleep(5);
	}
}

sub initinterfaces {
	my %IPs;
	my @ints = IO::Interface::Simple->interfaces;
	foreach my $int (@ints) {
		if ($int =~ /^e|^b/) {
			my $ip = IO::Interface::Simple->new($int);
			$IPs{$int} = $ip->address if ($ip->address);
		}
	}

	my %interfaces;
	my $cnt=0;
	foreach my $int (keys %IPs) {
		if ($use_multicast) {
			$interfaces{$int} = IO::Socket::Multicast->new(Proto=>'udp',PeerAddr=>$mcast_dest);
			$interfaces{$int}->mcast_if($int);
		} else {
			if ($cnt==0) {
				$interfaces{$int} = OSDial->new('DB'=>$DB);
				my $sret = $interfaces{$int}->sql_query("SELECT count(*) AS fndsvr FROM server_stats WHERE server_ip='" . $IPs{$int} . "';");
				if ($sret->{fndsvr} == 0) {
					$interfaces{$int}->sql_execute("INSERT INTO server_stats SET server_ip='" . $IPs{$int} . "';");
				}
			}
			$cnt++;
		}
	}

	print STDERR "osdial_resource_send: (Re)Init called...SUCCESS.\n" if ($use_multicast);
	return (\%IPs, \%interfaces);
}
