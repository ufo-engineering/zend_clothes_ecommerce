var filter = {};
var all_vals = [];
var filter_json = '';
function filter_set(cat, val){
    if($.inArray(val,all_vals) == '-1'){
        all_vals.push(val);
        if(filter[cat] == null){
            filter[cat] = '-'+val;  
        }else{
            filter[cat] += '-'+val;
        }
    }else{
        all_vals.splice($.inArray(val,all_vals), 1);
        filter[cat] = filter[cat].substr(0,strpos(filter[cat],'-'+val,0))+filter[cat].substr(strpos(filter[cat],'-'+val,0)+val.length+1);
    }
}

function strpos( haystack, needle, offset){	// Find position of first occurrence of a string
	// 
	// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)

	var i = haystack.indexOf( needle, offset ); // returns -1
	return i >= 0 ? i : false;
}

$(document).ready(function(){
    $('.filter_param').each(function(i,elem) {
    	if ($(this).hasClass('checked_param')) {
    		$(this).change();
    	} 
    });
});

function filter_apply(){
    var url = '/filter';
    var origin_url = window.location.href;
    var page_str = strpos(origin_url,'/page',0);
    var price_str = strpos(origin_url,'?',0);
    var href_url = '';
    $.each(filter, function(index, value) {
        if(value !== '') url += '/' + index+value;
    }); 
    if(price_str != false){
        price_str = origin_url.substr(price_str);
        origin_url = origin_url.substr(0,strpos(origin_url,'?',0));
        if(page_str != false){
            if(strpos(origin_url,'/filter',0) == false){
                href_url = origin_url.substr(0,page_str) +url+origin_url.substr(page_str,page_str.length)+price_str;
            }else{
                href_url = origin_url.substr(0,strpos(origin_url,'/filter',0)) +url+origin_url.substr(page_str,page_str.length)+price_str;
            }
        }else{
            if(strpos(origin_url,'/filter',0) == false){
                href_url = origin_url +url+price_str;
            }else{
                href_url = origin_url.substr(0,strpos(origin_url,'/filter',0)) +url+price_str;
            }
        }
    }else{
        if(page_str != false){
            if(strpos(origin_url,'/filter',0) == false){
                href_url = origin_url.substr(0,page_str) +url+origin_url.substr(page_str,page_str.length);
            }else{
                href_url = origin_url.substr(0,strpos(origin_url,'/filter',0)) +url+origin_url.substr(page_str,page_str.length);
            }
        }else{
            if(strpos(origin_url,'/filter',0) == false){
                href_url = origin_url +url;
            }else{
                href_url = origin_url.substr(0,strpos(origin_url,'/filter',0)) +url;
            }
        }
    }
    window.location.href = href_url;
}