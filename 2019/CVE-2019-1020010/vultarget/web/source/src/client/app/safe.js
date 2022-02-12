/**
 * ブラウザの検証
 */

// Detect an old browser
if (!('fetch' in window)) {
	alert(
		'Your browser (or your OS) seems outdated. ');
}
