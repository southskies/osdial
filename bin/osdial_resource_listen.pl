#!/usr/bin/perl

# This script listens for a multicast packet containing the IP
# address of the web-server.  It then adds it to the hosts file
# and exits.

use strict;
use IO::Socket::Multicast;
use Number::Format;

$|++;

my $DB = 0;
$DB = 1 if ($ARGV[0] eq '-v');

my $nf = new Number::Format;

# Get OSD configuration directives.
my $config = getOSDconfig('/etc/osdial.conf');
my $webroot = $config->{PATHweb};
$webroot = "/opt/osdial/html";
my $sreg = $config->{WEBserver_stats_regex};
my $shost = $config->{WEBserver_stats_remote_scp_host};
my $spath = $config->{WEBserver_stats_remote_scp_path};

my $sock;
my %hosts;

while (1) {
	my $count=0;
	while ($sock = initsocks($sock)) {
		my $data;
		next unless $sock->recv($data,1024);
		if ($data) {
			my %kvh;
			my @kva = split(/&/,$data);
			foreach my $kv (@kva) {
				my($k,$v) = split(/=/,$kv);
				$kvh{$k} = $v;
			}
			
			if ($kvh{ip} =~ /$sreg/ or $kvh{host} =~ /$sreg/) {
				$hosts{$kvh{ip}} = \%kvh;
				open(HTML, ">" . $webroot . "/admin/resources.txt");
				print HTML output_html(%hosts);
				close(HTML);
				open(HTML, ">" . $webroot . "/admin/resources-xtd.txt");
				print HTML output_html_extended(%hosts);
				close(HTML);
				$count++;
			}
		}
		if ($count>9) {
			$count=0;
			# Send to remote server every 10 grabs.
			if ($shost ne "" and $spath ne "") {
				my $cmd = "/usr/bin/scp " . $webroot . "/admin/resources*.txt " . $shost . ":" . $spath . '> /dev/null 2>&1';
				my $send = `$cmd`;
			}
		}
	}
	sleep 5;
}

sub output_html {
	my %hosts = @_;
	my $html;
	$html = "<br><table bgcolor=grey cellspacing=1 cellpadding=3>\n";
	$html .= "  <tr bgcolor=\$menubarcolor>\n";
	$html .= "   ";
	$html .= "<td align=center><font color=white size=1><b>System</b></font></td>";
	$html .= "<td align=center><font color=white size=1><b>CPU%</b></font></td>";
	$html .= "<td align=center><font color=white size=1><b>MEM%</b></font></td>";
	$html .= "\n";
	$html .= "  </tr>\n";
	my $col = '$oddrows';
	my %uhost;
	foreach my $host (sort keys %hosts) {
		if ($hosts{$host}->{host} and $uhost{$hosts{$host}->{host}} < 1) {
			$html .= "  <tr bgcolor=$col class=row>\n";
			$html .= "    ";
			$html .= "<td><font size=1 color=\$default_text>" . $hosts{$host}->{label} . "</font></td>";
			$html .= "<td align=right><font size=1 color=\$default_text>" . $hosts{$host}->{cpu_pct} . "</font></td>";
			$html .= "<td align=right><font size=1 color=\$default_text>" . $hosts{$host}->{mem_pct} . "</font></td>";
			$html .= "\n";
			$html .= "  </tr>\n";
			if ($col eq '$oddrows') {
				$col = '$evenrows';
			} else {
				$col = '$oddrows';
			}
		}
		$uhost{$hosts{$host}->{host}}++;
	}
	$html .= "  <tr bgcolor=\$menubarcolor><td colspan=3><font size=1></font></td></tr>\n";
	$html .= "</table>\n";
	return $html;
}
sub output_html_extended {
	my %hosts = @_;
	my $html;
	$html = "<br><table bgcolor=grey cellspacing=1 cellpadding=3>\n";
	$html .= "  <tr bgcolor=\$menubarcolor>\n";
	$html .= "    ";
	$html .= "<td colspan=5 align=center><font color=white size=1><b>System</b></font></td>";
	$html .= "<td colspan=4 align=center><font color=white size=1><b>LoadAvg</b></font></td>";
	$html .= "<td colspan=2 align=center><font color=white size=1><b>CPU</b></font></td>";
	$html .= "<td colspan=4 align=center><font color=white size=1><b>MEM</b></font></td>";
	$html .= "\n";
	$html .= "  </tr>\n";
	$html .= "  <tr bgcolor=\$menubarcolor>\n";
	$html .= "    ";
	$html .= "<td align=center><font color=white size=1><b>Label</b></font></td>";
	$html .= "<td align=center><font color=white size=1><b>IP</b></font></td>";
	$html .= "<td align=center><font color=white size=1><b>Host</b></font></td>";
	$html .= "<td align=center><font color=white size=1><b>Domain</b></font></td>";
	$html .= "<td align=center><font color=white size=1><b>Timestamp</b></font></td>";
	$html .= "<td align=center><font color=white size=1><b>1min</b></font></td>";
	$html .= "<td align=center><font color=white size=1><b>5min</b></font></td>";
	$html .= "<td align=center><font color=white size=1><b>10min</b></font></td>";
	$html .= "<td align=center><font color=white size=1><b>Procs</b></font></td>";
	$html .= "<td align=center><font color=white size=1><b>Count</b></font></td>";
	$html .= "<td align=center><font color=white size=1><b>Pct</b></font></td>";
	$html .= "<td align=center><font color=white size=1><b>Total</b></font></td>";
	$html .= "<td align=center><font color=white size=1><b>Free</b></font></td>";
	$html .= "<td align=center><font color=white size=1><b>Pct</b></font></td>";
	$html .= "<td align=center><font color=white size=1><b>Swap</b></font></td>";
	$html .= "\n";
	$html .= "  </tr>\n";
	my $col = '$oddrows';
	my %uhost;
	foreach my $host (sort keys %hosts) {
		if ($hosts{$host}->{host} and $uhost{$hosts{$host}->{host}} < 1) {
			$html .= "  <tr bgcolor=$col class=row>\n";
			$html .= "    ";
			$html .= "<td><font size=1 color=\$default_text>" . $hosts{$host}->{label} . "</font></td>";
			$html .= "<td><font size=1 color=\$default_text>" . $hosts{$host}->{ip} . "</font></td>";
			$html .= "<td><font size=1 color=\$default_text>" . $hosts{$host}->{host} . "</font></td>";
			$html .= "<td><font size=1 color=\$default_text>" . $hosts{$host}->{domain} . "</font></td>";
			$html .= "<td><font size=1 color=\$default_text>" . $hosts{$host}->{timestamp} . "</font></td>";
			$html .= "<td align=right><font size=1 color=\$default_text>" . $hosts{$host}->{load_one} . "</font></td>";
			$html .= "<td align=right><font size=1 color=\$default_text>" . $hosts{$host}->{load_five} . "</font></td>";
			$html .= "<td align=right><font size=1 color=\$default_text>" . $hosts{$host}->{load_ten} . "</font></td>";
			$html .= "<td align=right><font size=1 color=\$default_text>" . $hosts{$host}->{load_procs} . "</font></td>";
			$html .= "<td align=right><font size=1 color=\$default_text>" . $hosts{$host}->{cpu_count} . "</font></td>";
			$html .= "<td align=right><font size=1 color=\$default_text>" . $hosts{$host}->{cpu_pct} . "</font></td>";
			$html .= "<td align=right><font size=1 color=\$default_text>" . $nf->format_number($hosts{$host}->{mem_total}) . "k</font></td>";
			$html .= "<td align=right><font size=1 color=\$default_text>" . $nf->format_number($hosts{$host}->{mem_free}) . "k</font></td>";
			$html .= "<td align=right><font size=1 color=\$default_text>" . $hosts{$host}->{mem_pct} . "</font></td>";
			$html .= "<td align=right><font size=1 color=\$default_text>" . $nf->format_number($hosts{$host}->{swap_used}) . "k</font></td>";
			$html .= "\n";
			$html .= "  </tr>\n";
			if ($col eq '$oddrows') {
				$col = '$evenrows';
			} else {
				$col = '$oddrows';
			}
		}
		$uhost{$hosts{$host}->{host}}++;
	}
	$html .= "  <tr bgcolor=\$menubarcolor><td colspan=15><font size=1></font></td></tr>\n";
	$html .= "</table>\n";
	return $html;
}

sub initsocks {
	my ($sock) = @_;
	unless ($sock) {
		my $group = '226.1.1.2';
		my $port = '2001';
		print STDERR "osdial_resource_listen: (Re)Init called...";
		$sock = IO::Socket::Multicast->new(Proto=>'udp',LocalPort=>$port);
		if ($sock) {
			if ($sock->mcast_add($group)) {
				print STDERR "SUCCESS.\n";	
			} else {
				$sock = undef;
			}
		}
		print STDERR "FAILED.\n" unless ($sock);
	}
	return $sock;
}

sub getOSDconfig {
	my($AGCpath) = @_;
	my %config;
	$config{PATHconf} = $AGCpath;
	open(CONF, $config{PATHconf}) || die "can't open " . $config{PATHconf} . ": " . $! . "\n";
	while (my $line = <CONF>) {
		$line =~ s/ |>|"|'|\n|\r|\t|\#.*|;.*//gi;
		if ($line =~ /=|:/) {
			my($key,$val) = split /=|:/, $line;
			$config{$key} = $val;
		}
	}
	$config{VARDB_port} = '3306' unless ($config{VARDB_port});
	$config{VARFTP_port} = '21' unless ($config{VARFTP_port});
	$config{VARREPORT_port} = '21' unless ($config{VARREPORT_port});
	return \%config;
}

