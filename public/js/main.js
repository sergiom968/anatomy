/******************************Variables****************************/

var _structures = [{_structureId: null, structureName: "Prueba", polygons: [[]]}];
var _draw = true;
var _index = 0;
var _drag = false;
var _structure = 0;
var _polygon = 0;

/**********************************Router*********************************/
var origin = document.getElementById("helper").getAttribute("data-origin");
switch(origin){
	case 'marking':
		var canvas = document.getElementById("canvas");
		canvas.oncontextmenu = function(){return false;}
		var ctx = canvas.getContext('2d');
		var ctxPoint = canvas.getContext('2d');
		ctx.lineJoin = 'round';
		ctx.lineWidth = 6;
		ctx.fillStyle ="#FFC107";
		ctx.strokeStyle = "#FFC107";
		align();
		vueMarking();
		break;
}

/*************************************Canvas Functions**********************************/

canvas.addEventListener('mousedown', function(evt){//Listen click on canvas
	var mousePos = oMousePos(canvas, evt);
    if(evt.button != 2 && _draw){
		_structures[_structure].polygons[_polygon].push({x: mousePos.x, y: mousePos.y});
		render(mousePos.x, mousePos.y);
    }else{
    	var _point = isPoint(mousePos.x, mousePos.y);
		if(_point.bool){
			_polygon = _point.polygon;
			_drag = true;
			_index = _point.index-1;
		}
    }
});

canvas.addEventListener('mouseup', function(evt) {
	_drag = false;
	_index = 0;
});

canvas.addEventListener('contextmenu', function(evt){//Listen canvas contextMenu
	var mousePos = oMousePos(canvas, evt);
	if(_draw){
		_draw = false;
		render();
	}else{
		var _point = isPoint(mousePos.x, mousePos.y);
		if(_point.bool){
			alert("point");
		}else{
			var _line = isLine(mousePos.x, mousePos.y);
			if(_line.bool){
				_structures[_structure].polygons[_line.polygon].splice(_line.index, 0, {x: mousePos.x, y: mousePos.y});
				render();
			}
		}
	}
});

canvas.addEventListener('mousemove', function(evt) {//Listen movement on canvas
	var mousePos = oMousePos(canvas, evt);
	if(_draw){
		render(mousePos.x, mousePos.y);
	}else if(_drag){
		_structures[_structure].polygons[_polygon][_index].x = mousePos.x;
		_structures[_structure].polygons[_polygon][_index].y = mousePos.y;
		render();
	}
});

/*************************************Vue Functions**********************************/

function vueMarking(){
	mark = new Vue({
		el: "#rw",
		data: {
			structures: _structures
		},
		updated: function(){
			 $('.collapsible').collapsible();	
		}
	});
}

/**********************************Other Functions*********************************/

$(window).resize(function(){//Align canvas when window resize
	align();
});

function align(){//Align canvas
	var alt = ($( window ).height()) - ($("nav").height());
	$(".row").css({"height": alt, "margin-top": ((alt-600)/2)});
}

function oMousePos(canvas, evt) {//Detect mouse position
	var rect = canvas.getBoundingClientRect();
	return {
		x: Math.round(evt.clientX - rect.left),
		y: Math.round(evt.clientY - rect.top)
	}
} 

function render(x, y){//Render lines and point on canvas
	ctx.clearRect(0, 0, canvas.width, canvas.height);  
	for( var i = 0; i< _structures[_structure].polygons.length; i++)   {
		ctx.beginPath();
		for(var start = 0; start < _structures[_structure].polygons[i].length; start++){
			ctx.lineTo(_structures[_structure].polygons[i][start].x, _structures[_structure].polygons[i][start].y);
	        if(!_draw){
	        	ctx.arc(_structures[_structure].polygons[i][start].x, _structures[_structure].polygons[i][start].y,3,0,Math.PI*2,true);
	        }
		}
		if(_draw && _polygon == i){
			ctx.lineTo(x, y);
		}
		ctx.closePath();
		ctx.stroke(); 
		if (_draw == false) {//Close de polygon
			ctx.fillStyle = 'rgba(255,165,0,0.5)';
			ctx.fill();
		}
	}
}

function isLine(x, y){
	for (var polygon = 0; polygon < _structures[_structure].polygons.length; polygon++) {
		for (var point = 0; point < _structures[_structure].polygons[polygon].length; point++) {
			var path = new Path2D();
			path.lineTo(_structures[_structure].polygons[polygon][point].x, _structures[_structure].polygons[polygon][point].y);
			if(point == (_structures[_structure].polygons[polygon].length -1)){
				path.lineTo(_structures[_structure].polygons[polygon][0].x, _structures[_structure].polygons[polygon][0].y);
			}else{
				path.lineTo(_structures[_structure].polygons[polygon][point+1].x, _structures[_structure].polygons[polygon][point+1].y);
			}
			if(ctx.isPointInStroke(path, x, y)){
				return {index: point+1, bool: true, polygon: polygon};
				break;
			}
		}
		if(polygon == (_structures[_structure].polygons.length -1)){
			return {bool: false};
		}
	}
}

function isPoint(x, y){
	for (var polygon = 0; polygon < _structures[_structure].polygons.length; polygon++) {
		for (var point = 0; point < _structures[_structure].polygons[polygon].length; point++) {
			var path = new Path2D();
			path.arc(_structures[_structure].polygons[polygon][point].x, _structures[_structure].polygons[polygon][point].y, 3, 0, Math.PI*2, true);
			if(ctx.isPointInPath(path, x, y)){
				return {index: point+1, bool: true, polygon: polygon};
				break;
			}
		}
		if(polygon == (_structures[_structure].polygons.length -1)){
			return {bool: false};
		}
	}
}