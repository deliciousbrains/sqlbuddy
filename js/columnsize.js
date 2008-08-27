var activeColumnId = -1;
var activeColumn;
var styleNodeKeys = [];
var styleNodes = [];

function startColumnResize(e)
{
	var event = new Event(e);
	
	activeColumn = $(event.target.offsetParent.previousSibling.firstChild);
	
	activeColumnId = parseInt(activeColumn.getProperty("column"));
	
	lastWidth = parseInt(activeColumn.clientWidth) - 11; // -11 to account for padding
	mouseX = event.page.x;
	
	document.body.style.cursor = "ew-resize";
	
	window.addEvent("mousemove", columnResize);
	window.addEvent("mouseup", endColumnResize);
	
	return false;
}

function columnResize(e)
{
	if (activeColumn)
	{
		var event = new Event(e);
		
		var diff = (event.page.x - mouseX);
		
		lastWidth = (lastWidth + diff);
		mouseX = event.page.x;
		
		var removeLater = -1;
		var keyName = 'pane' + sb.topTab + '_' + activeColumnId;
		
		for (var i=0; i<styleNodeKeys.length; i++)
		{
			if (styleNodeKeys[i] == keyName)
			{
				document.getElementsByTagName("head")[0].removeChild(styleNodes[i]);
				removeLater = i;
			}
		}
		
		if (removeLater >= 0)
		{
			styleNodes.splice(removeLater, 1);
			styleNodeKeys.splice(removeLater, 1);
		}
		
		var newNode = new Element("style");
		newNode.setAttribute("type", "text/css");
		
		newNode.appendText("#pane" + sb.topTab + " .column" + activeColumnId + " { width: " + lastWidth + "px !important }");
		document.getElementsByTagName("head")[0].appendChild(newNode);
		
		styleNodes.push(newNode);
		styleNodeKeys.push(keyName);
	}
}

function endColumnResize()
{
	document.body.style.cursor = "";
	activeColumn = null;
	window.removeEvent("mousemove", columnResize);
	window.removeEvent("mouseup", endColumnResize);
}

function clearColumnSizes()
{
	if (styleNodes.length > 0)
	{
		for (var i=0; i<styleNodes.length; i++)
		{
			if (f(styleNodes[i]) != "")
			{
				document.getElementsByTagName("head")[0].removeChild(styleNodes[i]);
			}
		}
		styleNodes = [];
		styleNodeKeys = [];
	}
}