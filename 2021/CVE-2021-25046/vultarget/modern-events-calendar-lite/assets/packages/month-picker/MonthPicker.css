/*
The jQuery UI Month Picker Version 3.0.4
https://github.com/KidSysco/jquery-ui-month-picker/

Copyright (C) 2007 Free Software Foundation, Inc. <http://fsf.org/>
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see
<http://www.gnu.org/licenses/gpl-3.0.txt>.
*/

.month-picker {
    display: inline-block;
    position: absolute;
    z-index: 9999;
}

.month-picker table {
  border-collapse: separate;
  border-spacing: 2px 2px;
}

.month-picker td {
    padding: 0px;
}

/*
Prevents the button labels from maving sligtly to the left
when applying the width CSS property to the buttons.
See: .month-picker-month-table button { width: 4.3em; }
*/
.month-picker .ui-button-text {
  padding: .4em 0;
}

.month-picker-header {
    margin: 3px 3px 0px 3px;
}

.month-picker-year-table {
    width: 100%;
    /*
    Makes sure the next/previous/jump years buttons are not unnecessarily
    selected if the user clicks them a couple of times fast.
    */
    -ms-user-select: none; /* IE 10+ */
    -moz-user-select: -moz-none;
    -khtml-user-select: none;
    -webkit-user-select: none;
    user-select: none;
}

/*
The plugin uses buttons with a transparent background in the year-table
(aka header) in order to look consistent with jQuery UI datepicker and to
make the year title a button that blends into the heading in the default state.

The plugin does this by removing the .ui-state-default class from (in MonthPicker.js)
the a tags (buttons) which also ends up removing the 1px border that it applies.

To prevent the button from resizing and moving everything around when you hover
in and out, we use a carefully constructed selector, which gets overroden by the
more specific .ui-state-hover/actove class selectors in the jquery-ui.css
that apply the visible borders that we want.

This selector applies a 1px transparent border that keeps the button
in the same size, but it doesen't hide the borders that .ui-state-hover/actove give us.
*/
.month-picker-year-table a {
    border: 1px solid transparent;
}

/*
Sets the size of the next/previous buttons,
and makes the buttons in the heading (year-table) sligtly bigger,
and removes the pointer cursor from the buttons in the heading (year-table).
*/
.month-picker-year-table .ui-button {
    font-size: 1.1em;
    width: 1.5em;
    height: 1.5em;
    cursor: default;
    margin: 0;
}

.month-picker-year-table .month-picker-title {
    text-align: center;
}

.month-picker-year-table .month-picker-title .ui-button {
    font-size: 1em;
    padding: .1em 0;
    width: 100%;
    font-weight: bold;
}

/*
The buttons in the heading (year-table) are slightly shrinked, but because jQuery ui and
the .month-picker .ui-button-text rule at the top of this CSS file apply some
padding which results in the button text being moved to the bottom of
the button.

This rule removes the unnecessary padding so the text in
the jump years button will be vericaly centred.
*/
.month-picker-year-table .ui-button-text {
    padding: 0;
}

.month-picker-month-table td {
    height: 35px;
    text-align: center;
}

/*
Makes sure the buttons stay in the same size when swithching
to the Jump years menu.
this also ensures that the entire menu dosen't resize itself
in response to the slightly bigger buttons in the Jump years menu.
 */
.month-picker-month-table .ui-button {
    width: 4.2em;
    margin: .2em;
}

.month-picker-open-button {
    height: 20px;
    width: 20px;
    vertical-align: bottom;
}

.month-picker-invalid-message {
    display: none;
    background-color: Yellow;
}

.month-picker-disabled {
    background-color: #e1e1e1;
}
