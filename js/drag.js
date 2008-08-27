var lastLeft = -1;
var lastTop = -1;

function startDrag(e)
{
	var event = new Event(e);
	
	activeWindow = event.target;
	while (activeWindow != null && activeWindow.className.indexOf("fulltextwin") == -1)
	{
		activeWindow = activeWindow.parentNode;
	}
	
	lastLeft = activeWindow.style.left;
	lastLeft = parseInt(lastLeft.substring(0, lastLeft.length - 2));
	lastTop = activeWindow.style.top;
	lastTop = parseInt(lastTop.substring(0, lastTop.length - 2));
	mouseX = event.page.x;
	mouseY = event.page.y;
	
	window.addEvent("mousemove", doDrag);
	window.addEvent("mouseup", endDrag);
	
	return false;
}

function doDrag(e)
{
	if (activeWindow)
	{
		var event = new Event(e);
		
		var diffX = event.page.x - mouseX;
		var diffY = event.page.y - mouseY;
		
		lastLeft = lastLeft + diffX;
		lastTop = lastTop + diffY;
		mouseX = event.page.x;
		mouseY = event.page.y;
		
		activeWindow.style.left = lastLeft + "px";
		activeWindow.style.top = lastTop + "px";
	}
}

function endDrag()
{
	activeWindow = null;
	window.removeEvent("mousemove", doDrag);
	window.removeEvent("mouseup", endDrag);
}