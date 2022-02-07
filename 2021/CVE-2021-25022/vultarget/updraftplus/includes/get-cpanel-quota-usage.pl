#!/usr/local/bin/perl

use strict;
use Env qw(UPDRAFTPLUSKEY);

if ($UPDRAFTPLUSKEY ne 'updraftplus') { die('Error'); }
BEGIN { unshift @INC, '/usr/local/cpanel'; }

use Cpanel::Quota ();

# Used, limit, remain, files used, files limit, files remain
my @homesize = ( Cpanel::Quota::displayquota( { 'bytes' => 1, 'include_sqldbs' => 1, 'include_mailman' => 1, }));
print 'RESULT: '.join(" ", @homesize)."\n";
