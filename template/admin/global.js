var currentOpen = null;
var activeId = 0;

function get_by_id(id) {

	itm = null;
	
	if (document.getElementById) {
		itm = document.getElementById(id);
	} else if (document.all) {
		itm = document.all[id];
	} else if (document.layers) {
		itm = document.layers[id];
	}
	
	return itm;
}

function show_hide(id) {
	if (!id) 
		return;
	
	if (itm = get_by_id('menu_'+id)) {
		if (itm.style.display == "none") {
			itm.style.display = "";
		} else {
			itm.style.display = "none";
		}
	} else if (itm = get_by_id(id)) {
		if (itm.style.display == "none") {
			itm.style.display = "";
		} else {
			itm.style.display = "none";
		}
	}
}

function reveal(id) {
	if (!id) 
		return;
	
	if (citm = get_by_id(currentOpen)) {
		citm.style.display = "none";
	}
	
	if (itm = get_by_id(id)) {
		itm.style.display = "";
		currentOpen = id;
	}
}

function show(id) {
	if (!id) 
		return;
	
	if (itm = get_by_id(id)) {
		itm.style.display = "";
	}
}

function hide(id) {
	if (!id) 
		return;
	
	if (itm = get_by_id(id)) {
		itm.style.display = "none";
	}
}

function disable_element(id) {
	if (!id) 
		return;
	
	if (itm = get_by_id(id)) {
		itm.disabled = true;
	}
}

function add_tag(id, tag) {
	if (!id)
		return;
	if (!tag)	
		return;
	
	if (item = get_by_id(id)) {
		if (item.value != '') {
			itm.value = itm.value + ',';
		}
		
		itm.value = itm.value + tag;
	}
}

function clear_field(id) {
	if (!id)
		return;
	
	if (item = get_by_id(id)) {
		item.value = '';
	}
}

function set_id(frm,id) {
	if (!id)
		return;
	if (!frm)
		return;
	
	frmobj = get_by_id(frm);
	
	frmobj.value = id;
}

function uc_box(id) {
	if (!id)
		return;
	
	if (itm = get_by_id(id)) {
		itm.checked = 0;
	}
}