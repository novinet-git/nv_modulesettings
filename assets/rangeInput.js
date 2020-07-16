var getRangeData = function(element, all)
{
    var value = element.value;
    var data = element.getAttribute("data-values").split(";");

    if (!all)
    {
      
        
        if (!data || data.length < value) return;
        
        value = data[value];
        var split = value.split(",")
        var key = split[0];
        var label = split[1];
       
        return {
            key: key,
            label: label
        }
    }
    else
    {
        result = [];

        if (!data) return;

        var length = data.length;

        for (var i = 0; i < length; i++) 
        {
            var value  = data[i];
            var split = value.split(",")
            var key = split[0];
            var label = split[1];

            result.push({
                key: key,
                label: label
            });

        }

        return result;
    }
}




var getRangeValueToKeyValue = function(element, key)
{
    var data = getRangeData(element, true);
    var length = data.length;

    for (var i = 0; i < length; i++)
    {
        var item = data[i];

        if (item.key == key)
        {
            return i;
        }
    }

    return false;
}

var setRangeDefault = function(element)
{
    var defaultKey = element.getAttribute("data-default");
    var value = getRangeValueToKeyValue(element, defaultKey);
    element.value = value
    element.setAttribute("value", value);
}

var isRangeFieldNotSyncWidthHiddenValueField = function(element)
{
    var hiddenInput = getRangeHiddenValueField(element);
    if (!hiddenInput) return;

    if (hiddenInput.value === "") {
        var defaultKey = element.getAttribute("data-default"); 
        var index = getRangeValueToKeyValue(element, defaultKey);
    } else {
    var index = getRangeValueToKeyValue(element, hiddenInput.value);
    }
    return index == element.value;

}


var getRangeHiddenValueField = function(element)
{
    var name = element.name;
    var split = name.split("_range");
    name = split.join("");
    return document.querySelector('[name="'+name+'"]');
}       

var setRangeHiddenValue = function(element)
{
    var data = getRangeData(element,false);
    var hiddenInput = getRangeHiddenValueField(element);
    if (!hiddenInput) return;
    var defaultKey = element.getAttribute("data-default");
    if (data.key == defaultKey) {
        hiddenInput.value = '';
        return;
    }
    hiddenInput.value = data.key;
}

var setRangeLabel = function(element)
{
    var data = getRangeData(element,false);
    var label = document.getElementById(element.labelid);
    if (!label) return;
    label.innerText = data.label;
    setRangeHiddenValue(element);
}

var checkRangeValue = function(element)
{
    var attrVal = element.getAttribute("value");
    if (attrVal == "")
    {
        setRangeDefault(element);
    } 
    else if (!isRangeFieldNotSyncWidthHiddenValueField(element))
    {
        var hiddenInput = getRangeHiddenValueField(element);
        if (!hiddenInput) return;
        element.value = getRangeValueToKeyValue(element, hiddenInput.value);
    }
}

var checkRangeLabel = function(element)
{
    if (!element.labelid)
    {
        var label = document.createElement("label");
        label.id = element.id + "_label";
        element.labelid = label.id;
        element.parentNode.append(label);
        setRangeLabel(element);
    }
}

var onRangeInput = function()
{
    
    var elements = document.querySelectorAll(".nv-range-listener");
    var length = elements.length;

    for (var i = 0; i < length; i++) 
    {
        var element = elements[i];
        checkRangeLabel(element);
        setRangeLabel(element);
    }
}

var rangeUpdate = function()
{
    var elements = document.querySelectorAll(".nv-range-listener");
    var length = elements.length;

    for (var i = 0; i < length; i++) 
    {
        var element = elements[i];
        checkRangeValue(element);
        checkRangeLabel(element);
    }
}

setInterval(rangeUpdate, 200);