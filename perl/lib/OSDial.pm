#
# OSDial.pm
#
## Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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
#
package OSDial;

use 5.008000;
use strict;
use warnings;

use DBI;
use Asterisk::AGI;

our $VERSION = '2.2.1.053';

my %vars = (
        'DB'                   => 0,

        'PATHconf'             => '/etc/osdial.conf',
	'PATHdocs'             => '/usr/share/doc/osdial-SVN_Version',
	'PATHhome'             => '/opt/osdial/bin',
	'PATHlogs'             => '/var/log/osdial',
	'PATHagi'              => '/var/lib/asterisk/agi-bin',
	'PATHweb'              => '/opt/osdial/html',
	'PATHsounds'           => '/var/lib/asterisk/sounds',
	'PATHmonitor'          => '/var/spool/asterisk/VDmonitor',
	'PATHDONEmonitor'      => '/var/spool/asterisk/VDmonitor',
	'PATHarchive_home'     => '/opt/osdial/recordings',
	'PATHarchive_unmixed'  => 'processing/unmixed',
	'PATHarchive_mixed'    => 'processing/mixed',
	'PATHarchive_sorted'   => 'completed',

        'VARserver_ip'         => '127.0.0.1',
        'VARactive_keepalives' => 'X',

        'VARDB_server'         => '127.0.0.1',
        'VARDB_database'       => 'osdial',
        'VARDB_user'           => 'osdial',
        'VARDB_pass'           => 'osdial1234',
        'VARDB_port'           => '3306',

	'VARfastagi_log_min_servers'       => '3',
	'VARfastagi_log_max_servers'       => '16',
	'VARfastagi_log_min_spare_servers' => '2',
	'VARfastagi_log_max_spare_servers' => '8',
	'VARfastagi_log_max_requests'      => '1000',
	'VARfastagi_log_checkfordead'      => '30',
	'VARfastagi_log_checkforwait'      => '60',

	'VARFTP_host'    => '127.0.0.1',
	'VARFTP_user'    => 'osdial',
	'VARFTP_pass'    => 'osdialftp1234',
	'VARFTP_port'    => '21',
	'VARFTP_dir'     => 'recordings/processing/unmixed',
	'VARHTTP_path'   => '/',

	'VARREPORT_host' => '127.0.0.1',
	'VARREPORT_user' => 'osdial',
	'VARREPORT_pass' => 'osdialftp1234',
	'VARREPORT_port' => '21',
	'VARREPORT_dir'  => 'reports',

	'VARcps'                   => '9',
	'VARadapt_min_level'       => '1.5',
	'VARadapt_overlimit_mod'   => '20',
	'VARflush_hopper_each_run' => '0',
	'VARflush_hopper_manual'   => '1',

	'_sql'  => { },
);


sub new {
        my ($proto,%options) = @_;
        my $class = ref($proto) || $proto;

        my $self = {%vars};
        foreach my $key (keys %options) {
                $self->{$key} = $options{$key};
        }
	$self->{DB} = 0 unless ($self->{DB});
        bless $self, $class;

	$self->debug(1,'new',"Initializing OSDial module, debug-level is %s.",$self->{DB});
	
        $self->_load_config();
        $self->{settings} = $self->sql_query("SELECT * FROM system_settings LIMIT 1;");
	foreach my $st (keys %{$self->{settings}}) {
		$self->debug(4,'new','    %-30s => %-30s.',$st,$self->{settings}{$st});	
	}
        $self->{server} = $self->sql_query(sprintf("SELECT * FROM servers WHERE server_ip='%s';", $self->{VARserver_ip}));
	foreach my $st (keys %{$self->{server}}) {
		$self->debug(4,'new','    %-30s => %-30s.',$st,$self->{server}{$st});	
	}
        return $self;
}

# Start AGI when called first time, Allows for passing commands on subsequest requests.
# use OSDial;
# my $osdial = new OSDial;
# $osdial->AGI('agi-script_name.agi');
# $osdial->AGI->verbose("OSDial Rocks",1);
# $osdial->AGI->hangup();
#
sub AGI {
	my ($self,$mod) = @_;
	if (!defined $self->{_agi}) {
		die "  -- OSDial [AGI]: Function must be passed the name of the calling module." unless ($mod);

		$self->{_agi} = new Asterisk::AGI;
		my %aout = $self->{_agi}->ReadParse();
		$self->agi_output("AGI Environment Dump:");
		foreach my $i (sort keys %aout) {
		        $self->{_agi}{$i} = $aout{$i};
		        $self->agi_output(" -- $i = " . $self->{_agi}{$i});
		}
		$self->{_agi}{mod} = $mod;
		return 1;
	}
	return $self->{_agi};
}



sub debug {
	my ($self, $lev, $mod, $string, @params) = @_;
	if($self->{DB}>=$lev) {
		my $p = 2+$lev;
		my @sprint = (' ',$mod,@params);
		$string .= "\n" unless($string =~ /\n$/);
		print STDERR sprintf('%'.$p.'s-- OSDial [%s]:  '.$string,@sprint);
	}
}

sub _load_config {
	my $self = shift;
        if (-e $self->{PATHconf}) {
		$self->debug(4,'_load_config',"Loading configuration file (%s).",$self->{PATHconf});
        	open(CONF, $self->{PATHconf}) or die 'OSDial: Error opening ' . $self->{PATHconf} . "\n";
        	while (my $line = <CONF>) {
        	        $line =~ s/ |>|"|'|\n|\r|\t|\#.*|;.*//gi;
        	        if ($line =~ /=|:/) {
        	                my($key,$val) = split /=|:/, $line;
        	                $self->{$key} = $val;
				$self->debug(4,'_load_config',"    %-40s => %-40s.",$key,$val);
        	        }
        	}
	} else {
		$self->debug(0,'_load_config',"Configuration file (%s) does not exist.",$self->{PATHconf});
	}
}


sub sql_connect {
	my ($self, $dbh, $dbname, $dbserver, $dbport, $dbuser, $dbpass) = @_;
	$dbh = 'A' unless ($dbh);
	unless ($dbname) {
		$dbname = $self->{VARDB_database};
		$dbsrvr = $self->{VARDB_server};
		$dbport = $self->{VARDB_port};
		$dbuser = $self->{VARDB_user};
		$dbpass = $self->{VARDB_pass};
	}
	$self->{_sql}{$dbh} = {'connected'=>0} if (!defined $self->{_sql}{$dbh});
	if ($self->{_sql}{$dbh}{connected}<1) {
		my $dsn = 'DBI:mysql:' . $dbname . ':' . $dbsrvr . ':' . $dbport;
		$self->debug(5,'sql_connect',"Connecting to dbh %s at DSN: %s.",$dbh,$dsn);
		$self->{_sql}{$dbh}{dbh} = DBI->connect($dsn,$dbuser,$dbpass) or die '  -- OSDial: sql_connect:  ERROR ' . $self->{_sql}{$dbh}{dbh}->errstr;
		$self->{_sql}{$dbh}{dbh}{PrintError} = 0;
		$self->{_sql}{$dbh}{connected} = 1;
	}
}

sub sql_disconnect {
	my ($self, $dbh) = @_;
	$dbh = 'A' unless ($dbh);
	$self->{_sql}{$dbh} = {'connected'=>0} if (!defined $self->{_sql}{$dbh});
	if ($self->{_sql}{$dbh}{connected}>0) {
		$self->debug(5,'sql_disconnect',"Disconnecting from dbh %s.",$dbh);
		$self->{_sql}{$dbh}{dbh}->disconnect();
		$self->{_sql}{$dbh}{connected} = 0;
	}
}

sub sql_query {
        my ($self,$opt1,$opt2) = @_;
	my $row;

	my $opts = {};

	if (ref($opt1) eq "HASH") {
		$opts = $opt1;
	} elsif (ref($opt2) eq "HASH") {
		$opts = $opt2;
		$opts->{stmt} = $opt1;
	} else {
		$opts->{stmt} = $opt1;
		$opts->{dbh}  = $opt2;
	}
	# Set some defaults...
	$opts->{stmt} = '' if (!defined $opts->{stmt});   # The query to execute.
	$opts->{dbh} = 'A' if (!defined $opts->{dbh});    # the label used for dbh indentification.
	$opts->{init} = 0 if (!defined $opts->{init});    # If 1, Stop before attempting first record grab.

	my $stmt = $opts->{stmt};
	my $dbh  = $opts->{dbh};

	# If dbh has not been defined, connect to DB.
	$self->sql_connect($dbh) if (!defined $self->{_sql}{$dbh});

	return $self->sql_execute($opts) if ($stmt =~ /^update|^insert|^delete/i);

	# Check if this run is an iteration.
	if (defined $self->{_sql}{$dbh}{last_stmt}) {

		# Last stmt is set is but no sth, must have finished...
		if (!defined $self->{_sql}{$dbh}{sth}) {
			# stmt is not blank, and statement differs from last_stmt, clear and move on.
			if ($stmt ne '' and $stmt ne $self->{_sql}{$dbh}{last_stmt}) {
				delete $self->{_sql}{$dbh}{last_stmt};
				delete $self->{_sql}{$dbh}{rows};
				delete $self->{_sql}{$dbh}{row};
				delete $self->{_sql}{$dbh}{sth};

			# stmt is blank or same, but query already finished, clear and exit.
			} elsif ($stmt eq '' or $stmt eq $self->{_sql}{$dbh}{last_stmt}) {
				$self->debug(9,'sql_query',"DBH %-6s  [iteration]  already sent last row for this query, sending undef and exiting.",$dbh);
				delete $self->{_sql}{$dbh}{last_stmt};
				delete $self->{_sql}{$dbh}{rows};
				delete $self->{_sql}{$dbh}{row};
				return undef;
			}
		}

		if (defined $self->{_sql}{$dbh}{sth}) {
			# They current and previous run differ, clear and run new stmt.
			if ($stmt ne '' and $stmt ne $self->{_sql}{$dbh}{last_stmt}) {
				$self->debug(9,'sql_query',"DBH %-6s  [iteration]  last_stmt and stmt differ, clearing and moving on.",$dbh);
				$self->{_sql}{$dbh}{sth}->finish();
				delete $self->{_sql}{$dbh}{last_stmt};
				delete $self->{_sql}{$dbh}{rows};
				delete $self->{_sql}{$dbh}{row};
				delete $self->{_sql}{$dbh}{sth};

			# stmt is blank, so lets set it to the last_stmt.
			} elsif ($stmt eq '' or $stmt eq $self->{_sql}{$dbh}{last_stmt}) {
				$self->debug(9,'sql_query',"DBH %-6s  [iteration]  stmt blank or same as last_stmt, moving on.",$dbh);
				$self->{_sql}{$dbh}{last_stmt} = $stmt;
			}
		}
	}

	# If connected to DB and sth has not been defined, issue query.
	if (defined $self->{_sql}{$dbh}{dbh} and !defined $self->{_sql}{$dbh}{sth}) {
		$self->debug(5,'sql_query',"DBH %-6s  [execute]  STMT:  %s",$dbh, $stmt);
        	$self->{_sql}{$dbh}{sth} = $self->{_sql}{$dbh}{dbh}->prepare($stmt) or die "  -- OSDial: sql_query $dbh:  ERROR " . $self->{_sql}{$dbh}{dbh}->errstr;
        	$self->{_sql}{$dbh}{sth}->execute or die "  -- OSDial: sql_query $dbh:  ERROR " . $self->{_sql}{$dbh}{dbh}->errstr;
		$self->{_sql}{$dbh}{rows} = 0;
		$self->{_sql}{$dbh}{last_stmt} = $stmt;
		delete $self->{_sql}{$dbh}{row};
	}

	# If sth is defined, start record grab.
	if (defined $self->{_sql}{$dbh}{sth}) {
		# Get row counts if we havent yet.
		if (!defined $self->{_sql}{$dbh}{row}) {
			$self->{_sql}{$dbh}{row} = 0;
			$self->{_sql}{$dbh}{rows} = $self->{_sql}{$dbh}{sth}->rows();
			$self->debug(5,'sql_query',"DBH %-6s  [row_count]  %s",$dbh, $self->{_sql}{$dbh}{row});
		}

		# Test if we were just initializing or if we are running.
		if ($opts->{init} > 0) {
			$self->debug(9,'sql_query',"DBH %-6s  [init]  Init is set, returning before getting first row.",$dbh);
			return $self->{_sql}{$dbh}{rows};
		} else {
			# Get the record and increment count if we find one.
			$self->debug(6,'sql_query',"DBH %-6s  [fetch_row]  Getting row, iteration # %s.",$dbh, $self->{_sql}{$dbh}{row});
        		$row = $self->{_sql}{$dbh}{sth}->fetchrow_hashref;
			$row->{ROW} = ++$self->{_sql}{$dbh}{row} if ($row);
		}

		# If row count and current row are equal destroy sth and return row if there is one.
		if ($self->{_sql}{$dbh}{row} == $self->{_sql}{$dbh}{rows}) {
			$self->debug(7,'sql_query',"DBH %-6s  [last_row]  Reached last row, exiting.",$dbh);
			$self->{_sql}{$dbh}{sth}->finish();
			delete $self->{_sql}{$dbh}{sth};
		}
	}
	return $row;
}

sub sql_execute {
        my ($self,$opt1,$opt2) = @_;
	my $row;

	my $opts = {};

	if (ref($opt1) eq "HASH") {
		$opts = $opt1;
	} elsif (ref($opt2) eq "HASH") {
		$opts = $opt2;
		$opts->{stmt} = $opt1;
	} else {
		$opts->{stmt} = $opt1;
		$opts->{dbh}  = $opt2;
	}
	# Set some defaults...
	$opts->{stmt} = '' if (!defined $opts->{stmt});  # The query to execute.
	$opts->{dbh} = 'A' if (!defined $opts->{dbh});   # the label used for dbh indentification.
	$opts->{init} = 0 if (!defined $opts->{init});   # If 1, Stop before attempting first record grab.

	my $stmt = $opts->{stmt};
	my $dbh  = $opts->{dbh};

	# If dbh has not been defined, connect to DB.
	$self->sql_connect($dbh) if (!defined $self->{_sql}{$dbh});

	return $self->sql_query($opts) if ($stmt =~ /^select/i);

	$self->debug(5,'sql_query',"DBH %-6s  [execute]  STMT:  %s",$dbh, $stmt);
	$self->{_sql}{$dbh}{rows} = $self->{_sql}{$dbh}{dbh}->do($stmt) or die "  -- OSDial: sql_execute $dbh:  ERROR " . $self->{_sql}{$dbh}{dbh}->errstr;
	return $self->{_sql}{$dbh}{rows};
}

sub get_datetime {
	my($self,$tms) = @_;
	$tms = time() unless ($tms);
	my ($s,$m,$h,$D,$M,$Y,$wday,$yday,$isdst) = localtime($tms);
	$Y += 1900;
	return sprintf('%.4d-%.2d-%.2d %.2d:%.2d:%.2d', $Y, ++$M, $D, $h, $m, $s);
}

sub get_date {
	my($self,$tms) = @_;
	return substr($self->get_datetime($tms),0,10);
}


sub agi_output {
	my ($self,$agi_string,$extinfo) = @_;
	if ($self->{server}{agi_output} and $self->{_agi}{mod} and $self->{_agi} and $agi_string) {
		$agi_string .= '|' . $self->{_agi}{uniqueid} if ($self->{_agi}{uniqueid});
		$agi_string .= '|' . $self->{_agi}{CIDlead_id} if ($self->{_agi}{CIDlead_id});
		$agi_string .= '|' . join('|',$self->{_agi}{channel},$self->{_agi}{extension},$self->{_agi}{priority},$self->{_agi}{type},$self->{_agi}{accountcode}) if ($extinfo);
		if ($self->{server}{agi_output} =~ /FILE|BOTH/) {
			### open the log file for writing ###
        		my $logfile = $self->{PATHlogs} . '/agiout.' . $self->get_date();
			open(Lout, '>>' . $logfile) or die '  -- OSDial: agi_output:  Error opening ' . $logfile . "\n";
			print Lout sprintf("\%s|\%s|\%s|\%s\n",$self->get_datetime(),$self->{_agi}{mod},$$,$agi_string);
			close(Lout);
		}
		### send to STDERR writing ###
		$self->debug(2,'agi_output','%s|%s|%s|%s',$self->get_datetime(),$self->{_agi}{mod},$$,$agi_string) if ($self->{server}{agi_output} =~ /STDERR|BOTH/);
	}
}

sub event_logger {
        my ($self,$type,$string) = @_;
	if ($type and $string) {
        	my $logfile = $self->{PATHlogs} . '/' . $type . '.' . $self->get_date();
        	open(LOG, '>>' . $logfile) or die '  -- OSDial: event_logger:  Error opening ' . $logfile . "\n";
        	print LOG sprintf("\%s|\%s|\%s\n",$self->get_datetime(),$$,$string);
        	close(LOG);
	}
}


# Make sure we do a little cleanup before exiting.
sub DESTROY {
	my $self = shift;
	foreach my $dbh (keys %{$self->{_sql}}) {
		$self->sql_disconnect($dbh) if ($self->{_sql}{$dbh}{connected}>0);
	}
}


1;
__END__

=head1 NAME

OSDial - Perl extension for interfacing with OSDial 

=head1 SYNOPSIS

  use strict;
  use OSDial;

  my $osdial = OSDial->new();

  while ( my $rec = $osdial->sql_query("SELECT * FROM servers;") ) {
    print $rec->{server_ip} . ": " . $rec->{server_name} . "\n";
  }

=head1 DESCRIPTION

This module is inteded to provided quick and easy access to common functions
in OSDial.  The module will read existing configuration files, connect to
the OSDial database, and interface with Asterisk as needed.

=head2 EXPORT

None by default.



=head1 SEE ALSO

=head1 AUTHOR

Lott Caskey, E<lt>lottcaskey@gmail.com<gt>

=head1 COPYRIGHT AND LICENSE

Copyright (C) 2010 by Lott Caskey

This library is free software; you can redistribute it and/or modify
it under the same terms as Perl itself, either Perl version 5.10.0 or,
at your option, any later version of Perl 5 you may have available.


=cut
