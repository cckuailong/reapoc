=== WebP Converter for Media - Convert WebP and AVIF & Optimize Images ===
Contributors: mateuszgbiorczyk
Donate link: https://ko-fi.com/gbiorczyk/?utm_source=webp-converter-for-media&utm_medium=readme-donate
Tags: convert webp, webp, optimize images, compress images, webp converter
Requires at least: 4.9
Tested up to: 5.8
Requires PHP: 7.0
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Convert WebP and AVIF just now! Speed up your website by serving WebP and AVIF images instead of standard formats JPEG, PNG and GIF.

== Description ==

Speed up your website by serving WebP and AVIF images. By replacing files in standard JPEG, PNG and GIF formats with WebP and AVIF formats, you can save over a half of the page weight without losing quality.

After installing the plugin you do not have to do anything more. Your current images will be converted into a new format. When compress images is finished, users will automatically receive new, much lighter images than the original ones.

As of today, over 90% of users use browsers that support the WebP format. The loading time of your website depends to a large extent on its weight. Using convert WebP, now you can and speed up it in a few seconds without much effort!

This will be a profit both for your users who will not have to download so much data, but also for a server that will be less loaded. Remember that a better optimized website also affects your Google ranking.

#### AVIF support

Now you can use AVIF as the output format for your images. The AVIF format is a new extension - is the successor to WebP. It allows you to achieve even higher levels of image compression, and the quality of the converted images is better than in WebP.

#### How does this work?

- If you have just installed the plugin, you can optimize images with one click.
- New images that will be added to the Media Library will be converted automatically.
- Our plugin does not modify your original images in any way. This means security for you and your files.
- When the browser loads an image, our plugin checks if it supports the WebP format. If so, the image in WebP format is loaded.
- The plugin does not make redirects in default mode, so the URL is always the same. Only the MIME type of the image changes to `image/webp`.
- No redirects means no cache issues, faster and trouble-free operation of your website. If you want to know more about how it works, check out [the plugin FAQ](#faq) below.
- It does not matter if the image display as an `img` HTML tag or you use `background-image`. It works always!
- In case rewriting by rules from .htaccess file is blocked, a mode is available which loads images via PHP file. Then image URLs are changed, but the logic of operation is the same as in the case of the default mode.
- The final result is that your users download less than half of the data, and the website itself loads faster!
- You lose nothing - if you had to remove the plugin, it will remove everything after itself. It does not leave any trace, so you can check it with ease.

#### Convert WebP - it is the future!

Optimize images and raise your website to a new level now! Install the plugin and enjoy the website that loads faster by image optimization. Surely you and your users will appreciate it.

#### Support for additional directories

You can convert WebP and optimize images not only from `/uploads` directory but also from `/plugins` and `/themes` directories. This allows full integration with the WebP format!

#### Support to the development of plugin

We spend hours working on the development of this plugin. Technical support also requires a lot of time, but we do it because we want to offer you the best plugin. We enjoy every new plugin installation.

If you would like to appreciate it, you can [provide us a coffee](https://ko-fi.com/gbiorczyk/?utm_source=webp-converter-for-media&utm_medium=readme-content). **If every user bought at least one, we could work on the plugin 24 hours a day!**

#### Please also read the FAQ below. Thank you for being with us!

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/webp-converter-for-media` directory, or install plugin through the WordPress plugins screen directly.
2. Activate plugin through `Plugins` screen in WordPress Admin Panel.
3. Use `Settings -> Settings -> WebP Converter` screen to configure the plugin.
4. Click on the button `Regenerate All`.
5. Check if everything works fine.

That's all! Your website is already loading faster!

== Frequently Asked Questions ==

= How to get technical support? (before you ask for help) =

Please always adding your thread, **read all other questions in the FAQ of plugin and other threads in support forum first**. Perhaps someone had a similar problem and it has been resolved.

When adding a thread, follow these steps and reply to each of them:

**1.** Do you have any error on the plugin settings page? Please read [this thread](https://wordpress.org/support/topic/server-configuration-error-what-to-do/) if you have any errors.

**2.** URL of your website.

**3.** Configuration of your server *(link to it can be found on the settings page of plugin in the section **"We are waiting for your message"**)* - please take a screenshot of the ENTIRE page and send it to me.

**4.** Settings of plugin - please take a screenshot of the ENTIRE page and send it to me.

**5.** Please do the test, which is described in the FAQ in question `How to check if plugin works?`. Please send a screenshot of Devtools with test results.

**6.** Enable [debugging to the file](https://wordpress.org/support/article/debugging-in-wordpress/#wp_debug_log) and check if any errors are generated in the debug.log file when the works. Provide their content.

**7.** Do you use any plugin filters or actions from this FAQ? If so, list them all.

**8.** What plugin version are you using? If it is not the latest then update and check everything again.

**9.** A list of all the plugins you use. Have you tried checking the plugin operation by turning off all others and activating the default theme? If not, please try whenever possible. **This is very important because other plugins or themes can cause problems.** Therefore, we recommend disabling all necessary plugins and enabling the default theme.

Please remember to include the answers for all questions by adding a thread. It is much easier and accelerate the solution of your problem.

= Error on plugin settings screen? =

If you have an error on the plugin settings screen, first of all please read it carefully. They are displayed when there is a problem with the configuration of your server or website.

The messages are designed to reduce the number of support requests that are repeated. It saves your and our time. Please read [this thread](https://wordpress.org/support/topic/server-configuration-error-what-to-do/) for more information.

= Error while converting? =

You can get several types of errors when converting. First of all, carefully read their content. For the most part, you can solve this problem yourself. Try to do this or contact the server administrator.

If you get an error: `File "%s" does not exist. Please check file path.` means that the [file_exists()](https://www.php.net/manual/en/function.file-exists.php) function in PHP returned `false` using the file path given in the error message. Check this path and make sure it is correct.

If you get an error: `File "%s" is unreadable. Please check file permissions.` means that the [is_readable()](https://www.php.net/manual/en/function.is-readable.php) function in PHP returned `false` using the file path given in the error message. Check the permissions for the file and the directory in which the file is located.

If you get an error: `"%s" is not a valid image file.` means that the file is damaged in some way. Download the file to disk, save it again using any graphics program and add again to the page. If the error applies to individual images then you can ignore it - just the original images will load, not WebP.

If you get an error: `"%s" converted to .webp is larger than original and has been deleted.` means the original image weighed less than WebP. This happens when images have been compressed before. Disable the *"Automatic removal of files in output formats larger than original"* option in plugin settings to force always using WebP.

In the case of the above problems, **contacting the support forum will be useless**. Unfortunately, we are unable to help you if your files are damaged. You have to fix it yourself. If you have previously used other tools that changed the original files and damaged them, you will do nothing more.

Remember that it happens that other plugins can cause problems with accessing files or the REST API. Please try to disable all other plugins and set the default theme to make sure that it is not one of them that causes these types of problems.

= What are requirements of plugin? =

Practically every hosting meets these requirements. You must use PHP at least 7.0 and have the `GD` or `Imagick` extension installed. **The extension must support `WebP format`.** If you have an error saying that the GD or Imagick library are not installed, but you have it installed then they are probably incorrectly configured and do not have WebP support.

They are required native PHP extensions, used among others by WordPress to generate thumbnails. Your server must also have the modules `mod_mime`, `mod_rewrite` and `mod_expires` enabled.

An example of the correct server configuration can be found [here](https://gbiorczyk.pl/webp-converter/serverinfo.png). Link to your current configuration can be found in the administration panel, on the management plugin page in the section **"We are waiting for your message"** *(or using the URL path: `/wp-admin/options-general.php?page=webpc_admin_page&action=server`)*.

**Note the items marked in red.** If the values marked in red do not appear in your case, it means that your server does not meet the technical requirements. Pay attention to the **WebP Support** value for the GD library and **WEBP in the list of supported extensions** for the Imagick library.

In a situation where your server does not meet the technical requirements, please contact your server Administrator. We are not able to help you. Please do not contact us about this matter, because this is a server configuration problem, not a plugin.

Also REST API must be enabled and work without additional restrictions. If you have a problem with it, please contact the Developer who created your website. He should easily find the issue with the REST API not working.

= How to check if plugin works? =

When you have installed plugin and converted all images, follow these steps:

1. Run `Google Chrome` and enable `Dev Tools` *(F12)*.
2. Go to the `Network` tab and select filtering for `Img` *(Images)*.
3. Refresh your website page.
4. Check list of loaded images. Note `Type` column.
5. If value of `webp` is there, then everything works fine.
6. Remember that this plugin does not change URLs. This means that e.g. link will have path to .jpg file, but `.jpg.webp file will be loaded instead of original .jpg`.
7. In addition, you can check weight of website before and after using plugin. The difference will be huge!
8. More information: [here](https://gbiorczyk.pl/webp-converter/check-devtools.png)

Please remember that in default loading mode *(via .htaccess)* URLs will remain unchanged. When you open the image in a new tab or look at its URL, you'll see the original URL.

WebP is only used when loading a image on a website. In default loading mode *(via .htaccess)* it is done by the rules from the .htaccess file, on the server side, without the visible URL change to the image. Yes, it can be called magic :)

That is why the plugin should be tested in Dev Tools. If the Type of file is `WebP`, then everything is working properly. You can also turn off the plugin for a moment and check the weight of your website, then turn it on and test again. The difference should be visible.

The operation of the plugin for non-advanced users may sometimes be less understood, but everything is fine. Thanks to this, regardless of whether your browser supports WebP or not, everything works without problems.

Only images from the `/uploads` directory are automatically converted. If you use other plugins that also save images in the `/uploads` directory then this may not work. Therefore, check the plugin settings and try converting all images again.

= Why are some images not in WebP? =

If the converted image in WebP format is larger than the original, the browser will use the original file. This converted file will be deleted. Therefore, you can also see files other than WebP on the list. When this happens, you will receive information in debug.log.

When such a situation occurs, a file in `.webp.deleted` format will be created. This avoids re-converting images that were larger than original after converting to WebP. If the option of forced conversion of all images is checked, this image will also be re-converted.

If you want to force the use of WebP files, uncheck the `Automatic removal of files in output formats larger than original` option in the plugin settings. Then click on the `Regenerate All` button to convert all images again.

Remember that this plugin supports images from the `/wp-content` directory, e.g. files downloaded from the Media Library. Redirections will not work if your images are downloaded from another domain, i.e. from an external service.

When checking the operation of the plugin, e.g. in Dev Tools, pay attention to the path from which the files are downloaded and which directories you have enabled in the settings of plugin.

= How to change path to uploads? =

This is possible using the following types of filters to change default paths. It is a solution for advanced users. If you are not, please skip this question.

Path to the root installation directory of WordPress *(`ABSPATH` by default)*:

`add_filter( 'webpc_site_root', function( $path ) {
	return ABSPATH;
} );`

Path to `/uploads` directory *(relative to the root directory)*:

`add_filter( 'webpc_dir_name', function( $path, $directory ) {
	if ( $directory !== 'uploads' ) {
		return $path;
	}
	return 'wp-content/uploads';
}, 10, 2 );`

Directory path with converted WebP files *(relative to the root directory)*:

`add_filter( 'webpc_dir_name', function( $path, $directory ) {
	if ( $directory !== 'webp' ) {
		return $path;
	}
	return 'wp-content/uploads-webpc';
}, 10, 2 );`

**Note that the `/uploads-webpc` directory must be at the same nesting level as the `/uploads`, `/plugins` and `/themes` directories.**

Prefix in URL of `/wp-content/` directory or equivalent *(used in .htaccess)*:

`add_filter( 'webpc_uploads_prefix', function( $prefix ) {
	return '/';
} );`

For the following sample custom WordPress structure:

`...
├── web
	...
	├── app
	│	├── mu-plugins
	│	├── plugins
	│	├── themes
	│	└── uploads
	├── wp-config.php
	...`

Use the following filters:

`add_filter( 'webpc_site_root', function( $path ) {
	return 'C:/WAMP/www/project/webp'; // your valid path to root
} );
add_filter( 'webpc_dir_name', function( $path, $directory ) {
	if ( $directory !== 'uploads' ) {
		return $path;
	}
	return 'app/uploads';
}, 10, 2 );
add_filter( 'webpc_dir_name', function( $path, $directory ) {
	if ( $directory !== 'webp' ) {
		return $path;
	}
	return 'app/uploads-webpc';
}, 10, 2 );`
`add_filter( 'webpc_uploads_prefix', function( $prefix ) {
	return '/';
} );`

After setting the filters go to `Settings -> WebP Converter` in the admin panel and click the `Save Changes` button. `.htaccess` files with appropriate rules should be created in the directories `/uploads` and `/uploads-webpc`.

= How to exclude paths from converting? =

To exclude selected directories, use the following filter:

`add_filter( 'webpc_supported_source_directory', function( bool $status, string $directory_name, string $server_path ): bool {
    $excluded_directories = [ 'my-directory' ];
    if ( ! $status || in_array( $directory_name, $excluded_directories ) ) {
        return false;
    }

    return $status;
}, 10, 3 );`

To exclude selected files use the following filter:

`add_filter( 'webpc_supported_source_file', function( bool $status, string $file_name, string $server_path ): bool {
    $excluded_files = [ 'my-image.jpg' ];
    if ( ! $status || in_array( $file_name, $excluded_files ) ) {
        return false;
    }

    return $status;
}, 10, 3 );`

Argument `$server_path` is the absolute server path to a directory or file. Inside the filters, you can apply more complicated rules as needed.

Filters run before images are converted - they no longer support converted images. You have to delete them manually if they should not be converted.

= How to run manually conversion? =

By default, all images are converted when you click on the `Regenerate All` button. In addition, conversion is automatic when you add new files to your Media Library.

Remember that our plugin takes into account images generated by WordPress. There are many plugins that generate, for example, images of a different size or in a different version.

If you would like to integrate with your plugin, which generates images by yourself, you can do it. Our plugin provides the possibility of this type of integration. This works for all images in the `/wp-content` directory.

It is a solution for advanced users. If you would like to integrate with another plugin, it's best to contact the author of that plugin and give him information about the actions available in our plugin. This will help you find a solution faster.

You can automatically run the option to regenerate all new images. This is useful when you use external plugins that generate images themselves. To do this, use the following code:

`do_action( 'webpc_regenerate_all', $paths );`

Below is an example of how to use this action to automatically regenerate images after changing the theme:

`add_action('init', function() {
	do_action( 'webpc_regenerate_all' );
});`

To manually start converting selected files, you can use the action to which you will pass an array with a list of paths *(they must be absolute server paths)*:

`do_action( 'webpc_convert_paths', $paths );`

An alternative method is to manually start converting the selected attachment by passing the post ID from the Media Library. Remember to run this action after registering all image sizes *(i.e. after running the `add_image_size` function)*:

`do_action( 'webpc_convert_attachment', $post_id );`

Argument `$paths` is array of absolute server paths and `$skip_exists` means whether to skip converted images.

You can also modify the list of image paths for an attachment, e.g. to exclude one image size. To do this, use the following filter:

`add_filter( 'webpc_attachment_paths', function( $paths, $attachment_id ) {
	return $paths;
}, 10, 2 );`

Argument `$paths` is array of absolute server paths and `$attachment_id` is the post ID of attachment, added to the Media Library.

To delete manually converted files, use the following action, providing as an argument an array of absolute server paths to the files *(this will delete manually converted files)*:

`do_action( 'webpc_delete_paths', $paths );`

= How to change .htaccess rules? =

Manually editing the rules in the .htaccess file is a task only for experienced developers. Remember that the wrong rules can cause your website to stop working.

Below is a list of filters that allow you to modify all rules. Remember that it's best to use your own rule set rather than edit parts of exists. This will ensure greater failure-free after plugin update.

Returning an empty string will delete these rules the next time you save the plugin settings. You must do this after each filter edit.

Rules for redirects: *(returns rules for `mod_rewrite` module)*:

`add_filter( 'webpc_htaccess_mod_rewrite', function( $rules, $path ) {
	return '';
}, 10, 2 );`

Argument `$path` is absolute server path for `.htaccess` file *(`/wp-content/.htaccess` or `/wp-content/uploads/.htaccess`)*.

Rules for `image/webp` MIME type: *(returns rules for `mod_mime` module)*:

`add_filter( 'webpc_htaccess_mod_mime', function( $rules ) {
	return '';
} );`

Rules for Browser Caching: *(returns rules for `mod_expires` module)*:

`add_filter( 'webpc_htaccess_mod_expires', function( $rules ) {
	return '';
} );`

All rules from the files `/wp-content/.htaccess`, `/wp-content/uploads/.htaccess` and `/wp-content/uploads-webpc/.htaccess`: *(returns rules for modules: `mod_rewrite`, `mod_mime` and `mod_expires`)*:

`add_filter( 'webpc_htaccess_rules', function( $rules, $path ) {
	return '';
}, 10, 2 );`

Argument `$path` is absolute server path for `.htaccess` file.

= Does plugin support CDN? =

Unfortunately not. This is due to the logic of the plugin's operation. Plugins that enable integration with the CDN servers modify the HTML of the website, changing URLs for media files. This plugin does not modify URLs. Replacing URLs in the HTML code is not an optimal solution.

The main problem when changing URLs is cache. When we modify the image URL for WebP supporting browser, then use the browser without WebP support, it will still have the URL address of an image in .webp format, because it will be in the cache.

While in the case of the `img` tag you can solve this problem, in the case of `background-image` it is possible. We wanted full support so that all images would be supported - no matter how they are displayed on the website.

Therefore in this plugin for browsers supporting the WebP format, only the source of the file is replaced by using the `mod_rewrite` module on the server. The URL for image remains the same. This solves the whole problem, but it is impossible to do when the files are stored on the CDN server.

If you are using a CDN server, find one that automatically converts images to WebP format and properly sends the correct image format to the browser.

= Configuration for Apache =

In the case of Apache, when saving the settings, .htaccess files will be generated automatically in the following locations:
- `/wp-content/.htaccess`
- `/wp-content/uploads/.htaccess`
- `/wp-content/uploads-webpc/.htaccess`

If these files are missing or empty, try disabling and re-enabling the plugin or saving the plugin settings again. Also check the write permissions of the directories where these files are located.

If your server is a combination of Apache and Nginx, remember that the image files must be supported by Apache. Only then will the redirections in the .htaccess file work properly. Alternatively, you can use the configuration for Nginx.

= Configuration for Nginx =

This configuration is only required for the image loading mode set to via .htaccess in the plugin settings.

Please edit the configuration file:
- `/etc/nginx/mime.types`

and add this code line:

`types {`
`	# ...`
`	image/webp webp;`
`	image/avif avif;`
`}`

Then find the configuration file in one of the paths *(remember to select configuration file used by your vhost)*:
- `/etc/nginx/sites-enabled/`
- `/etc/nginx/conf.d/`

and add below code in this file *(add these lines to very beginning of file if possible)*:

`map $http_accept $load_avif {`
`	~image/avif "/wp-content/uploads-webpc/$path.$ext.avif";`
`}`
`map $http_accept $load_webp {`
`	~image/webp "/wp-content/uploads-webpc/$path.$ext.webp";`
`}`
``
`server {`
`	location ~ /wp-content/(?<path>.+)\.(?<ext>jpe?g|png|gif)$ {`
`		add_header Vary Accept;`
`		add_header Cache-Control "private" always;`
`		expires 365d;`
`		try_files $load_avif $load_webp $uri =404;`
`	}`
`	# ...`
`}`

After making changes, remember to restart the machine: `systemctl restart nginx`.

= Configuration for Multisite Network =

Multisite Network mode works fine but requires adding configuration manually.

Please manually paste the following code **at the beginning of .htaccess file** in the `/wp-content` directory:

`# BEGIN WebP Converter`
`# ! --- DO NOT EDIT PREVIOUS LINE --- !`
`<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteCond %{HTTP_ACCEPT} image/avif
	RewriteCond %{DOCUMENT_ROOT}/wp-content/uploads-webpc/$1.jpg.avif -f
	RewriteRule (.+)\.jpg$ /wp-content/uploads-webpc/$1.jpg.avif [NC,T=image/avif,L]
	RewriteCond %{HTTP_ACCEPT} image/avif
	RewriteCond %{DOCUMENT_ROOT}/wp-content/uploads-webpc/$1.jpeg.avif -f
	RewriteRule (.+)\.jpeg$ /wp-content/uploads-webpc/$1.jpeg.avif [NC,T=image/avif,L]
	RewriteCond %{HTTP_ACCEPT} image/avif
	RewriteCond %{DOCUMENT_ROOT}/wp-content/uploads-webpc/$1.png.avif -f
	RewriteRule (.+)\.png$ /wp-content/uploads-webpc/$1.png.avif [NC,T=image/avif,L]
</IfModule>
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteCond %{HTTP_ACCEPT} image/webp
	RewriteCond %{DOCUMENT_ROOT}/wp-content/uploads-webpc/$1.jpg.webp -f
	RewriteRule (.+)\.jpg$ /wp-content/uploads-webpc/$1.jpg.webp [NC,T=image/webp,L]
	RewriteCond %{HTTP_ACCEPT} image/webp
	RewriteCond %{DOCUMENT_ROOT}/wp-content/uploads-webpc/$1.jpeg.webp -f
	RewriteRule (.+)\.jpeg$ /wp-content/uploads-webpc/$1.jpeg.webp [NC,T=image/webp,L]
	RewriteCond %{HTTP_ACCEPT} image/webp
	RewriteCond %{DOCUMENT_ROOT}/wp-content/uploads-webpc/$1.png.webp -f
	RewriteRule (.+)\.png$ /wp-content/uploads-webpc/$1.png.webp [NC,T=image/webp,L]
</IfModule>
<IfModule mod_headers.c>
  Header Set Cache-Control "private"
</IfModule>`
`# ! --- DO NOT EDIT NEXT LINE --- !`
`# END WebP Converter`

And the following code **at the beginning of .htaccess file** in the `/wp-content/uploads-webpc` directory:

`# BEGIN WebP Converter`
`# ! --- DO NOT EDIT PREVIOUS LINE --- !`
`<IfModule mod_mime.c>
	AddType image/webp .webp
	AddType image/avif .avif
</IfModule>
<IfModule mod_expires.c>
	ExpiresActive On
	ExpiresByType image/webp "access plus 1 year"
	ExpiresByType image/avif "access plus 1 year"
</IfModule>
<IfModule mod_headers.c>
  Header Set Cache-Control "private"
</IfModule>`
`# ! --- DO NOT EDIT NEXT LINE --- !`
`# END WebP Converter`

= Is the plugin completely free? =

The plugin is free and you can use it without restrictions. We also offer a paid version that allows for additional functionalities

However, working on plugins and technical support requires many hours of work. If you are using the free version of the plugin and if you want to appreciate us, you can [provide us a coffee](https://ko-fi.com/gbiorczyk/?utm_source=webp-converter-for-media&utm_medium=readme-faq). Thanks everyone!

Thank you for all the ratings and reviews.

If you are satisfied with this plugin, please recommend it to your friends. Every new person using our plugin is valuable to us.

This is all very important to us and allows us to do even better things for you!

== Screenshots ==

1. How to start using plugin few moments?
2. Screenshot of the options panel
3. Screenshot when regenerating images

== Changelog ==

= 4.0.2 (2021-12-17) =
* `[Fixed]` Fetching large list of files to conversion
* `[Fixed]` Rewrites caching for some servers
* `[Changed]` Connection when converting using remote server

= 4.0.1 (2021-12-10) =
* `[Added]` Informational banners on plugin settings page

= 4.0.0 (2021-12-04) =
* `[Added]` Converting images using remote server
* `[Added]` Converting images to AVIF format
* `[Added]` Error detection for invalid permalinks structure

See [changelog.txt](https://plugins.svn.wordpress.org/webp-converter-for-media/trunk/changelog.txt) for previous versions.

== Upgrade Notice ==

None.
