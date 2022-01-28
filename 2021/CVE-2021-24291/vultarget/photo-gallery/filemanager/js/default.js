var ajax = true;
var dragFiles;
var isUploading;
var filesSelected;
var keyFileSelected;
var all_files_selected = false;
var params = [];
var no_selected_files = [];
var wdb_all_files_filtered = [];

jQuery(function() {
	var page = 1;
	var page_per = jQuery("#explorer_body").data('page_per');
	jQuery("#explorer_body_container").scroll(function () {
		var items_count = jQuery("#explorer_body .explorer_item").length;
		var scroll_position = jQuery(this).scrollTop() + jQuery(this).innerHeight();
		var scroll_Height = jQuery(this)[0].scrollHeight - 200;
		if ( scroll_position >= scroll_Height && items_count == (page_per * page) ) {
			var orderby = jQuery("input[name='sort_by']").val();
			var order = jQuery("input[name='sort_order']").val();
			params['is_search'] = false;
			params['element'] = jQuery("#explorer_body");
			params['search'] = jQuery('#search_by_name .search_by_name').val().toLowerCase();
			params['page'] = page;
			params['orderby'] = orderby;
			params['order'] = order;
			ajax_print_images( params );
			page++;
		}
	});

	filesSelected = [];
	dragFiles = [];

	jQuery("#wrapper").css("top", jQuery("#file_manager_message").css("height"));
	jQuery(window).resize(function () {
		jQuery("#container").css("top", jQuery("#file_manager_message").css("height"));
	});

	isUploading = false;
	jQuery("#uploader").css("display", "none");
	jQuery("#uploader_progress_bar").css("display", "none");
	jQuery("#importer").css("display", "none");

	//decrease explorer header width by scroller width
	jQuery(".scrollbar_filler").css("width", getScrollBarWidth() + "px");
	jQuery(document).keydown(function(e) {
		onKeyDown(e);
	});

	jQuery("#search_by_name .search_by_name").on("input", function() { // keyup
		page = 0;
		var search_by_name = jQuery(this).val().toLowerCase();
		var orderby = jQuery("input[name='sort_by']").val();
		var order = jQuery("input[name='sort_order']").val();
		var element = jQuery("#explorer_body");
		element.html('');
		jQuery('html, body').animate({scrollTop:0},0);
		params['is_search'] = true;
		params['element'] = element;
		params['search'] = search_by_name;
		params['page'] = page;
		params['orderby'] = orderby;
		params['order'] = order;
		ajax_print_images( params );
	});
});

function getClipboardFiles() {
  return jQuery("form[name=adminForm]").find("input[name=clipboard_file]").val();
}

function submit(task, sortBy, sortOrder, itemsView, destDir, fileNewName, newDirName, clipboardTask, clipboardFiles, clipboardSrc, clipboardDest) {
  jQuery('#loading_div', window.parent.document).show();
  var names_array = [];
  if ( all_files_selected === true ) {
    for (i in wdb_all_files_filtered) {
      var index = no_selected_files.indexOf(wdb_all_files_filtered[i]["name"]);
      if (index < 0) {
        var all_names = wdb_all_files_filtered[i]["name"];
        names_array.push(all_names);
      }
    }
    fileNames = names_array.join("**#**");
  }
  else {
    fileNames = filesSelected.join("**#**");
  }

  switch (task) {
    case "parsing_items":
      destDir = dir;
	break;
    case "rename_item":
      destDir = dir;
      newDirName = "";
      clipboardTask = ""
      clipboardDest = "";
      break;
    case "remove_items":
      destDir = dir;
      fileNewName = "";
      newDirName = "";
      clipboardTask = ""
      clipboardDest = "";
      break;
    case "make_dir":
      destDir = dir;
      fileNewName = "";
      clipboardTask = ""
      clipboardDest = "";
      break;
    case "paste_items":
      destDir = dir;
      fileNewName = "";
      newDirName = "";
      break;
    case "import_items":
      destDir = dir;
      fileNewName = "";
      newDirName = "";
      break;
    default:
      task = "";
      break;
  }

  jQuery("form[name=adminForm]").find("input[name=task]").val(task);

  if (sortBy != null) {
    jQuery("form[name=adminForm]").find("input[name=sort_by]").val(sortBy);
  }
  if (sortOrder != null) {
    jQuery("form[name=adminForm]").find("input[name=sort_order]").val(sortOrder);
  }
  if (itemsView != null) {
    jQuery("form[name=adminForm]").find("input[name=items_view]").val(itemsView);
  }
  if (destDir != null) {
    jQuery("form[name=adminForm]").find("input[name=dir]").val(destDir);
  }
  if (fileNames != null) {
    jQuery("form[name=adminForm]").find("input[name=file_names]").val(fileNames);
  }
  if (fileNewName != null) {
    jQuery("form[name=adminForm]").find("input[name=file_new_name]").val(fileNewName);
  }
  if (newDirName != null) {
    jQuery("form[name=adminForm]").find("input[name=new_dir_name]").val(newDirName);
  }
  if (clipboardTask != null) {
    jQuery("form[name=adminForm]").find("input[name=clipboard_task]").val(clipboardTask);
  }
  if (clipboardFiles != null) {
    jQuery("form[name=adminForm]").find("input[name=clipboard_files]").val(clipboardFiles);
  }
  if (clipboardSrc != null) {
    jQuery("form[name=adminForm]").find("input[name=clipboard_src]").val(clipboardSrc);
  }
  if (clipboardDest != null) {
    jQuery("form[name=adminForm]").find("input[name=clipboard_dest]").val(clipboardDest);
  }
  jQuery("form[name=adminForm]").submit();
}

function updateFileNames() {
  var result = "";
  if (filesSelected.length > 0) {
    var fileNames = [];
    for (var i = 0; i < filesSelected.length; i++) {
      fileNames[i] = "'" + filesSelected[i] + "'";
    }
    result = fileNames.join(" ");
  }
  jQuery("#file_names_span span").html(result);
}

function submitFiles() {
  if (filesSelected.length == 0) {
    return;
  }

  var filesValid = [];
  if (all_files_selected === true) {
    for (i in wdb_all_files_filtered) {
      var fileData = [];
      if (wdb_all_files_filtered[i]["is_dir"] === '0') {
        var index = no_selected_files.indexOf(wdb_all_files_filtered[i]["name"]);
        if ( index < 0 ) {
          fileData['name'] = wdb_all_files_filtered[i]["name"];
          fileData['filename'] = wdb_all_files_filtered[i]["filename"];;
          fileData['alt'] = wdb_all_files_filtered[i]["alt"];;
          fileData['url'] = dir + "/" + wdb_all_files_filtered[i]["name"];
          fileData['reliative_url'] = dirUrl + "/" + wdb_all_files_filtered[i]["name"];
          fileData['thumb_url'] = dir + "/thumb/" + wdb_all_files_filtered[i]["name"];
          fileData['thumb'] = wdb_all_files_filtered[i]["thumb"];
          fileData['size'] = wdb_all_files_filtered[i]["size"];
          fileData['filetype'] = wdb_all_files_filtered[i]["type"];
          fileData['date_modified'] = wdb_all_files_filtered[i]["date_modified"];
          fileData['resolution'] = wdb_all_files_filtered[i]["resolution"];
          fileData['resolution_thumb'] = wdb_all_files_filtered[i]["resolution_thumb"];
          fileData['aperture'] = wdb_all_files_filtered[i]["aperture"];
          fileData['credit'] = wdb_all_files_filtered[i]["credit"];
          fileData['camera'] =wdb_all_files_filtered[i]["camera"];
          fileData['caption'] = wdb_all_files_filtered[i]["caption"];
          fileData['iso'] = wdb_all_files_filtered[i]["iso"];
          fileData['orientation'] = wdb_all_files_filtered[i]["orientation"];
          fileData['copyright'] = wdb_all_files_filtered[i]["copyright"];
          fileData['tags'] = wdb_all_files_filtered[i]["tags"];
          filesValid.push(fileData);
        }
      } else {
        submit("", null, null, null, dir + DS + jQuery(file_object).attr("name"), null, null, null, null, null, null);
        return
      }
    }
  }
  else {
    for (var i = 0; i < filesSelected.length; i++) {
      var file_object = jQuery('.explorer_item[name="' + filesSelected[i] + '"]');
      if (jQuery(file_object).attr("isDir") == "false") {
        var fileData = [];
        fileData['name'] = filesSelected[i];
        fileData['filename'] = jQuery(file_object).attr("filename");
        fileData['alt'] = jQuery(file_object).attr("alt");
        fileData['url'] = dir + "/" + filesSelected[i];
        fileData['reliative_url'] = dirUrl + "/" + filesSelected[i];
        fileData['thumb_url'] = dir + "/thumb/" + filesSelected[i];
        fileData['thumb'] = jQuery(file_object).attr("filethumb");
        fileData['size'] = jQuery(file_object).attr("filesize");
        fileData['filetype'] = jQuery(file_object).attr("filetype");
        fileData['date_modified'] = jQuery(file_object).attr("date_modified");
        fileData['resolution'] = jQuery(file_object).attr("fileresolution");
        fileData['resolution_thumb'] = jQuery(file_object).attr("fileresolution_thumb");
        fileData['aperture'] = jQuery(file_object).attr("fileAperture");
        fileData['credit'] = jQuery(file_object).attr("fileCredit");
        fileData['camera'] = jQuery(file_object).attr("fileCamera");
        fileData['caption'] = jQuery(file_object).attr("fileCaption");
        fileData['iso'] = jQuery(file_object).attr("fileIso");
        fileData['orientation'] = jQuery(file_object).attr("fileOrientation");
        fileData['copyright'] = jQuery(file_object).attr("fileCopyright");
        fileData['tags'] = jQuery(file_object).attr("fileTags");
        filesValid.push(fileData);
      } else {
        submit("", null, null, null, dir + DS + jQuery(file_object).attr("name"), null, null, null, null, null, null);
        return
      }
    }
  }
  window.parent[callback](filesValid);
  window.parent.tb_remove();
}

function getScrollBarWidth() {
  var inner = document.createElement("p");
  inner.style.width = "100%";
  inner.style.height = "200px";

  var outer = document.createElement("div");
  outer.style.position = "absolute";
  outer.style.top = "0px";
  outer.style.left = "0px";
  outer.style.visibility = "hidden";
  outer.style.width = "200px";
  outer.style.height = "150px";
  outer.style.overflow = "hidden";
  outer.appendChild(inner);

  document.body.appendChild(outer);
  var w1 = inner.offsetWidth;
  outer.style.overflow = "scroll";
  var w2 = inner.offsetWidth;
  if (w1 == w2) {
    w2 = outer.clientWidth;
  }
  document.body.removeChild(outer);

  return (w1 - w2);
}

function getFileName(file) {
  var dotIndex = file.lastIndexOf('.');
  return file.substring(0, dotIndex < 0 ? file.length : dotIndex);
}

function getFileExtension(file) {
  return file.substring(file.lastIndexOf('.') + 1);
}

//ctrls bar handlers
function onBtnUpClick(event, obj) {
  var destDir = dir.substring(0, dir.lastIndexOf(DS));
  submit("", null, null, null, destDir, null, null, null, null, null, null);
}

function onBtnMakeDirClick(event, obj) {
  var newDirName = prompt(messageEnterDirName);
  if ((newDirName) && (newDirName != "")) {
    submit("make_dir", null, null, null, null, null, newDirName.replace(/ /g, "_"), null, null, null, null);
  }
}

function onBtnRenameItemClick(event, obj) {
	if (filesSelected.length != 0) {
		var oldName = getFileName(filesSelected[0]);
		var newName = prompt(messageEnterNewName, oldName);
		if ((newName != null) && (newName != "")) {
			newName = newName.replace(/"/g, "").replace(/ /g, "_").replace(/%/g, "");
			submit("rename_item", null, null, null, null, newName, null, null, null, null, null);
		}
	}
}

function onBtnCopyClick(event, obj) {
	if (filesSelected.length != 0) {
		var names_list =  filesSelected.join("**#**");
		var names_array = [];
		if (all_files_selected === true) {
			for (i in wdb_all_files_filtered) {
				var index = no_selected_files.indexOf(wdb_all_files_filtered[i]["name"]);
				if (index < 0) {
				  var all_names = wdb_all_files_filtered[i]["name"];
				  names_array.push(all_names);
				}
			}
			names_list =  names_array.join("**#**");
		}

		submit("", null, null, null, null, null, null, "copy", names_list, dir, null);
	}
}

function onBtnCutClick(event, obj) {
	if (filesSelected.length != 0) {
		var names_list =  filesSelected.join("**#**");
		var names_array = [];
		if (all_files_selected === true) {
			for (var i in wdb_all_files_filtered) {
				var index = no_selected_files.indexOf(wdb_all_files_filtered[i]["name"]);
				if (index < 0) {
					var all_names = wdb_all_files_filtered[i]["name"];
					names_array.push(all_names);
				}
			}
			names_list = names_array.join("**#**");
		}
		submit("", null, null, null, null, null, null, "cut", names_list, dir, null);
	}
}

function onBtnPasteClick(event, obj) {
	if (getClipboardFiles() != "") {
		submit("paste_items", null, null, null, null, null, null, null, null, null, dir);
	}
}

function onBtnRemoveItemsClick(event, obj) {
	if ((filesSelected.length != 0) && (confirm(warningRemoveItems) == true)) {
		submit("remove_items", null, null, null, null, null, null, null, null, null, null);
	}
}

function onBtnParsingItemsClick(event, obj) {
	submit("parsing_items", null, null, null, null, null, null, null, null, null, null);
}

function onBtnShowUploaderClick(event, obj) {
	jQuery(document).trigger("onUploadFilesPressed");
	jQuery("#uploader").fadeIn();
}

function onBtnViewThumbsClick(event, obj) {
	submit("", null, null, "thumbs", null, null, null, null, null, null, null);
}

function onBtnViewListClick(event, obj) {
	submit("", null, null, "list", null, null, null, null, null, null, null);
}

function onBtnBackClick(event, obj) {
	if ((isUploading == false) || (confirm(warningCancelUploads) == true)) {
		submit("", null, null, null, null, null, null, null, null, null, null);
	}
}

function onPathComponentClick(event, obj, key) {
	var path = '';
	var pathArr = [];
	jQuery("#path .path_dir").each( function( i,v ) {
		path += ( i == 0 ) ? '' : '/' + jQuery(v).text().trim();
		pathArr[i] = path;
	});
	var path = ( pathArr[key] ) ? pathArr[key] : '';
	submit('display', null, null, null, path, null, null, null, null, null, null);
}

function onBtnShowImportClick(event, obj) {
	jQuery("#importer").fadeIn();
}

function onNameHeaderClick(event, obj) {
	var newSortOrder = ((sortBy == "name") && (sortOrder == "asc")) ? "desc" : "asc";
	submit("", "name", newSortOrder, null, null, null, null, null, null, null, null);
}

function onSizeHeaderClick(event, obj) {
	var newSortOrder = ((sortBy == "size") && (sortOrder == "asc")) ? "desc" : "asc";
	submit("", "size", newSortOrder, null, null, null, null, null, null, null, null);
}

function onDateModifiedHeaderClick(event, obj) {
	var newSortOrder = ((sortBy == "date_modified") && (sortOrder == "asc")) ? "desc" : "asc";
	submit("", "date_modified", newSortOrder, null, null, null, null, null, null, null, null);
}

//file handlers
function onKeyDown(e) {
	var e = e || window.event;
	var chCode1 = e.which || e.paramlist_keyCode;
	if ((e.ctrlKey || e.metaKey) && chCode1 == 65) {
    onBtnSelectAllClick(dir + DS);
		e.preventDefault();
	}
}

function onFileMOver(event, obj) {
	jQuery(obj).addClass("explorer_item_hover");
}

function onFileMOut(event, obj) {
	jQuery(obj).removeClass("explorer_item_hover");
}

function onFileClick(event, obj) {
  var isMobile = (/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()));
  jQuery(".explorer_item").removeClass("explorer_item_select");
  var objName = jQuery(obj).attr("name");
  if (event.ctrlKey == true || event.metaKey == true || isMobile) {
    if (all_files_selected === true) {
      if (filesSelected.indexOf(objName) == -1) {
        var index = no_selected_files.indexOf(objName);
        if (index >= 0) {
          no_selected_files.splice( index, 1 );
        }
      }
      else {
        no_selected_files.push(objName);
      }
    }
    if (filesSelected.indexOf(objName) == -1) {
      filesSelected.push(objName);
      keyFileSelected = obj;
    }
    else {
      filesSelected.splice(filesSelected.indexOf(objName), 1);
      jQuery(obj).removeClass("explorer_item_select");
      jQuery(obj).removeClass("explorer_item_hover");
    }
  }
  else if (event.shiftKey == true) {
    filesSelected = [];
    var explorerItems = jQuery(".explorer_item");
    var curFileIndex = explorerItems.index(jQuery(obj));
    var keyFileIndex = explorerItems.index(keyFileSelected);
    var startIndex = Math.min(keyFileIndex, curFileIndex);
    var endIndex = startIndex + Math.abs(curFileIndex - keyFileIndex);
    for (var i = startIndex; i < endIndex + 1; i++) {
      filesSelected.push(jQuery(explorerItems[i]).attr("name"));
    }
  }
  else {
    filesSelected = [jQuery(obj).attr("name")];
    keyFileSelected = obj;
  }

  for (var i = 0; i < filesSelected.length; i++) {
    jQuery('.explorer_item[name="' + filesSelected[i] + '"]').addClass("explorer_item_select");
  }
  updateFileNames();
}

function onFileDblClick(event, obj) {
  if (jQuery(obj).attr("isDir") == "true") {
    submit("", null, null, null, dir + DS + jQuery(obj).attr("name"), null, null, null, null, null, null);
  }
  else {
    filesSelected = [];
    filesSelected.push(jQuery(obj).attr("name"));
    submitFiles();
  }
}
/*TODO function not used on view! (only file)*/
function onFileDragStart(event, obj) {
  var objName = jQuery(obj).attr("name");
  if (filesSelected.indexOf(objName) < 0) {
    jQuery(".explorer_item").removeClass("explorer_item_select");
    if (event.ctrlKey == true || event.metaKey == true) {
      if (filesSelected.indexOf(objName) == -1) {
        filesSelected.push(objName);
        keyFileSelected = obj;
      }
    }
    else if (event.shiftKey == true) {
      filesSelected = [];
      var explorerItems = jQuery(".explorer_item");
      var curFileIndex = explorerItems.index(jQuery(obj));
      var keyFileIndex = explorerItems.index(keyFileSelected);
      var startIndex = Math.min(keyFileIndex, curFileIndex);
      var endIndex = startIndex + Math.abs(curFileIndex - keyFileIndex);
      for (var i = startIndex; i < endIndex + 1; i++) {
        filesSelected.push(jQuery(explorerItems[i]).attr("name"));
      }
    }
    else {
      filesSelected = [jQuery(obj).attr("name")];
      keyFileSelected = obj;
    }

    for (var i = 0; i < filesSelected.length; i++) {
      jQuery('.explorer_item[name="' + filesSelected[i] + '"]').addClass("explorer_item_select");
    }

    updateFileNames();
  }
  dragFiles = filesSelected;
}
/*TODO function not used on view! (only folder)*/
function onFileDragOver(event, obj) {
  event.preventDefault();
}
/*TODO function not used on view! (only folder)*/
function onFileDrop(event, obj) {
  var destDirName = jQuery(obj).attr("name");
  if ((dragFiles.length == 0) || (dragFiles.indexOf(destDirName) >= 0)) {
    return false;
  }
  var clipboardTask = (event.ctrlKey == true || event.metaKey == true) ? "copy" : "cut";
  var clipboardDest = dir + DS + destDirName;
  submit("paste_items", null, null, null, null, null, null, clipboardTask, dragFiles.join("**#**"), dir, clipboardDest);
  event.preventDefault();
}

function onBtnOpenClick(event, obj) {
  if (jQuery('.explorer_item[name="' + filesSelected[0] + '"]').attr("isDir") == true) {
    filesSelected.length = 1;
    submit("", null, null, null, dir + DS + filesSelected[0], null, null, null, null, null, null);
  }
  else {
    submitFiles();
  }
  window.parent.bwg_remove_loading_block();
}

function onBtnCancelClick(event, obj) {
  window.parent.tb_remove();
}

function onBtnSelectAllClick( dir ) {
	jQuery(".explorer_item").removeClass("explorer_item_select");
	jQuery(".explorer_item:visible").addClass("explorer_item_select");
	var search = jQuery('#search_by_name .search_by_name').val();
	var orderby = jQuery("input[name='sort_by']").val();
	var order = jQuery("input[name='sort_order']").val();
	jQuery.ajax({
		type: "POST",
		dataType: "json",
		url: ajax_get_all_select_url,
		data: {
			dir,
			search,
			order,
			orderby
		},
		success: function (res) {
			files = res.data;
			filesSelected = [];
			jQuery.each(files, function(i, v) {
				var objName = v.name;
				if (filesSelected.indexOf(objName) == -1) {
					filesSelected.push(objName);
					keyFileSelected = this;
				}
			});
			all_files_selected = true;
			wdb_all_files_filtered = files;
		},
		beforeSend: function() {
		},
		complete:function() {
		}
	});
}

function ajax_print_images( params ) {
	var element = params['element'];
	var is_search = params['is_search'];
	var paged = params['page'];
	var search = params['search'];
	var orderby = params['orderby'];
	var order = params['order'];
	var page_per = element.data('page_per');
	var files_count = element.data('files_count');
	var found_wrap = jQuery('#explorer_body_container .fm-no-found-wrap');
	if ( (page_per * paged) < files_count ) {
		jQuery.ajax({
			type: "POST",
			dataType: "json",
			url: ajax_pagination_url,
			data: {
				dir,
				paged,
				search,
				order,
				orderby
			},
			success: function (res) {
				if ( is_search ) {
					jQuery('#loading_div', window.parent.document).hide();
					element.html('');
				}
				if ( res.html ) {
					element.append(res.html);
					jQuery('#explorer_body .explorer_item').each(function(i,that) {
						var img = jQuery(that).find('img');
							img.attr('scr', jQuery(that).attr('filethumb') );
					});
					found_wrap.hide();
				}
				else if ( search && res.html == '') {
					found_wrap.show();
				}
			},
			beforeSend: function() {
				if ( is_search ) {
					jQuery('#loading_div', window.parent.document).show();
					element.html('');
				}
			},
			complete:function() {
			}
		});
	}
}