/******************************Variables****************************/
//{_brandId: null, _brandDescription: null, _structureId: null, _structureName: "Prueba", polygons: [[]]}
var _structures = [];
var _draw = false;
var _index = 0;
var _drag = false;
var _structure = 0;
var _polygon = 0;
var canvas = document.getElementById("canvas");

/**********************************Router*********************************/
var origin = document.getElementById("helper").getAttribute("data-origin");
switch(origin){
	case 'marking':
		canvas.oncontextmenu = function(){return false;}
		var ctx = canvas.getContext('2d');
		var ctxPoint = canvas.getContext('2d');
		ctx.lineJoin = 'round';
		ctx.lineWidth = 4;
		ctx.fillStyle ="#FFC107";
		ctx.strokeStyle = "#FFC107";
		align();
		setInterval('save(true)',180000)
		/*************************************Canvas Functions**********************************/
		canvas.addEventListener('mousedown', function(evt){//Listen click on canvas
			var mousePos = oMousePos(canvas, evt);
		    if(evt.button != 2 && _draw){
				_structures[_structure].polygons[_polygon].push({x: mousePos.x, y: mousePos.y});
				render(mousePos.x, mousePos.y);
		    }else if(evt.button != 2 && !_draw){
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
					contextMenu(true, evt, _point.index,_point.polygon);
				}else{
					var _line = isLine(mousePos.x, mousePos.y);
					if(_line.bool){
						_structures[_structure].polygons[_line.polygon].splice(_line.index, 0, {x: mousePos.x, y: mousePos.y});
						render();
					}else{
						contextMenu(false, evt);
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
		break;
}


/*************************************Vue Functions**********************************/

function vueMarking(){
	mark = new Vue({
		el: "#rw",
		data: {
			structures: _structures
		},
		updated: function(){
			$('.systems').select2({
				data: jsonStructures
			});
		}
	});
}

/**********************************Other Functions*********************************/

function ajax(type, params, callback){
	$.ajax({
		url: "/anatomy/control",
		method: "POST",
		data: params,
		dataType: type,
	})
	.done(function (data){
		callback(data);
	})
	.fail(function(err){
		console.log(err);
	});
}

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

function contextMenu(point, event, _point, polygon){
	if(point){
		$("#contextMenu").html("<li onclick='_structures[_structure].polygons[" + polygon + "].splice(" + (_point-1) + ",1); $(\"#contextMenu\").hide(400); render();'>Eliminar punto.</li><li onclick='_structures[_structure].polygons.splice(" + (polygon) + ", 1); if(_structures[_structure].polygons.length == 0){addPolygon();} $(\"#contextMenu\").hide(400); render();'>Eliminar marca.</li>");	
	}else{
		$("#contextMenu").html("<li onclick='addPolygon(); $(\"#contextMenu\").hide(400); render();'>Añadir marca.</li>");
	}
	$("#contextMenu").css({top: (event.pageY - 20), left: (event.pageX - 5)});
	$("#contextMenu").show(400);
}

function login(event){
	event.preventDefault();
	var _username = $("#txtuserName").val();
	var _password = $("#txtpass").val();
	console.log(_username + "; " + _password);
	ajax("json", {route: "login", username: _username, password: _password}, function(res){
		console.log(res);
		console.log(res.state);
		if(res.state == true){
			window.location.href = "/anatomy";
		}else{
			Materialize.toast('Usuario o contraseña incorrectos, por favor intente nuevamente', 3000, 'rounded');
		}
	});
}

function addPolygon(){
	_structures[_structure].polygons.push([]);
	_draw = true;
	_polygon = (_structures[_structure].polygons.length - 1);
}

function addStructure(){
	_structures.push({_brandId: code(), _brandDescription: null, _structureId: null, _structureName: null, polygons: [[]]});
	_structure = (_structures.length - 1);
	_polygon = 0;
	_draw = true;
	render();
}

function confirm(index, _brandId){
	$("#btnAcept").attr('onclick', 'deleteBrand(' + index + ', "' + _brandId +'");');
	$('#confirm').modal('open');
}

function deleteBrand(index, _brandId){
	$('#confirm').modal('close');	
	ajax("json", {route: "deleteBrand", _brandId: _brandId}, function(res){
		if(res.state){
			_structures.splice(index, 1);
			if(_structures.length == 0){
				addStructure();
			}else{
				_structure = (_structures.length - 1);
				_polygon = (_structures[_structure].polygons.length - 1);
				render();
			}	
			Materialize.toast('Marca borrada exitosamente', 3000, 'rounded');
		}
	});
}

function save(auto){
	var _index = 0;
	_structures.forEach(function(structure){
		if(structure._structureId == null){
			_index ++
		}
	});
	if(_index > 0 && auto == false){
		Materialize.toast('Debe asociar cada marcaje a una estructura', 3000, 'rounded');
	}else{
		var data = JSON.stringify(_structures);
		ajax("json", {route: "save", data: data, imageId: imageId}, function(res){
			if(res.state == true){
				Materialize.toast('Guardado exitoso', 3000, 'rounded');
			}
		});
	}
}

function code(){
	var caracteres = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	var longitud = 30;
	var code = "";
	for (x=0; x < longitud; x++){
		rand = Math.floor(Math.random()*caracteres.length);
		code += caracteres.substr(rand, 1);
	}
	return code;
}


function getStructures(Id_Structure, index){
	index = (index.getAttribute("data-index"));
	ajax("json", {route: "getStructure", Id_Structure: Id_Structure}, function(res){
		if(res.state){
			$("#" + index).empty();
			$("#" + index).select2({
				data: res.data
			});
		}
	});
}

function checkStructure(id, selector){
	var index = (selector.getAttribute("id"));
	_structures[index]._structureId = id;
	_structures[index]._structureName = $("#" + index + " option:selected").text();
}