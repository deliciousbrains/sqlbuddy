/*

SQL Buddy - Web based MySQL administration
http://www.sqlbuddy.com/

animation.js
- animation functions for the sidemenu

MIT license

2008 Calvin Lough <http://calv.in>

*/

var animationStack = [];

function addAnimation(id, finish)
{
	var elem = $(id);
	
	//remove duplicates
	for (var i in animationStack)
	{
		if (animationStack[i][0] == elem)
			animationStack.splice(i, 1);
	}
	
	var start = elem.offsetHeight;
	
	var change = finish - start;
	
	var totalFrames = 15;
	
	if (window.gecko)
		totalFrames -= 5;
	
	animationStack.push([elem, start, change, 0, totalFrames]);
	if (animationStack.length == 1)
		animate();
}

function animate()
{
	var j, elem, start, change, currentFrame, totalFrames;
	for (var i = 0; i < animationStack.length; i++)
	{
		
		j = parseInt(i);
		
		elem = animationStack[j][0];
		start = animationStack[j][1];
		change = animationStack[j][2];
		animationStack[j][3] += 1;
		currentFrame = animationStack[j][3];
		totalFrames = animationStack[j][4];
		
		var newHeight = sineInOut(currentFrame, start, change, totalFrames);
		
		elem.style.height = newHeight + "px";
		
		if (currentFrame >= totalFrames)
		{
			animationStack.splice(j, 1);
			
			//if the menu is expanded, take off the explicit height attribute
			if (elem.style.height != "0px")
			{
				elem.style.height = '';
			}
		}
	}
	if (animationStack.length > 0)
		setTimeout('animate()', 25);
}

function yellowFade(el, curr)
{	
	if (!curr)
		curr = 175;
	
	el.style.background = 'rgb(255, 255, '+ (curr+=3) +')';
	
	if (curr < 255)
			setTimeout(function(){ yellowFade(el, curr) }, 25);
}

function sineInOut(t, b, c, d)
{
	return -c/2 * (Math.cos(Math.PI*t/d) - 1) + b;
}