//Author Molnar Raul Alexandru

function grayscale(src){
	
		var graycanvas = document.createElement('canvas');
		var ctx = graycanvas .getContext('2d');
		var imgObj = new Image();
		imgObj.src = src;
		graycanvas .width = imgObj.width;
		graycanvas .height = imgObj.height; 
		ctx.drawImage(imgObj, 0, 0); 
		var canvasPixels = ctx.getImageData(0, 0, graycanvas .width, graycanvas .height);
		
			for(var y = 0; y < canvasPixels.height; y++){
			for(var x = 0; x < canvasPixels.width; x++){
				
				var i = (y * 4) * canvasPixels.width + x * 4;
				var average= (canvasPixels.data[i] + canvasPixels.data[i + 1] + canvasPixels.data[i + 2]) / 3;
				canvasPixels.data[i] = average; 
				canvasPixels.data[i + 1] = average; 
				canvasPixels.data[i + 2] = average;
			}
		}
		ctx.putImageData(canvasPixels, 0, 0, 0, 0, canvasPixels.width, canvasPixels.height);
		return graycanvas .toDataURL();
    }

(function($) {
  $.fn.grayScale = function()
  {	
var elem=$(this);
	var source=elem.attr('src');
	elem.attr("src",grayscale(source));
}
})(jQuery);