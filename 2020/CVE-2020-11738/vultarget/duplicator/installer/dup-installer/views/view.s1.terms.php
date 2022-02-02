<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>
<!-- ====================================
TERMS & NOTICES DIALOG
==================================== -->
<div id="dialog-terms" title="Terms and Notices" style="display:none">
	<div id="s1-warning-msg">
		<b>TERMS &amp; NOTICES</b> <br/><br/>

		<b>Disclaimer:</b>
		The Duplicator software and installer should be used at your own risk.  Users should always back up or have backups of your database and files before running this installer.
		If you're not sure about how to use this tool then please enlist the guidance of a technical professional.  <u>Always</u> test this installer in a sandbox environment
		before trying to deploy into a production environment.  Be sure that if anything happens during the install that you have a backup recovery plan in place.   By accepting
		this agreement the users of this software do not hold liable Snapcreek LLC or any of its affiliates liable for any issues that might occur during use of this software.
		<br/><br/>

		<b>Database:</b>
		Do not connect to an existing database unless you are 100% sure you want to remove all of it's data. Connecting to a database that already exists will permanently
		DELETE all data in that database. This tool is designed to populate and fill a database with NEW data from a duplicated database using the SQL script in the
		package name above.
		<br/><br/>

		<b>Setup:</b>
		Only the archive and installer file should be in the install directory, unless you have manually extracted the package and selected the
		'Manual Archive Extraction' option under options. All other files will be OVERWRITTEN during install.  Make sure you have full backups of all your databases and files
		before continuing with an installation. Manual extraction requires that all contents in the package are extracted to the same directory as the installer file.
		Manual extraction is only needed when your server does not support the ZipArchive extension.  Please see the online help for more details.
		<br/><br/>

		<b>After Install:</b> When you are done with the installation you must remove these files/directories:
		<ul>
			<li>dup-installer</li>
			<li>installer.php</li>
			<li>installer-backup.php</li>
			<li>dup-installer-bootlog__[HASH].txt</li>
			<!--li>dup-wp-config-arc_[HASH].txt</li-->
			<li>[HASH]_archive.zip/daf</li>
		</ul>

		These files contain sensitive information and should not remain on a production system for system integrity and security protection.
		<br/><br/>

		<b>License Overview</b><br/>
		Duplicator is licensed under the GPL v3 https://www.gnu.org/licenses/gpl-3.0.en.html including the following disclaimers and limitation of liability.
		<br/><br/>

		<b>Disclaimer of Warranty</b><br/>
		THERE IS NO WARRANTY FOR THE PROGRAM, TO THE EXTENT PERMITTED BY APPLICABLE LAW. EXCEPT WHEN OTHERWISE STATED IN WRITING THE COPYRIGHT HOLDERS AND/OR OTHER PARTIES
		PROVIDE THE PROGRAM “AS IS” WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
		FITNESS FOR A PARTICULAR PURPOSE. THE ENTIRE RISK AS TO THE QUALITY AND PERFORMANCE OF THE PROGRAM IS WITH YOU. SHOULD THE PROGRAM PROVE DEFECTIVE, YOU ASSUME
		THE COST OF ALL NECESSARY SERVICING, REPAIR OR CORRECTION.
		<br/><br/>

		<b>Limitation of Liability</b><br/>
		IN NO EVENT UNLESS REQUIRED BY APPLICABLE LAW OR AGREED TO IN WRITING WILL ANY COPYRIGHT HOLDER, OR ANY OTHER PARTY WHO MODIFIES AND/OR CONVEYS THE PROGRAM AS
		PERMITTED ABOVE, BE LIABLE TO YOU FOR DAMAGES, INCLUDING ANY GENERAL, SPECIAL, INCIDENTAL OR CONSEQUENTIAL DAMAGES ARISING OUT OF THE USE OR INABILITY TO USE THE
		PROGRAM (INCLUDING BUT NOT LIMITED TO LOSS OF DATA OR DATA BEING RENDERED INACCURATE OR LOSSES SUSTAINED BY YOU OR THIRD PARTIES OR A FAILURE OF THE PROGRAM TO
		OPERATE WITH ANY OTHER PROGRAMS), EVEN IF SUCH HOLDER OR OTHER PARTY HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.
		<br/><br/>

	</div>
</div>

<script>
/**
 * View the terms an notices section */
DUPX.viewTerms = function ()
{
	$( "#dialog-terms" ).dialog({
	  resizable: false,
	  height: 600,
	  width: 550,
	  modal: true,
	  position: { my: 'top', at: 'top+150' },
	  buttons: {
		"OK": function() {
			$(this).dialog("close");
		}
	  }
	});
}
</script>