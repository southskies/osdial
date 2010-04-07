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
        bless $self, $class;

	print STDERR "  -- OSDial: new:  Initializing OSDial module, debug-level is " . $self->{DB} . ".\n" if ($self->{DB}>4);
	
        $self->_load_config();
        $self->{settings} = $self->sql_query("SELECT * FROM system_settings LIMIT 1;");
        $self->{server} = $self->sql_query(sprintf("SELECT * FROM servers WHERE server_ip='%s';", $self->{VARserver_ip}));
        return $self;
}


sub _load_config {
	my $self = shift;
        if (-e $self->{PATHconf}) {
		print STDERR "  -- OSDial: _load_config:  Loading configuration file (" . $self->{DB} . ").\n" if ($self->{DB}>4);
        	open(CONF, $self->{PATHconf}) or die 'OSDial: Error opening ' . $self->{PATHconf} . "\n";
        	while (my $line = <CONF>) {
        	        $line =~ s/ |>|"|'|\n|\r|\t|\#.*|;.*//gi;
        	        if ($line =~ /=|:/) {
        	                my($key,$val) = split /=|:/, $line;
        	                $self->{$key} = $val;
				print STDERR "  -- OSDial: _load_config:    Setting: $key    => $val\n" if ($self->{DB}>4);
        	        }
        	}
	} else {
		print STDERR '  -- OSDial: _load_config:  Configuration file (' . $self->{PATHconf} . ") does not exist.\n";
	}
}


sub sql_connect {
	my ($self, $dbh) = @_;
	$dbh = 'A' unless ($dbh);
	$self->{_sql}{$dbh} = {'connected'=>0} if (!defined $self->{_sql}{$dbh});
	if ($self->{_sql}{$dbh}{connected}<1) {
		my $dsn = 'DBI:mysql:' . $self->{VARDB_database} . ':' . $self->{VARDB_server} . ':' . $self->{VARDB_port};
		print STDERR "  -- OSDial: sql_connect:  Connecting to dbh $dbh at DSN: $dsn.\n" if ($self->{DB}>9);
		$self->{_sql}{$dbh}{dbh} = DBI->connect($dsn,$self->{VARDB_user},$self->{VARDB_pass}) or die '  -- OSDial: sql_connect:  ERROR ' . $self->{_sql}{$dbh}{dbh}->errstr;
		$self->{_sql}{$dbh}{dbh}{PrintError} = 0;
		$self->{_sql}{$dbh}{connected} = 1;
	}
}

sub sql_disconnect {
	my ($self, $dbh) = @_;
	$dbh = 'A' unless ($dbh);
	$self->{_sql}{$dbh} = {'connected'=>0} if (!defined $self->{_sql}{$dbh});
	if ($self->{_sql}{$dbh}{connected}>0) {
		print STDERR "  -- OSDial: sql_disconnect:  Disconnecting from dbh $dbh.\n" if ($self->{DB}>9);
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
				print STDERR "  -- OSDial: sql_query $dbh:    [iteration]  already sent last row for this query, sending undef and exiting.\n" if ($self->{DB}>9);
				delete $self->{_sql}{$dbh}{last_stmt};
				delete $self->{_sql}{$dbh}{rows};
				delete $self->{_sql}{$dbh}{row};
				return undef;
			}
		}

		if (defined $self->{_sql}{$dbh}{sth}) {
			# They current and previous run differ, clear and run new stmt.
			if ($stmt ne '' and $stmt ne $self->{_sql}{$dbh}{last_stmt}) {
				print STDERR "  -- OSDial: sql_query $dbh:    [iteration]  last_stmt and stmt differ, clearing and moving on.\n" if ($self->{DB}>9);
				$self->{_sql}{$dbh}{sth}->finish();
				delete $self->{_sql}{$dbh}{last_stmt};
				delete $self->{_sql}{$dbh}{rows};
				delete $self->{_sql}{$dbh}{row};
				delete $self->{_sql}{$dbh}{sth};

			# stmt is blank, so lets set it to the last_stmt.
			} elsif ($stmt eq '' or $stmt eq $self->{_sql}{$dbh}{last_stmt}) {
				print STDERR "  -- OSDial: sql_query $dbh:    [iteration]  stmt blank or same as last_stmt, moving on.\n" if ($self->{DB}>9);
				$self->{_sql}{$dbh}{last_stmt} = $stmt;
			}
		}
	}

	# If connected to DB and sth has not been defined, issue query.
	if (defined $self->{_sql}{$dbh}{dbh} and !defined $self->{_sql}{$dbh}{sth}) {
		print STDERR "  -- OSDial: sql_query $dbh:  [execute query]  $stmt.\n" if ($self->{DB}>9);
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
			print STDERR "  -- OSDial: sql_query $dbh:  [set rows]  Total rows found: " . $self->{_sql}{$dbh}{rows} . ".\n" if ($self->{DB}>9);
		}

		# Test if we were just initializing or if we are running.
		if ($opts->{init} > 0) {
			print STDERR "  -- OSDial: sql_query $dbh:  [init]  Init is set, returning before getting first row.\n" if ($self->{DB}>9);
			return $self->{_sql}{$dbh}{rows};
		} else {
			# Get the record and increment count if we find one.
			print STDERR "  -- OSDial: sql_query $dbh:    [fetch row]  Getting row, iteration #: " . $self->{_sql}{$dbh}{row} . ".\n" if ($self->{DB}>9);
        		$row = $self->{_sql}{$dbh}{sth}->fetchrow_hashref;
			$row->{ROW} = ++$self->{_sql}{$dbh}{row} if ($row);
		}

		# If row count and current row are equal destroy sth and return row if there is one.
		if ($self->{_sql}{$dbh}{row} == $self->{_sql}{$dbh}{rows}) {
			print STDERR "  -- OSDial: sql_query $dbh:    [last row]  Reached last row, exiting.\n" if ($self->{DB}>9);
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

	print STDERR "  -- OSDial: sql_execute $dbh:  executing: $stmt.\n" if ($self->{DB}>9);
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
	my ($self,$script,$AGI,$agi_string,$extinfo) = @_;
	if ($self->{server}{agi_output} and $script and $AGI and $agi_string) {
		$agi_string .= '|' . $AGI->{uniqueid} if ($AGI->{uniqueid});
		$agi_string .= '|' . $AGI->{CIDlead_id} if ($AGI->{CIDlead_id});
		$agi_string .= '|' . join('|',$AGI->{channel},$AGI->{extension},$AGI->{priority},$AGI->{type},$AGI->{accountcode}) if ($extinfo);
		if ($self->{server}{agi_output} =~ /FILE|BOTH/) {
			### open the log file for writing ###
        		my $logfile = $self->{PATHlogs} . '/agiout.' . $self->get_date();
			open(Lout, '>>' . $logfile) or die '  -- OSDial: agi_output:  Error opening ' . $logfile . "\n";
			print Lout sprintf("\%s|\%s|\%s|\%s\n",$self->get_datetime(),$script,$$,$agi_string);
			close(Lout);
		}
		### send to STDERR writing ###
		print STDERR sprintf("\%s|\%s|\%s|\%s\n",$self->get_datetime(),$script,$$,$agi_string) if ($self->{server}{agi_output} =~ /STDERR|BOTH/ or $self->{DB} > 1);
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
