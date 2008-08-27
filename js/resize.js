var activeContent;
var compX = 0;
var compY = 0;

function startResize(e)
{
	var event = new Event(e);
	
	activeWindow = event.target;
	while (activeWindow != null && activeWindow.className.indexOf("fulltextwin") == -1)
	{
		activeWindow = activeWindow.parentNode;
	}
	
	activeContent = $E(".fulltextcontent", activeWindow);
	
	lastWidth = parseInt(activeWindow.offsetWidth);
	lastHeight = parseInt(activeContent.offsetHeight);
	mouseX = event.page.x;
	mouseY = event.page.y;
	
	activeContent.style.height = lastHeight + "px";
	activeContent.style.maxHeight = '';
	
	window.addEvent("mousemove", doResize);
	window.addEvent("mouseup", endResize);
	
	return false;
}

function doResize(e)
{
	if (activeWindow)
	{
		var event = new Event(e);
		
		var diffX = event.page.x - mouseX;
		var diffY = event.page.y - mouseY;
		
		if (compX > 0 && compX > diffX)
		{
			compX -= diffX;
			diffX = 0;
		}
		else if (compX > 0)
		{
			diffX -= compX;
			compX = 0;
		}
		
		if (compY > 0 && compY > diffY)
		{
			compY -= diffY;
			diffY = 0;
		}
		else if (compY > 0)
		{
			diffY -= compY;
			compY = 0;
		}
		
		lastWidth = lastWidth + diffX;
		lastHeight = lastHeight + diffY;
		
		if (lastWidth < 175)
		{
			compX += 175 - lastWidth;
			lastWidth = 175;
		}
		
		if (lastHeight < 100)
		{
			compY += 100 - lastHeight;
			lastHeight = 100;
		}
		
		mouseX = event.page.x;
		mouseY = event.page.y;
		
		activeWindow.style.width = lastWidth + "px";
		activeContent.style.height = lastHeight + "px";
	}
}

function endResize()
{
	activeWindow = null;
	activeContent = null;
	compX = 0;
	compY = 0;
	window.removeEvent("mousemove", doResize);
	window.removeEvent("mouseup", endResize);
}