<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;box-sizing:border-box;width:100%;">
	<tbody>
	<tr>
		<td align="center" style="font-family:sans-serif;font-size:14px;vertical-align:top;padding-bottom:15px;">
			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;width:100%;width:auto;">
				<tbody>

				</tbody>
			</table>
		</td>
	</tr>
	</tbody>
</table>
<?php
if ( isset( $body ) && $body !== false ) {
	echo wpautop( $body );
}