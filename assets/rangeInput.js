var getRangeData = function(element)
{
    var value = element.value;
    var data = element.getAttribute("data-values").split(";");
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

var setRangeHiddenValue = function(element)
{
    var data = getRangeData(element);
    var name = element.name;
    var split = name.split("_range");
    name = split.join("");
    var hiddenInput = document.querySelector('[name="'+name+'"]');
    
    if (!hiddenInput) return;
    hiddenInput.value = data.key;
}

var setRangeLabel = function(element)
{
    var data = getRangeData(element);
    var label = document.getElementById(element.labelid);
    label.innerText = data.label;
    setRangeHiddenValue(element);
}

var checkRangeValue = function(element)
{
    var attrVal = element.getAttribute("value");
    if (attrVal == "")
    {
        var def = parseInt(element.getAttribute("data-default"));
        element.value = def;
        element.setAttribute("value", def);
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