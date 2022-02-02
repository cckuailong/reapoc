<?php defined('ABSPATH') || defined('DUPXABSPATH') || exit; ?>
<style>
    body {font-family:Verdana,Arial,sans-serif; font-size:13px}
    fieldset {border:1px solid silver; border-radius:5px; padding:10px}
    h3 {margin:1px; padding:1px; font-size:13px;}
    a {color:#222}
    a:hover{color:gray}

    input:not([type=checkbox]):not([type=radio]):not([type=button]) , select {
        width: 100%;
        border-radius: 2px;
        border: 1px solid silver;
        padding: 4px;
        padding-left: 4px;
        font-family: Verdana,Arial,sans-serif;
        line-height: 20px;
        height: 30px;
        box-sizing: border-box;
        background-color: white;
        color: black;
        border-radius: 4px;
    }

    input[readonly]:not([type="checkbox"]):not([type="radio"]):not([type="button"]),
    select[readonly],
    select[readonly] option{
        background-color: darkgray;
        color: lightgray;
        cursor: not-allowed;
    }

    select[size] {
        height: auto;
        line-height: 25px;
    }

    select , option {
        color: black;
    }
    select {
        padding-left:0;
    }
    select option {
        padding: 2px 5px;
    }
    select option:disabled {
        text-decoration: line-through;
        cursor: not-allowed;
        color: #A9A9A9;
    }

    select:disabled {background:#EBEBE4}
    input.readonly {background-color:#efefef;}
    .no-display { display: none; }

	/* ============================
	COMMON VIEWS
     ============================ */
	div#content {border:1px solid #CDCDCD; width:850px; min-height:550px; margin:auto; margin-top:18px; border-radius:5px; box-shadow:0 8px 6px -6px #333; font-size:13px}
	div#content-inner {padding:10px 25px; min-height:550px}
	form.content-form {min-height:550px; position:relative; line-height:17px}
	div.logfile-link {float:right; font-weight:normal; font-size:11px; font-style:italic}
	span.sub-notes {font-size:10px;}

	/* Header */
	table.header-wizard {border-top-left-radius:5px; border-top-right-radius:5px; width:100%; box-shadow:0 5px 3px -3px #999; background-color:#F1F1F1; font-weight:bold}
	div.dupx-logfile-link {float:right; font-weight:normal; font-style:italic; font-size:11px; cursor:pointer}
	.wiz-dupx-version {white-space:nowrap; color:#777; font-size:11px; font-style:italic; text-align:right;  padding:3px 15px 5px 0; line-height:14px; font-weight:normal}
	.wiz-dupx-version a { color:#777; }
	div.dupx-debug-hdr {padding:5px 0 5px 0; font-size:16px; font-weight:bold}
	div.dupx-branding-header {font-size:26px; padding: 10px 0 7px 15px;}

	.dupx-overwrite {color:#AF0000;}
	.dupx-pass {display:inline-block; color:green;}
	.dupx-fail {display:inline-block; color:#AF0000;}
	.dupx-warn {display:inline-block; color:#555;}
	.dupx-notice {display:inline-block; color:#000;}
	i[data-tooltip].fa-question-circle {cursor: pointer; color:#C3C3C3}
	div.status-badge-pass {background-color:#418446;}
	div.status-badge-fail {background-color:maroon;}
	div.status-badge-warn {background-color:#555;}
	div.status-badge-pass, div.status-badge-fail, div.status-badge-warn {float:right; border-radius:4px; color:#fff; padding:0 3px 0 3px;  font-size:11px !important; min-width:30px; text-align:center; font-weight:normal;}
    button.default-btn, .default-btn, .default-btn:hover {
		cursor:pointer; color:#fff; font-size:16px; border-radius:5px;	padding:7px 25px 5px 25px;
	    background-color:#13659C; border:1px solid gray; text-decoration: none;
	}
	button.disabled, input.disabled {background-color:#F4F4F4; color:silver; border:1px solid silver;}

	div.log-ui-error {padding-top:2px; font-size:13px}
	div#progress-area {padding:5px; margin:150px 0 0 0; text-align:center;}
	div#ajaxerr-data {padding:6px; height:425px; width:99%; border:1px solid silver; border-radius:5px; background-color:#F1F1F1; font-size:13px; overflow-y:scroll; line-height:20px}
	div.hdr-main {font-size:22px; padding:0 0 5px 0; border-bottom:1px solid #D3D3D3; font-weight:bold; margin:15px 0 20px 0;}
	div.hdr-main span.step {color:#DB4B38}

	div.sub-header {font-size:11px; font-style:italic; font-weight:normal; margin:5px 0 -1px 0}
	div.hdr-sub1 {font-size:18px; margin-bottom:5px;border:1px solid #D3D3D3;padding:7px; background-color:#f9f9f9; font-weight:bold; border-radius:4px}
	div.hdr-sub1 a {cursor:pointer; text-decoration: none !important}
	div.hdr-sub1 i.fa {font-size:15px; display:inline-block; margin:2px 5px 0 0; vertical-align:top}
	div.hdr-sub1-area {padding:5px}
	div.hdr-sub2 {font-size:15px; padding:2px 2px 2px 0; font-weight:bold; margin-bottom:5px; border:none}
	div.hdr-sub3 {font-size:15px; padding:2px 2px 2px 0; border-bottom:1px solid #D3D3D3; font-weight:bold; margin-bottom:5px;}
	div.hdr-sub4 {font-size:15px; padding:7px; border:1px solid #D3D3D3;; font-weight:bold; background-color:#e9e9e9;}
	div.hdr-sub4:hover  {background-color:#dfdfdf; cursor:pointer}
	div.toggle-hdr:hover {cursor:pointer; background-color:#f1f1f1; border:1px solid #dcdcdc; }
	div.toggle-hdr:hover a{color:#000}

     [data-type="toggle"] > i.fa,
    i.fa.fa-toggle-empty { min-width: 8px; }

    /* ============================
    NOTICES
    ============================ */
    /* step messages */
    #step-messages { padding: 10px 25px 0; }
    .notice {
        background: #fff;
        border:1px solid #dfdfdf;
        border-left: 4px solid #fff;
        margin: 4px;
        padding: 5px;
        border-radius: 4px;
        font-size: 12px;
    }

    .notice-report {
        border-left: 4px solid #fff;
        padding-left: 0;
        padding-right: 0;
        margin-bottom: 4px;
    }

    .next-step .title-separator {
        margin-top: 5px;
        padding-top: 5px;
        border-top: 1px solid lightgray;
    }

    .notice .info pre {
        margin: 0;
        padding: 0;
        overflow: auto;
    }

    .notice-report .title {
        padding: 0 10px;
    }

    .notice-report .info {
        border-top: 1px solid #dedede;
        padding: 10px;
        font-size: 10px;
        background: #FAFAFA;
		word-wrap: break-word;
    }

    .notice.l-info,
    .notice.l-notice {border-left-color: #197b19;}
    .notice.l-swarning {border-left-color: #636363;}
    .notice.l-hwarning {border-left-color: #636363;}
    .notice.l-critical {border-left-color: maroon;}
    .notice.l-fatal {border-left-color: #000000;}

    .report-sections-list .section {
        border: 1px solid #DFDFDF;
        margin-bottom: 25px;
        box-shadow: 4px 8px 11px -8px rgba(0,0,0,0.41);
    }

    .report-sections-list .section > .section-title {
        background-color: #efefef;
        padding: 3px;
        font-weight: bold;
        text-align: center;
        font-size: 14px;
    }

    .report-sections-list .section > .section-content {
        padding: 5px;
    }

    .notice-level-status {
        border-radius: 4px;
        padding: 2px;
        margin: 1px;
        font-size: 10px;
        display: inline-block;
        color: #FFF;
        font-weight: bold;
        min-width:55px;
    }

    .notice-level-status.l-info,
    .notice-level-status.l-notice {background: #197b19;}
    .notice-level-status.l-swarning {background: #636363;}
    .notice-level-status.l-hwarning {background: #636363;}
    .notice-level-status.l-critical {background: maroon;}
    .notice-level-status.l-fatal {background: #000000;}

    /*Adv Opts */
    table.dupx-opts {width:100%; border:0px;}
	table.dupx-opts td{padding:3px;}
	table.dupx-opts td:first-child{width:125px; font-weight: bold}
	table.dupx-advopts td:first-child{width:125px;}
	table.dupx-advopts label.radio {width:50px; display:inline-block}
	table.dupx-advopts label {white-space:nowrap; cursor:pointer}
	table.dupx-advopts-space {line-height:24px}

	div.error-pane {border:1px solid #efefef; border-left:4px solid #D54E21; padding:0 0 0 10px; margin:2px 0 10px 0}
	div.dupx-ui-error {padding-top:2px; font-size:13px; line-height: 20px}

	div.footer-buttons {position:absolute; bottom:10px; padding:10px;  right:0; width:100%; text-align:right}
	div.footer-buttons  input:hover, button:hover {border:1px solid #000}
	div.footer-buttons input[disabled=disabled], button[disabled=disabled]{background-color:#F4F4F4; color:silver; border:1px solid silver;}
	form#form-debug {display:block; margin:10px auto; width:750px;}
	form#form-debug a {display:inline-block;}
	form#form-debug pre {margin-top:-2px; display:none}

    /*Dialog Info */
	div.dlg-serv-info {line-height:22px; font-size:12px}
	div.dlg-serv-info label {display:inline-block; width:200px; font-weight: bold}
    div.dlg-serv-info div.hdr {font-weight: bold; margin-top:5px; padding:2px 5px 2px 0; border-bottom: 1px solid #777; font-size:14px}
	div.dupx-modes {color:#999; font-weight:normal; font-style:italic; font-size:11px; padding:5px 10px 0 0; text-align:right}

	/* ============================
	INIT 1:SECURE PASSWORD
	============================ */
    button.pass-toggle {height:26px; width:26px; position:absolute; top:0px; right:0px; border:1px solid silver;  border-radius:0 4px 4px 0;padding:2px 0 0 3px;}
    button.pass-toggle  i { padding:0; display:block; margin:-4px 0 0 -5px}
    div.i1-pass-area {
        width:100%;
        text-align:center;
        max-width: 300px;
        margin: auto;
        position: relative;
    }
    div.i1-pass-data table {width:100%; border-collapse:collapse; padding:0}
    div.i1-pass-data label {
        display: block;
        margin-bottom: 10px;
        font-weight:bold;
    }
    div.i1-pass-errmsg {color:maroon; font-weight:bold}
    div#i1-pass-input {position:relative; margin:2px 0 15px 0}
    input#secure-pass {border-radius:4px 0 0 4px; width:250px}


	/* ============================
	STEP 1 VIEW
	 ============================ */
	div#s1-area-setup-type label {cursor:pointer}
	div.s1-setup-type-sub {padding:5px 0 0 25px; display:none}
	div#s1-area-archive-file .ui-widget.ui-widget-content {border: 0px solid #d3d3d3}
	table.s1-archive-local {width:100%}
    table.s1-archive-local td {padding:4px 4px 4px 4px}
	table.s1-archive-local td:first-child {font-weight:bold; width:55px}
	div.s1-archive-failed-msg {padding:15px; border:1px dashed maroon; font-size: 12px; border-radius:5px;}
	div.s1-err-msg {padding:0 0 80px 0; line-height:20px}
	div.s1-err-msg i {color:maroon}

	div#s1-area-sys-setup div.info-top {text-align:center; font-style:italic; font-size:11px; padding:0 5px 5px 5px}
	table.s1-checks-area {width:100%; margin:0; padding:0}
	table.s1-checks-area td.title {font-size:16px; width:100%}
	table.s1-checks-area td.title small {font-size:11px; font-weight:normal}
	table.s1-checks-area td.toggle {font-size:11px; margin-right:7px; font-weight:normal}

	div.s1-reqs {background-color:#efefef; border:1px solid silver; border-radius:4px; padding-bottom:4px}
	div.s1-reqs div.header {background-color:#E0E0E0; color:#000;  border-bottom: 1px solid silver; padding:2px; font-weight:bold }
	div.s1-reqs div.status {float:right; border-radius:4px; color:#fff; padding:0 3px 0 3px; margin:4px 5px 0 0; font-size:11px; min-width:30px; text-align:center;}
	div.s1-reqs div.pass {background-color:green;}
	div.s1-reqs div.fail {background-color:maroon;}
	div.s1-reqs div.title {padding:3px 3px 3px 5px; font-size:13px;}
	div.s1-reqs div.title:hover {background-color:#dfdfdf; cursor:pointer}
	div.s1-reqs div.info {padding:8px 8px 20px 8px; background-color:#fff; display:none; line-height:18px; font-size: 12px}
	div.s1-reqs div.info a {color:#485AA3;}
	select#archive_engine {width:90%; cursor:pointer}

	/*Terms and Notices*/
	div#s1-warning-check label{cursor:pointer;}
    div#s1-warning-msg {padding:5px;font-size:12px; color:#333; line-height:14px;font-style:italic; overflow-y:scroll; height:460px; border:1px solid #dfdfdf; background:#fff; border-radius:3px}
	div#s1-warning-check {padding:3px; font-size:14px; font-weight:normal;}
    input#accept-warnings {height: 17px; width:17px}

	/* ============================
	STEP 2 VIEW
	============================ */
	div.s2-opts label {cursor:pointer}
	textarea#debug-dbtest-json {width:98%; height:200px}
	div.php-chuncking-warning {font-style:italic; font-size:11px; color:maroon; white-space:normal; line-height:16px; padding-left:20px}

	/*Toggle Buttons */
	div.s2-btngrp {text-align:center; margin:0 auto 10px auto}
	div.s2-btngrp input[type=button] {font-size:14px; padding:6px; width:120px; border:1px solid silver;  cursor:pointer}
	div.s2-btngrp input[type=button]:first-child {border-radius:5px 0 0 5px; margin-right:-2px}
	div.s2-btngrp input[type=button]:last-child {border-radius:0 5px 5px 0; margin-left:-4px}
	div.s2-btngrp input[type=button].active {background-color:#13659C; color:#fff;}
	div.s2-btngrp input[type=button].in-active {background-color:#E4E4E4; }
	div.s2-btngrp input[type=button]:hover {border:1px solid #999}

	/*Basic DB */
	select#dbname-select {width:100%; border-radius:3px; height:20px; font-size:12px; border:1px solid silver;}
	div#s2-dbrefresh-basic {float:right; font-size:12px; display:none;  font-weight:bold; margin:5px 5px 1px 0}
	div#s2-db-basic-overwrite {border: 1px solid silver; margin:5px 0 5px 0; padding:10px; background:#f9f9f9; border-radius:5px}
	div#s2-db-basic-overwrite div.warn-text {font-size:12px; padding:5px 0 5px 0; color:maroon}
	div#s2-db-basic-overwrite div.btn-area {text-align: right; margin:5px 0}
	input.overwrite-btn {
		cursor:pointer; color:#fff; font-size:13px; border-radius:5px;	padding:5px 20px 4px 20px;
	    background-color:#989898; border:1px solid #777;
	}
	
	/*cPanel DB */    
	div.s2-cpnl-pane {margin-top:5px}
	div.s2-gopro {color: black; margin-top:10px; padding:0 20px 10px 20px; border: 1px solid silver; background-color:#F6F6F6; border-radius: 4px}
	div.s2-gopro h2 {text-align: center; margin:10px}
	div.s2-gopro small {font-style: italic}
	div.s2-cpanel-login {padding:15px; color:#fff; text-align:center; margin:15px 5px 15px 5px; border:1px solid silver; border-radius:5px; background-color:#13659C; font-size:14px; line-height:22px}
	div.s2-cpanel-off {padding:15px; color:#fff; text-align:center; margin:15px 5px 15px 5px; border:1px solid silver; border-radius:5px; background-color:#b54949; font-size:14px; line-height:22px}
    div.s2-cpnl-panel-no-support {text-align:center; font-size:18px; font-weight:bold; line-height:30px; margin-top:40px}

	/*DATABASE CHECKS */
	div.s2-dbtest-area {margin:auto; margin:5px 0 15px 0;  min-height:110px}
	div.s2-dbtest-area input[type=button] {font-size:11px; height:20px; border:1px solid gray; border-radius:3px; cursor:pointer}
	div.s2-dbtest-area small.db-check {color:#000; text-align:center; padding:3px; font-size:11px; font-weight:normal }
	div.s2-dbtest-area div.message {padding:10px 10px 10px 10px; margin:5px auto 5px auto; text-align:center; font-style:italic; font-size:15px; line-height:22px; width:100%;}
	div.s2-dbtest-area div.sub-message {padding:5px; text-align:center; font-style:italic; color:maroon}
	div.s2-dbtest-area div.error-msg {color:maroon}
	div.s2-dbtest-area div.success-msg {color:green}
	div.s2-dbtest-area pre {font-family:Verdana,Arial,sans-serif; font-size:13px; margin:0; white-space:normal;}

	div.s2-reqs-hdr {border-radius:0; border-top-right-radius:6px; border-top-left-radius:6px; border-bottom:none}
	div.s2-notices-hdr {border-radius:0; border-bottom:1px solid #D3D3D3; }
	div#s2-reqs-all {display:none}
	div#s2-notices-all {display:none}

	div.s2-reqs {background-color:#efefef; border:1px solid #D3D3D3; border-top:none}
	div.s2-reqs div.status {float:right; border-radius:4px; color:#fff; padding:0 4px 0 4px; margin:4px 7px 0 0; font-size:12px; min-width:30px; text-align:center;}
	div.s2-reqs div.title {padding:3px 8px 3px 20px; font-size:13px; background-color:#f1f1f1; border-top: 1px solid #D3D3D3;}
	div.s2-reqs div.title:hover {background-color:#dfdfdf; cursor:pointer}
	div.s2-reqs div.info {padding:4px 12px 15px 12px;; background-color:#fff; display:none; line-height:18px; font-size: 12px}
	div.s2-reqs div.info a {color:#485AA3;}
	div.s2-reqs div.info ul {padding-left:25px}
	div.s2-reqs div.info ul li {padding:2px}
	div.s2-reqs div.info ul.vids {list-style-type: none;}
	div.s2-reqs div.sub-title{border-bottom: 1px solid #d3d3d3; font-weight:bold; margin:7px 0 3px 0}

	div.s2-reqs10 table {margin-top:5px;}
	div.s2-reqs10 table td {padding:1px;}
	div.s2-reqs10 table td:first-child {font-weight:bold; padding-right:10px}
	div.s2-reqs40 div.db-list {height:70px; width:95%; overflow-y:scroll; padding:2px 5px 5px 5px; border:1px solid #d3d3d3;}
	div.s2-reqs60 div.tbl-list {padding:2px 5px 5px 5px; border:0 }
	div.s2-reqs60 div.tbl-list b {display:inline-block; width:55px; }

	div.s2-notice20 table.collation-list table {padding:2px;}
	div.s2-notice20 table.collation-list td:first-child {font-weight:bold; padding-right:5px }

	/*Warning Area and Message */
	div.s2-warning-emptydb {color:maroon; margin:2px 0 0 0; font-size:11px; display: none; white-space:normal; width: 550px}
	div.s2-warning-manualdb {color:#1B67FF; margin:2px 0 0 0; font-size:11px; display:none; white-space:normal; width: 550px}
	div.s2-warning-renamedb {color:#1B67FF; margin:2px 0 0 0; font-size:11px; display:none; white-space:normal; width: 550px}
	div#s2-tryagain {padding-top:50px; text-align:center; width:100%; font-size:16px; color:#444; font-weight:bold;}

	/* ============================
	STEP 3 VIEW
	============================ */
	table.s3-opts{width:96%; border:0;}
	table.s3-opts i.fa{font-size:16px}
	table.s3-opts td{white-space:nowrap; padding:3px;}
	table.s3-opts td:first-child{width:90px; font-weight: bold}
	div#s3-adv-opts {margin-top:5px; }
	div.s3-allnonelinks {font-size:11px; float:right;}
	div.s3-manaual-msg {font-style: italic; margin:-2px 0 5px 0}
	small.s3-warn {color:maroon; font-style:italic}

	/* ============================
	STEP 4 VIEW
	============================ */
	div.s4-final-msg {height:110px; border:1px solid #CDCDCD; padding:8px;font-size:12px; border-radius:5px;box-shadow:0 4px 2px -2px #777;}
	div.s4-final-title {color:#BE2323; font-size:18px}
	div.s4-connect {font-size:12px; text-align:center; font-style:italic; position:absolute; bottom:10px; padding:10px; width:100%; margin-top:20px}
	table.s4-report-results,
	table.s4-report-errs {border-collapse:collapse; box-shadow: 4px 8px 11px -8px rgba(0,0,0,0.41);}
	table.s4-report-errs  td {text-align:center; width:33%}
	table.s4-report-results th, table.s4-report-errs th {background-color:#d0d0d0; padding:3px; font-size:14px;}
	table.s4-report-results td, table.s4-report-errs td {padding:3px; white-space:nowrap; border:1px solid #dfdfdf; text-align:center; font-size:11px}
	table.s4-report-results td:first-child {text-align:left; font-weight:bold; padding-left:3px}
	div.s4-err-title {background-color:#dfdfdf; font-weight: bold; margin:-3px 0 15px 0; padding:5px; border-radius:3px; font-size:13px}

	div.s4-err-msg {padding:8px;  display:none; border:1px dashed #999; margin:10px 0 20px 0; border-radius:5px;}
	div.s4-err-msg div.content{padding:5px; font-size:11px; line-height:17px; max-height:125px; overflow-y:scroll; border:1px solid silver; margin:3px;  }
	div.s4-err-msg div.info-error{padding:7px; background-color:#f9c9c9; border:1px solid silver; border-radius:2px; font-size:12px; line-height:16px }
	div.s4-err-msg div.info-notice{padding:7px; background-color:#FCFEC5; border:1px solid silver; border-radius:2px; font-size:12px; line-height:16px;}
	table.s4-final-step {width:100%;}
	table.s4-final-step td {padding:5px 15px 5px 5px;font-size:13px; }
	table.s4-final-step td:first-child {white-space:nowrap; width:165px}
	div.s4-go-back {border-top:1px dotted #dfdfdf; margin:auto;font-size:11px; color:#333; padding-top:4px}
	div.s4-go-back ul {line-height:18px}
	button.s4-final-btns {cursor:pointer; color:#fff; font-size:16px; border-radius:5px; padding:7px; background-color:#13659C; border:1px solid gray; width:145px;}
	button.s4-final-btns:hover {background-color: #dfdfdf;}
    div.s4-gopro-btn {text-align:center; font-size:14px; margin:auto; width:200px; font-style: italic; font-weight:bold}
	div.s4-gopro-btn a{color:green}
	div.s4-warn {color:maroon;}
	pre.s4-diff-viewer {line-height:11px}
	div#s4-notice-reports div.section-content div.title {cursor:pointer}

    /* ============================
    STEP 5 HELP
    ============================	*/
    #body-help div#content {
        width: 100%;
        max-width: 1024px;
    }
    div.help-target {float:right;}
    div.help-target a {float:right; font-size:16px; color:#13659C}
    div#main-help sup {font-size:11px; font-weight:normal; font-style:italic; color:blue}
    div.help-online {text-align:center; font-size:18px; padding:10px 0 0 0; line-height:24px}
    div.help {color:#555; font-style:italic; font-size:11px; padding:4px; border-top:1px solid #dfdfdf}
    div.help-page fieldset {margin-bottom:25px}
    div#main-help {font-size:13px; line-height:17px}
    div#main-help h3 {border-bottom:1px solid silver; padding:8px; margin:4px 0 8px 0; font-size:20px}
    div#main-help span.step {color:#DB4B38}
    .help-opt {width: 100%; border: none; border-collapse: collapse;  margin:5px 0 0 0;}
    .help-opt .col-opt {
        width: 250px;
    }
    .help-opt td.section {background-color:#dfdfdf;}
    .help-opt td, .help-opt th {padding:15px 10px; border:1px solid silver;}
    .help-opt td:first-child {font-weight:bold; padding-right:10px; white-space:nowrap}
    .help-opt th {background: #333; color: #fff;border:1px solid #333 }

    #main-help section {
        border: 1px solid silver;
        margin-top: 28px;
        border-radius: 4px;
        overflow: hidden;
    }

    #main-help section h2.header {
        background-color:#F1F1F1;
        padding:15px;
        margin:0;
        font-size:20px;
    }

    #main-help section .content {
        padding: 10px;
    }

    /* ============================
    Expandable section
	============================	*/
    .expandable.close .expand-header {
        cursor:pointer;
    }

    .expandable.open .expand-header {
         cursor:pointer;
    }

    .expandable .expand-header::before {
        font-family: 'Font Awesome 5 Free';
        margin-right: 10px;
    }

    .expandable.close .expand-header::before {
        content: "\f0fe";
    }

    .expandable.open .expand-header::before {
        content: "\f146";
    }

    .expandable.close .content {
        display: none;
    }

    .expandable.open .content {
        display: block;
    }

    /* ============================
    VIEW EXCEPTION
	============================	*/
    .exception-trace {
        overflow: auto;
        border: 1px solid lightgray;
        padding: 10px;
        margin: 0;
    }


    /*!
     * password indicator
     */

    .top_testresult{
        font-weight:bold;	font-size:11px; color:#222;	display: block;
        position: absolute;
        top: 0;
        right: 30px;
        text-align: right;
        padding-right: 20px;
        box-sizing: border-box;
        width: 40%;
        height: 30px;
        line-height: 30px;
    }


    .top_shortPass,
    .top_badPass {
        background:#edabab;
        background: transparent linear-gradient(90deg, transparent 20%, #edabab);
        display:block;
    }
    .top_goodPass{
        background:#ffffe0;
        background: transparent linear-gradient(90deg, transparent 20%, #ffffe0);
        display:block;
    }
    .top_strongPass{
        background:#d3edab;
        background: transparent linear-gradient(90deg, transparent 20%, #d3edab);
        display:block;
    }

	/*================================================
	LIB OVERIDES*/
	input.parsley-error, textarea.parsley-error, select.parsley-error {
	  color:#B94A48 !important;
	  background-color:#F2DEDE !important;
	  border:1px solid #EED3D7 !important;
	}
	ul.parsley-errors-list {margin:1px 0 0 -40px; list-style-type:none; font-size:10px}
    .ui-widget {font-size:13px}


	<?php if ($GLOBALS['DUPX_DEBUG']) : ?>
		.dupx-debug {display:block; margin:0 0 25px 0; font-size:11px; background-color:#f5dbda; padding:8px; border:1px solid silver; border-radius:4px}
		.dupx-debug label {font-weight:bold; display:block; margin:4px 0 1px 0}
		.dupx-debug textarea {width:95%; height:100px; font-size:11px}
		.dupx-debug input {font-size:11px; padding:3px}
	<?php else : ?>
		.dupx-debug {display:none}
	<?php endif; ?>

</style>
<?php
DUPX_U_Html::css();