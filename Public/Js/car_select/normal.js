
function comp_fctlist(p_fct_name,p_br_name,p_year_name,bdef,xdef,kdef) {
	bdef=bdef||0;
    var dd = fct['0'].split(',');
    var fct_name = p_fct_name ? p_fct_name : 'fctbox';
    var br_name = p_br_name ? p_br_name : 'brbox';
    var year_name = p_year_name ? p_year_name : 'yearbox';
    obj = document.getElementById(fct_name);
    obj.options.length = 0;
    obj.options[0] = new Option('选择品牌', 0);
    var c = 1;
    for (loop = 0; loop < dd.length; loop += 2) {
        obj.options[c] = new Option(dd[loop + 1], dd[loop]);
		if(bdef==dd[loop])
		{
			obj.options[c].selected = true;  
		}
        c++;
    }
    try {
        comp_brlist(fct_name, br_name, year_name,xdef,kdef);
    }
    catch (e) { }
}
function comp_brlist(p_fct_name, p_br_name, p_year_name,def,kdef) {
    try {
		def=def||0;
        var fct_name = p_fct_name ? p_fct_name : 'fctbox';
        var br_name = p_br_name ? p_br_name : 'brbox';
        var year_name = p_year_name ? p_year_name : 'yearbox';
        obj_fct = document.getElementById(fct_name);
        obj_br = document.getElementById(br_name);
        obj_year = document.getElementById(year_name);
        if (!obj_fct) return;
        if (obj_fct.value != '0') {
            if (br[obj_fct.value] != '') {
                obj_br.disabled = false;
                var brand_list = br[obj_fct.value].split(',');
                obj_br.options.length = 0;
                obj_br.options[0] = new Option('选择车系', 0);
                var index = 1;
                for (loop = 0; loop < brand_list.length; loop += 2) {
                    obj_br.options[index] = new Option(brand_list[loop + 1], brand_list[loop]);
					if(def==brand_list[loop])
					{
					obj_br.options[index].selected = true;  
					}
                    index++;
                }
            }
            else {
				obj_br.disabled = true;
                obj_br.options.length = 0;
                obj_br.options[0] = new Option('选择车系', 0);
            }
        }
        else {
            obj_br.disabled = true;
            obj_br.options.length = 0;
            obj_br.options[0] = new Option('选择车系', 0);
        }
        if (obj_year) {
            comp_yearlist(fct_name, br_name, year_name,kdef);
        }
    }
    catch (e) {
    }
}
function comp_yearlist(p_fct_name, p_br_name, p_year_name,def) {
    try {
		def=def||0;
        var fct_name = p_fct_name ? p_fct_name : 'fctbox';
        var br_name = p_br_name ? p_br_name : 'brbox';
        var year_name = p_year_name ? p_year_name : 'yearbox';
        obj_fct = document.getElementById(fct_name);
        obj_br = document.getElementById(br_name);
        obj_year = document.getElementById(year_name);
        if (!obj_year) return;
        if (obj_fct.value == '0') {
            obj_year.disabled = true;
        }
        else {
            obj_year.disabled = false;
        }
        if (obj_br.value != '0') {
            if (md[obj_br.value] != '') {
                var year_list = md[obj_br.value].split(',');
                obj_year.options.length = 0;
                obj_year.options[0] = new Option('全部车型', 0);
                var index = 1;
                for (loop = 0; loop < year_list.length; loop += 2) {
                    obj_year.options[index] = new Option(year_list[loop + 1], year_list[loop]);
					if(def==year_list[loop])
					{	
						obj_year.options[index].selected = true;  
					}
                    index++;
                }
            }
            else {
                obj_year.options.length = 0;
                obj_year.options[0] = new Option('全部车型', 0);
            }
        }
        else {
            obj_year.options.length = 0;
            obj_year.options[0] = new Option('全部车型', 0);
        }
    }
    catch (e) {
    }
}