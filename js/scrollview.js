var animationTimer;
var currentFrame = 0;
var totalFrames = 27;

function init()
{
	if ($('frame') && $('frame').scrollLeft != 0)
		$('frame').scrollLeft = 0;
}

function scrollView(paneLoc)
{
	if (animationTimer != null)
	{
		clearInterval(animationTimer);
		animationTimer = null;
	}
	
	var start = $('frame').scrollLeft;
	var finish = ((682 + 5) * paneLoc);
	var change = finish - start;
		
	currentFrame = 0;
	
	if (start != finish)
		animationTimer = setInterval("doAnimate(" + start + "," + change + ")", 15);
	
	var controls = $('framecontrols').getElementsByTagName("li");
	for (var i=0; i<controls.length; i++)
	{
		controls[i].className = controls[i].className.replace("selected", "");
	}
	controls[paneLoc].className += " selected";
}

function doAnimate(start, change)
{
	currentFrame++;
	
	if (currentFrame >= totalFrames)
	{
		clearTimeout(animationTimer);
		animationTimer = null;
	}
	
	var newLeft = sineInOut(currentFrame, start, change, totalFrames);
	
	$('frame').scrollLeft = newLeft;
}

function sineInOut(t, b, c, d)
{
	return -c/2 * (Math.cos(Math.PI*t/d) - 1) + b;
}