//Author: Realazy
//http://realazy.org/blog/ (chinese)

jQuery.arrRemove = function(arr, rm){
    for (var i = 0, n = 0; i < arr.length; ++i){
        if (arr[i] != rm)
            arr[n++] = arr[i];
    }
    arr.length--;
}

//usage: jQuery(from).tagTo(target, seperator)
//from contain the tags(use a links), target must be assigned and its type must be input type="text" or textarea
//seperator can be "-", "," and space etc, if not assign, the default seperator is ","
//tclass is the class name of the tag which is currently selected, if not assign, the default class name is "selected"

jQuery.fn.tagTo = function(target, seperator, tclass){
    if ("string" == typeof target) target = jQuery(target);
    seperator = arguments[1] || ",";
    tclass = arguments[2] || "selected";

    var tagname = target.get(0).nodeName.toLowerCase();
    if (tagname == "input" || tagname == "textarea"){
        jQuery('a', this).click(function(){
            if (jQuery.trim(target.val()) == ''){
                target.val(jQuery(this).text());
                jQuery(this).addClass(tclass);
            } else {
                var arr = target.val().split(seperator);    
                var isInArr = false;
                var position;
                for (var i = 0, n = arr.length; i < n; ++i) {
                    if (jQuery.trim(arr[i]) == jQuery(this).text()){
                        isInArr = true;
                        position = i;
                        break;
                    }
                }
                if (isInArr == true){
                    jQuery.arrRemove(arr, arr[position]);
                    jQuery(this).removeClass(tclass);
                } else {
                    arr.push(jQuery(this).text());
                    jQuery(this).addClass(tclass);
                }
                target.val(arr.join(seperator));
            }
            return false;
        }); 
    } else {
        throw "target must be an text area";
    }
} 
