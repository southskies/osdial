#!/usr/bin/perl

# This script is to be run on the web-server, sending out multicast
# beacons containing the IP address of its associated multicast
# interface.  Which you allow a workstation recieving those requests
# to correctly identify where to point the browser.

use strict;
use IO::Interface::Simple;
use IO::Socket::Multicast;

$|++;
my $dest = '226.1.1.2:2001'; 
my $IPs;
my $socks;

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


($IPs, $socks) = initsocks();

while (1) {
	my $error = 0;
	foreach my $int (keys %{$socks}) {
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
			push @kvary, $k . '=' . $kvh{$k};
		}
		my $mess = join('&',@kvary);
		$socks->{$int}->send($mess) || $error++;
	}
	($IPs, $socks) = initsocks() if ($error);
}

sub initsocks {
	my %IPs;
	my @ints = IO::Interface::Simple->interfaces;
	foreach my $int (@ints) {
		if ($int =~ /^e/) {
			my $ip = IO::Interface::Simple->new($int);
			$IPs{$int} = $ip->address if ($ip->address);
		}
	}

	my %socks;
	foreach my $int (keys %IPs) {
		$socks{$int} = IO::Socket::Multicast->new(Proto=>'udp',PeerAddr=>$dest);
		$socks{$int}->mcast_if($int);
	}

	print STDERR "osdial_resource_send: (Re)Init called...SUCCESS.\n";
	return (\%IPs, \%socks);
}
