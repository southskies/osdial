#!/usr/bin/perl

# This script listens for a multicast packet containing the IP
# address of the web-server.  It then adds it to the hosts file
# and exits.

use strict;
use OSDial;
use Getopt::Long;
use Number::Format;
use Net::IP;
use IO::Socket::Multicast;
$|++;

my $use_multicast=0;

my $DB = 0;
$DB = 1 if ($ARGV[0] eq '-v');

GetOptions('multicast!' => \$use_multicast, 'debug!' => \$DB);

my $nf = new Number::Format;

my $interface;
my %hosts;

my $osdial;
$osdial = OSDial->new('DB'=>$DB) if ($use_multicast);

while (1) {
	my $count=0;
	while ($interface = initinterfaces($interface)) {
		my $data;
		if ($use_multicast) {
			next unless $interface->recv($data,1024);
			if ($data) {
				my %kvh;
				my @kva = split(/&/,$data);
				foreach my $kv (@kva) {
					my($k,$v) = split(/=/,$kv);
					$kvh{$k} = $v;
				}
				if ($kvh{ip} =~ /$osdial->{WEBserver_stats_regex}/ or $kvh{host} =~ /$osdial->{WEBserver_stats_regex}/) {
					my $ip = new Net::IP($kvh{ip});
					$hosts{$ip->intip()} = \%kvh;
					$count++;
				}
			}
		} else {
			$osdial = $interface;
			$count=0;
			while (my $sret = $interface->sql_query("SELECT SQL_NO_CACHE * FROM server_stats WHERE update_time>NOW()-1000;")) {
				my %kvh;
				foreach my $key (keys %{$sret}) {
					$kvh{$key} = $sret->{$key};
				}
				$kvh{ip} = $kvh{server_ip};
				$kvh{timestamp} = $kvh{server_timestamp};
				if ($kvh{ip} =~ /$osdial->{WEBserver_stats_regex}/ or $kvh{host} =~ /$osdial->{WEBserver_stats_regex}/) {
					my $ip = new Net::IP($kvh{ip});
					$hosts{$ip->intip()} = \%kvh;
					$count++;
				}
			}
		}
		if ($count>0) {
			open(HTML, ">" . $osdial->{PATHweb} . "/admin/resources.txt");
			print HTML output_html(%hosts);
			close(HTML);
			open(HTML, ">" . $osdial->{PATHweb} . "/admin/resources-xtd.txt");
			print HTML output_html_extended(%hosts);
			close(HTML);
		}
		if ($count>9) {
			$count=0;
			# Send to remote server every 10 grabs.
			if ($osdial->{WEBserver_stats_remote_scp_host} ne "" and $osdial->{WEBserver_stats_remote_scp_path} ne "") {
				my $cmd = "/usr/bin/scp " . $osdial->{PATHweb} . "/admin/resources*.txt " . $osdial->{WEBserver_stats_remote_scp_host} . ":" . $osdial->{WEBserver_stats_remote_scp_path} . '> /dev/null 2>&1';
				my $send = `$cmd`;
			}
		}
		sleep(5);
	}
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

sub initinterfaces {
	my ($int) = @_;
	unless ($int) {
		if ($use_multicast) {
			my $group = '226.1.1.2';
			my $port = '2001';
			print STDERR "osdial_resource_listen: (Re)Init called...";
			$int = IO::Socket::Multicast->new(Proto=>'udp',LocalPort=>$port);
			if ($int) {
				if ($int->mcast_add($group)) {
					print STDERR "SUCCESS.\n";	
				} else {
					$int = undef;
				}
			}
			print STDERR "FAILED.\n" unless ($int);
		} else {
			$int = OSDial->new('DB'=>$DB);
		}
	}
	return $int;
}
