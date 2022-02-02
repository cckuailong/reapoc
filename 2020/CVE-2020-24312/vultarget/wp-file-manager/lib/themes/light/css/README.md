# Stylesheets
All CSS for your theme will be located here.

The `theme.css` file is the focal point for loading the styles. These could all have been in one file, but have been split up for the sake of more easily structuring and maintaining the codebase.

* **reset.css** : resets background and border of all elfinder elements so that you can skin from scratch without manually positioning the main elements yourself
* **main.css** : main UI elements (wrapper for the main elfinder div, global styles, etc..)
* **icons.css** : icons across the UI (e.g. file associations)
* **toolbar.css** : toolbar at the top of the elfinder container. Contains toolbar buttons and searchbar
* **navbar.css** : directory navigation on the left-hand panel
* **view-list.css** : defines the list view
* **view-thumbnail.css** : defines the thumbnail/tile view
* **contextmenu.css** : context menu shown when right-clicking on in the list/thumbnail view or navbar
* **dialog.css** : information dialogs/modal windows
* **statusbar.css** : footer; contains information about directory and currently selected files

Note that many of the styles have a large degree of selectivity. E.g:

```css
.elfinder .elfinder-navbar .elfinder-navbar-dir.ui-state-active:hover { /* */ }
```

This is to minimize the need for using `!important` flags to override the existing styles (particularly with respect to jQuery UI's CSS).

## Tips
* Use the `reset.css` style to reset the styles that you need to. Comment out selectors that you wish to remain untouched.
* If you need to reset a style outside of `reset.css`, the following normally suffices:

    ```css
      background: none;
      border: none;
    ```
* If you want to change the icons in a particular container, it is best to reset the icon's style from a general selector, then style each individual icon separately. For example:

    ```css
    /* All toolbar icons */
    .elfinder .elfinder-toolbar .elfinder-buttonset .elfinder-button-icon {
      /* reset the style and set  properties common to all toolbar icons */
    }

    /* mkfile toolbar icon */
    .elfinder .elfinder-toolbar .elfinder-buttonset .elfinder-button-icon-mkfile {
      /* styles specific to the mkfile button (e.g. background-position) */
    }
    ```
* Some styles have their `text-indent` property set to `-9999px` to keep the text out of view. If after styling you can't see the text (and you need to), change the `text-indent` property
