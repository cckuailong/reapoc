<?php
/**
 * Information about debugging displayed in server configuration widget.
 *
 * @var string $size_png_path         Size of file.
 * @var string $size_png2_path        Size of file.
 * @var string $size_png_url          Size of file.
 * @var string $size_png2_url         Size of file.
 * @var string $size_png_as_webp_url  Size of file.
 * @var string $size_png2_as_webp_url Size of file.
 * @package WebP Converter for Media
 */

?>
<h4>Errors debug</h4>
<table>
	<tbody>
	<tr>
		<td class="e">Size of PNG <em>(by server path )</em></td>
		<td class="v">
			<?php echo esc_html( $size_png_path ); ?>
		</td>
	</tr>
	<tr>
		<td class="e">Size of PNG2 <em>(by server path )</em></td>
		<td class="v">
			<?php echo esc_html( $size_png2_path ); ?>
		</td>
	</tr>
	<tr>
		<td class="e">Size of PNG as WEBP <em>(by URL)</em></td>
		<td class="v">
			<?php echo esc_html( $size_png_as_webp_url ); ?>
		</td>
	</tr>
	<tr>
		<td class="e">Size of PNG as PNG <em>(by URL)</em></td>
		<td class="v">
			<?php echo esc_html( $size_png_url ); ?>
		</td>
	</tr>
	<tr>
		<td class="e">Size of PNG2 as WEBP <em>(by URL)</em></td>
		<td class="v">
			<?php echo esc_html( $size_png2_as_webp_url ); ?>
		</td>
	</tr>
	<tr>
		<td class="e">Size of PNG2 as PNG2 <em>(by URL)</em></td>
		<td class="v">
			<?php echo esc_html( $size_png2_url ); ?>
		</td>
	</tr>
	</tbody>
</table>
