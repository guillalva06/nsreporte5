$(document).ready(function(){
		
	function get_periodos(){
		console.log('Periodo');
		$.ajax({
			url : 'consultas_reporte5.php',
			data: {id: 0},
			success : function(data,status){
				console.log('Periodos:'+data+'\nStatus'+status);	
				var periodos = jQuery.parseJSON(data);			
				$.each(periodos, function(p, periodo){
					$('#id_periodos').append($('<option>',{
						value: periodo.id,
						text: periodo.name
					}));
				});				
			}
		});
	};

	$('#id_periodos').change(function(){
		console.log('Cambio Periodos');
		console.log($('#id_periodos').val());
		get_variantes($('#id_periodos').val());
	});

	get_periodos();

	$('#id_variantes').change(function(){
		//get_variantes(1);
		console.log('Cambio Variante');
		console.log($('#id_variantes').val());
	});

	function get_variantes(parent){
		console.log('Variante');
		$.ajax({
			url: 'consultas_reporte5.php',
			data: {id: 1,
				parent:parent},
			success: function(data,status){
				console.log('Variantes:'+data);
				var variantes = jQuery.parseJSON(data);
				$('#id_variantes').empty();
				$('#id_variantes').append($('<option>',{
						value: '0',
						text: 'Seleccione una variante'
					}));
				$.each(variantes,function(v,variante){
					$('#id_variantes').append($('<option>',{
						value: variante.id,
						text: variante.name
					}));
				});
			}
		});
	};

	function get_componentes(namecategory, path){
		$.ajax({
			url:'consultas_reporte5.php',
			data:{id: 2,
				namecategory: namecategory,
				path: path},
			success: function(data, status){
				console.log('Componentes '+data);
				var componentes = jQuery.parseJSON(data);
				$('#id_componentes').empty();
				$('#id_componentes').append($('<option>',{
						value: '0',
						text: 'Seleccione un componente'
					}));
				$.each(componentes,function(c,componente){
					$('#id_componentes').append($('<option>',{
						value: componente.id,
						text: componente.name
					}));
				});
			}
		});
	}


	$('#id_nombre_categoria').keyup(function(){
		console.log($('#id_nombre_categoria').val());
		var periodo = $('#id_periodos').val();
		var variante = $('#id_variantes').val();		
		var path = '/';
		if(periodo!=0){
			path += periodo+'/';
			if(variante!=0){
				path += variante+'/';
			}
		}
		console.log(path);
		get_componentes('%'+$('#id_nombre_categoria').val()+'%',path+'%');
	});

	$('#id_componentes').change(function(){
		var componentid = $('#id_componentes').val();
		console.log(componentid);
		if(componentid!=0){
			$('input[name=componente_id]').attr('value', $("#id_componentes").val());
			$('#mform1').submit();
		}
	});

});