<?php
/*
*Arquivo de funções do Nopadi
*Author: Paulo Leonardo Da Silva Cassimiro
*/


function painel_search($url,$names_list,$options=null,$options_settings=array())
{
	$form = null;
	$option_person = null;
	
	$gets = $_GET;
	
	$s_key = isset($gets['nsn']) ? $gets['nsn'] : null;
	$s_val = isset($gets['nsv']) ? $gets['nsv'] : null;
	$s_typ = isset($gets['nst']) ? $gets['nst'] : null;
	
	foreach($names_list as $k=>$v)
	{
		$k = explode(':',$k);
		$value = $k[0];
		$type = isset($k[1]) ? $k[1] : 'text';
		
		$selected = ($s_key == $value) ? 'selected' : null; 
		
	    $option_person .= "<option value='{$value}' type='{$type}' {$selected}>{$v}</option>";	
	}
	
	$break = null;
	if(is_array($options))
	{
	 if(isset($options['break']))
	 {
		 $break = $options['break'];
		 $break_options = null;
		 
		 foreach($break as $k=>$v)
		 {
			 $break_options .= "<option value='{$k}'>{$v}</option>";
		 }
	  
	     $break = "<div class='row'>
            <div class='col-4'>
            <select name='nsb' class='form-select'>
				{$break_options}
			</select>
            </div>
            <div class='col-4'>
              <input type='date' name='nsi' class='form-control'>
            </div>	
			<div class='col-4'>
              <input type='date' name='nse' class='form-control'>
            </div>
            </div>";
       }			
	}
	
	if(isset($options['break'])){ unset($options['break']); }
	
	$options_content = null;
	$options_content .= "<div class='row'>"; 	
	foreach($options as $k=>$v)
	{
		$k = explode(':',$k);
		$label = isset($k[1]) ? $k[1] : $k[0];
		$k = $k[0];
		
		if(is_array($v))
		{
	      $options_content_values = null;	
		  	  
		  foreach($v as $k2=>$v2)
		  {

			$selected = null;
			if(array_key_exists($k,$options_settings))
			{
			   $selected = $options_settings[$k] == $k2 ? 'selected' : $selected;
			}  
			  
			 $options_content_values .= "<option value='{$k2}' {$selected}>{$v2}</option>"; 
		  }
		  $options_content .= "<div class='col-6'>
		   <lable class='form-label'>{$label}</lable>
		   <select class='form-select' name='{$k}'>{$options_content_values}</select></div>";
		}else{
			$options_content .= "<input type='hidden' name='{$k}' value='{$v}'>";
		}
	}
	$options_content .= "</div>"; 	

	$url = url($url);
	
	/*Types de pesquisa*/
	$types_array_list = array(
	'e'=>'É',
	'ne'=>'Não é',
	'c'=>'Contém',
	'nc'=>'Não contém',
	'cc'=>'Começa com',
	'tc'=>'Termina com',
	'mi'=>'Menor que',
	'mii'=>'Menor ou igual a',
	'mx'=>'Maior que',
	'mai'=>'Maior ou igual a',
	'v'=>'Está vazio',
	'nv'=>'Não está vazio'
	);
	
	$types_array_options = null;
	
	foreach($types_array_list as $keyx=>$valx){
		$selected = ($s_typ == $keyx) ? 'selected' : null;
		$types_array_options .= "<option value='{$keyx}' {$selected}>{$valx}</option>";
	}
	
	$form .= "<form action='{$url}' method='GET'>
	             <div class='row'>
                 <div class='col-4'>
				   <lable class='form-label'>Campo</lable>
                   <select name='nsn' class='form-select' id='np-painel-search-name'>
				     {$option_person}
				   </select>
                 </div>
				  <div class='col-3'>
				  <lable class='form-label'>Condição</lable>
                   <select name='nst' class='form-select' id='np-painel-search-type'>
				     {$types_array_options}
				   </select>
                 </div>
                 <div class='col-5'>
				   <lable class='form-label'>Valor</lable>
                   <input type='text' id='np-painel-search-value' name='nsv' class='form-control' value='{$s_val}'>
                 </div>	
                </div>	
				{$break}
				{$options_content}
			   <div class='row'>
               <div class='col-12 text-right'><br>
                   <input type='submit' class='btn btn-outline-primary' value='Aplicar filtro'>
               </div>
               </div>			   
             </form>";
	
	$form .= "
	<script>
	     $(function(){
			 $('#np-painel-search-name').click(function(){
				 let painelInputType = $('#np-painel-search-name :selected').attr('type'); 
				 $('#np-painel-search-value').attr('type',painelInputType);
			 });
			 
		 });
	</script>";
	
	return $form;
}

function painel_form_filters()
{
   	
}

function painel_alert($msg,$type='success')
{
   return "<div class='alert alert-{$type} alert-dismissible' role='alert'>{$msg}<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";	
}

function painel_filters($url,$filters)
{
	$url = url($url);
	$form = "<form id='np-form-filters' class='col m12 row'>";
	foreach($filters as $name=>$filter)
	{
  $name_p = explode('|',$name);
  $name = $name_p[0]; 
  $label = isset($name_p[1]) ? $name_p[1] : $name;
  
  $check = has_filter($name) ? 'checked="checked"' : null;
		
  $form .= "<div class='col m12'>
                <label><input type='checkbox' id='{$name}' class='check' {$check}/><span>{$label}</span></label>
            </div>";
  $form .= "<div id='{$name}-container' class='col m12'>
                <label>{$label}</label>
							{$filter}
					</div>";
	}
	
	$form .= "<div class='col m12'><input type='submit' value='Aplicar filtro' class='btn-small'></div>";
	
	$form .= "</form>";
	
	
	/*Script*/
	$form .= "<script>
               $(function(){
   
   let form = $('#np-form-filters');
   
   $('.check:not(.check:checked)').each(function(index){   
						let containerIdSelector = '#' + $('.check:not(.check:checked)').eq(index).attr('id') + '-container';
						let input = $(containerIdSelector + ' ' +'input' ); 

						input.prop('disabled', true);

						$(containerIdSelector).hide();

   });

   $('.check:checked').each(function(index){
						let containerIdSelector = '#' + $('.check:checked').eq(index).attr('id') + '-container';
						let input = $(containerIdSelector + ' ' +'input' ); 

						input.prop('required', true);
    });

    $('.check').click(function(){
						let containerIdSelector = '#' + $(this).attr('id') + '-container';
						let input = $(containerIdSelector + ' ' +'input');
						let disabledToggle = !input.prop('disabled');

						input.prop('required',true);
						input.prop('disabled', disabledToggle); 

						$(containerIdSelector).slideToggle(400);					
					});
   
   form.submit(function(){
			
      let gets = form.serialize();
	  
	  if(gets.length > 2){
		   window.location.href = '{$url}?'+gets;
	  }else{
		alert('Selecione um campo.');
	  }
	  
      return false;
	  
   });
   
  
   });
        </script>";
	
	
	return $form;
}

/*Componete SELECT2*/
function select2_search($url,$default=null,$args=null,$method='POST',$observe=null,$multiple=false,$required=false)
{	
 $id = 'select2-'.str_url($url.$default);
 $id = str_ireplace('|','',$id);
 
 $url = url($url);
 $params = null;

 if(is_array($args))
 {
    foreach ($args as $key => $value) 
    {
    	$params .= ",{$key}:'{$value}'";
    }
 }
	 
	 $required = $required ? 'required' : null;

     $default = explode('|',$default);
	 $name = $default[0];

	 if(isset($default[1])){
	 	 $key =  $default[1];
	 	 $val = isset($default[2]) ? $default[2] : 'Padrão';
	     $default =  "<option value='{$key}'>{$val}</option>";
	 }else{
	 	 $default = null;
	 }

$observe_id = 'observe_id';
$observe_name ='observe_name';	

if(!is_null($observe))
{
	$observe = explode('|',$observe);
	$observe_id = $observe[0];
	if(isset($observe[1])) $observe_name = $observe[1];
}
$token = csrf_token();
$multiple = $multiple ? "multiple='multiple'" : null;

$select  = "<select {$multiple} {$required} id='{$id}' name='{$name}' class='select2 form-select'>{$default}</select>";
$select .= "<script type='text/javascript'>
            $(document).ready(function(){
			
             let obVal = null;
			 let observeSelect = $('#{$observe_id}');
             obVal = observeSelect.val();

			observeSelect.change(function(){
				obVal = $('option:selected', this).val();
			});

            $('#{$id}').select2({
				width:'100%',
                ajax: {
                    url: '{$url}',
                    type: '{$method}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                    var query = {
						 $observe_name:obVal,
						 _token: '{$token}',
                         search: params.term,
                         page: params.page{$params}
                         }
                      return query;
                    },
                    processResults: function (response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
});
</script>";

return $select;

}

function select2_multiple($url,$default=null,$args=null,$method='post',$observe=null)
{
	return select2_search($url,$default,$args,$method,$observe,true);
}

function hello_list_search($array){
	$array = id_value($array, 'name');
	$id = get('id');
	$search = get_search();

	if($id && !$search){
		$array = $array ? $array[$id] : 'no_value_found';
		hello($array);
	}elseif($search){
      if($array){ 
		 foreach($array as $key=>$val){ hello_list($key,$val,$id); }
	  }else{ hello('no_value_found'); }
    }else{ hello('no_value_found'); }
}

function hello_list($id,$name,$id_local=null){
		echo '<ul class="np-ul">';
		if($id == $id_local){
			echo'<li title="'.$name.'" class="np-btn-option np-border-bottom np-link np-hover-text-blue"  value="'.$id.'"><i class="material-icons np-text-green">done</i>'.$name.'</li>';
		}else{
			echo '<li title="'.$name.'" class="np-btn-option np-border-bottom np-link np-hover-text-blue" value="'.$id.'"><i class="material-icons np-text-gray">done</i>'.$name.'</li>';
		}
        echo '</ul>';
	}

function form_search_list($config){
    
	$title = isset($config['title']) ? $config['title'] : null;
	$url = isset($config['url']) ? $config['url'] : null;
	$name = isset($config['name']) ? $config['name'] : null;
	$id = isset($config['id']) ? $config['id'] : $name;
	$value = isset($config['value']) ? $config['value'] : null;
	
	$name_form = 'np-form-search-radio-';
	$content = $name_form.'content-'.$id;
	$form = $name_form.'form-'.$id;
	$input = $name_form.'input-'.$id;
	$input_for = $name_form.'input-for-'.$id;
	$input_search = $name_form.'text-'.$id;
	$modal = $name_form.'modal-'.$id;
	$progress =  $name_form.'progress-'.$id;
	$not_found = $name_form.'not_found-'.$id;

    $out = '<div class="input-field col m12 s12">
          <a href="#'.$modal.'" class="modal-trigger prefix"><i class="material-icons">search</i></a>
          <input type="text" id="'.$input.'">
          <label>'.$title.'</label>
		  <input value="'.$value.'" type="hidden" name="'.$name.'">
	      <span id="'.$not_found.'" class="np-text-red np-small">'.text(':no_value_found').'</span>
        </div>';

$out .= '<div class="modal" id="'.$modal.'">
	         <div class="modal-content">
                <div class="container">
				    <h6><i class="material-icons">search</i>'.$title.'</h6>
				    <input type="text" id="'.$input_search.'" placeholder="'.lang('search').'" class="np-input np-border np-round">
					  <div id="'.$progress.'" class="preloader-wrapper big active">
                       <div class="spinner-layer spinner-blue-only">
                       <div class="circle-clipper left">
                       <div class="circle"></div>
                      </div><div class="gap-patch">
                      <div class="circle"></div>
                      </div><div class="circle-clipper right">
                      <div class="circle"></div>
                     </div>
                      </div>
                      </div>
                 <div id="'.$content.'">
				 </div>
                 </div>
				 <div class="modal-footer">
                   <a href="#!" class="waves-light btn modal-close red">'.lang('close').'</a>
                  </div>
              </div>
          </div>
		  <script>
		  $(function(){
			  var inputText = $("#'.$input.'");
			  var input = $("#'.$input_search.'");
			  var content = $("#'.$content.'");
			  var progress = $("#'.$progress.'");
              progress.hide();

			  input.keypress(function(){
				  var input = $(this).val();
				  if(input.length >= 1){
					$.ajax({
						"url":"'.url($url).'",
						"type":"get",
						"data":{search:input,id:$("input[name='.$name.']").val()},
						beforeSend:function(){ progress.show(); },
						complete:function(){ progress.hide(); },
						success:function(data){ 
							if(data == "no_value_found"){
								var not = "<h6 class=\"np-text-red np-center np-animate-opacity\"><i class=\"material-icons\">info</i>'.text(':no_value_found').'</h6>";
								content.html(not);
							  }else{
								content.html(data);
								content.on("click",".np-btn-option",function(){
								   var title = $(this).attr("title");
								   var value = $(this).attr("value");
								   $("input[name='.$name.']").val(value);
								   inputText.val(title);
								   $("#'.$modal.'").hide();
								   npLoadRadioinput();
								});
							  } 
						}
					});  
				  }
			  });
			function npLoadRadioinput(){
				var not = $("#'.$not_found.'");
				not.hide();
				var id = $("input[name='.$name.']");
				var inp = id.val();
				inp = inp.trim();
				if(inp.length >= 1)
				{
						$.ajax({
						"url":"'.url($url).'",
						"type":"get",
						"data":{id:id.val()},
						success:function(data){
							 if(data == "no_value_found"){
                               not.show();
							   inputText.addClass("np-border-red");
							 }else{
                                inputText.val(data);
								not.hide();
								inputText.removeClass("np-border-red");
							 } 
						}
					}); 
				}
			}
			npLoadRadioinput();
		  });
		  </script>';
    return $out;
}


function form_switch($name=null,$value=0,$id=null){
	$value = trim($value);
	$value = $value == 'on' || $value == 1 ? 'on' : 'off';
	$checked = ($value == 'on') ? ' checked' : null;
	return '
	<!-- Switch -->
  <div class="switch">
    <label>
      <span class="red-text">'.lang('off').'</span>
      <input name="'.$name.'" type="checkbox"'.$checked.' id="'.$id.'">
      <span class="lever"></span>
      <span class="green-text">'.lang('on').'</span>
    </label>
  </div>
	';
}

function form_checkbox($name=null,$value=0,$id=null){
	return form_switch($name,$value,$id);
}

function modal_open_click($id='np-modal-01',$type=true){
	$id = 'np-modal-'.$id;
	$type = $type ? 'block' : 'none';
	return 'onclick="document.getElementById(\''.$id.'\').style.display=\''.$type.'\'"';
}

function btn_delete($id,$content=null){
	$npId = 'np-modal-delete-'.$id;
    return '<a type="button" class="np-link np-hover-text-red" onclick="document.getElementById(\''.$npId.'\').style.display=\'block\'"><i class="material-icons">delete</i></a>

<!-- The Modal -->
<div id="'.$npId.'" class="np-modal">
  <div class="np-modal-content np-round np-animate-top" style="max-width:510px">
    <div class="np-container">
      <span onclick="document.getElementById(\''.$npId.'\').style.display=\'none\'"
      class="np-button np-hover-white np-hover-text-red np-display-topright np-round np-small np-padding-small">
	  <i class="material-icons">close</i></span>
	  <h3><i class="material-icons">delete</i>'.text(':delete').'</h3>
      <div class="np-padding np-delete-loading-'.$id.'">'.$content.'</div>
	  <span onclick="document.getElementById(\''.$npId.'\').style.display=\'none\'"
      class="np-button np-text-red">'.text(':cancel').'</span>
	 <span class="np-btn-delete np-button np-text-green" id="'.$id.'">'.text(':confirm').'</span>
    </div>
  </div>
</div> ';
}

function script_delete($route){
 return '<script>
$(function(){
  $(".np-btn-delete").click(function(){
     var id = $(this).attr("id"); 
     var msg = $(".np-delete-loading-"+id);
	 var item = $(".np-item-"+id);
	 
     $.ajax({
     url : "'.url($route).'",
     type : "delete",
     data : {id:id},
     beforeSend : function(){
       msg.html(\'<div class="np-center"><div class="np-progress np-white"><div class="np-indeterminate np-red"></div></div><h3 class="np-animate-fading np-text-red">'.text(':processing').'</h3></div>\');  
     },
	 success : function(data){
         if(data != "error"){
		    msg.html(data);
			item.hide("fast");
		 }else{
			 msg.html(data);
		 }
     }
    }); 
     return false;
  });
});
</script>';
}

/*Função para abrir um formulário*/
function form_open($title=null,$url=null,$method='POST',$id_form=null){
	
	
	 $met = explode(':',$method);
	 $method = isset($met[1]) ? $met[1] : $met[0];
	 $debug = (isset($met[1]) && $met[0] == 'debug') ? true : false;
	
	 $res = explode(':',$title);
	 $title = isset($res[1]) ? $res[1] : $res[0];
	 $reset = (isset($res[1]) && $res[0] == 'reset') ? '$("input").val("");' : null;
	
	 $id_form = !is_null($id_form) ? "-{$id_form}" : null;
	
	  $title = text($title);
	  $url = url($url);
	  $form = '<script>
	   $(document).ready(function() { 
            var form = $("#np-form-open'.$id_form.'"); 
			form.submit(function(){ 
			var values = form.serialize();
			var loading = document.createElement("div");
            loading.innerHTML = "<div class=\"preloader-wrapper big active\"><div class=\"spinner-layer spinner-blue-only\"><div class=\"circle-clipper left\"><div class=\"circle\"></div></div><div class=\"gap-patch\"><div class=\"circle\"></div></div><div class=\"circle-clipper right\"><div class=\"circle\"></div></div></div></div>";
					$.ajax({
					url : "'.$url.'",
                    type :"'.$method.'",
                    data :values,
					dataType: "json",
                    beforeSend : function(){ 
                         
						  swal({
                            content: loading,
							title: "Salvando informações...",
                            button: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                            });

					},
					error:function(data){
						 console.log(data);
						 swal("'.text(':error').'","Erro de servidor.", "error");
					},
					success : function(data){';
					      
						      if($debug){
								  $form .= ' swal("xxx", "OK", "success"); $("#np-painel-form-local'.$id_form.'").html(data); '; 
							  }else{
                              $form .= 'var type = data.type;
							  var msg  = data.msg;
							  if(type == "success"){
								  swal("'.text(':success').'", msg, "success"); '.$reset.'
							  }else if(type == "error"){
								   swal("'.text(':error').'", msg, "error");
							  }else{
								 swal("'.text(':info').'", msg, "info") 
							  }';}
							  
							  
					$form .= '}
				});
				return false;
			});
      });
	  </script>
	  <form id="np-form-open'.$id_form.'" action="'.url($url).'" method="'.$method.'" class="container row">
	  <h5>'.$title.'</h5><div id="np-painel-form-local'.$id_form.'"></div>';
	  return $form;
}

/*Função para fechar um formulário*/
function form_close($title='save'){
	$btns = '<div style="margin-top:40px;margin-bottom:40px" class="col m12 s12"><button type="submit" class="waves-effect waves-light btn-small right gradient-45deg-green-teal"><i class="material-icons right">save</i>'.text(':'.$title).'</button>
	<a onclick="window.history.go(-1);" class="waves-effect waves-light btn-small left gradient-45deg-red-pink"><i class="material-icons left">arrow_back</i>Voltar</a>';
	return '<div id="np-form-msg"></div>'.$btns.'</div></form>';
}


/*Função para abrir um formulário*/
function form_modal($id='np-form-modal',$url=null,$method='POST'){
	  $url = url($url);
	  $form = '
	  <script>
	   $(document).ready(function() {
            var formModal = $("#'.$id.'"); 
			formModal.submit(function(){ 
			var values = formModal.serialize();
				$.ajax({
					 url : "'.$url.'",
                     type :"'.$method.'",
                     data :values,
                    beforeSend : function(){ $("#np-top-loading").show(); $("#np-main-menu-top").addClass("np-animate-fading"); },
					success : function(data){ $("#'.$id.'-msg").html(data); },
					complete : function(){ $("#np-top-loading").hide(); $("#np-main-menu-top").removeClass("np-animate-fading"); }
				});
				return false;
			});
      });
	  </script>
	  <form id="'.$id.'" action="'.$url.'" method="'.$method.'" class="np-row">
	  <div id="'.$id.'-msg"></div>';
	  return $form;
}

/*Função para abrir um formulário*/
function form_input($params=null,$required=false){
	    //tipo
		$type = isset($params['type']) ? $params['type'] : 'text';
		$label = isset($params['label']) ? text($params['label']) : null;
		$value = isset($params['value']) ? $params['value'] : null;
		$name = isset($params['name']) ? $params['name'] : null;
		$options = isset($params['options']) ? $params['options'] : array();
	    //pleceholder
		$placeholder = isset($params['placeholder']) ? text($params['placeholder']) : null;
		//required
		$required = $required ? 'required' : null;
		
		$class = $required == 'required' ? 'input' : 'input';
		
		if($type == 'select'){
			 $option = null;
			 foreach($options as $key=>$val)
			 {
				$option .= '<option value="'.$key.'">'.$val.'</option>';  
			 }
			 
			 return '<label>'.$label.'</label>
	   <select type="'.$type.'" class="'.$class.'" name="'.$name.'" '.$required.'>'.$option.'</select>';
		}else{
			 return '<label>'.$label.'</label>
	   <input type="'.$type.'" class="'.$class.'" value="'.$value.'" placeholder="'.$placeholder.'" name="'.$name.'" '.$required.'>';
		}

	   return '<label>'.$label.'</label>
	   <input type="'.$type.'" class="'.$class.'" value="'.$value.'" placeholder="'.$placeholder.'" name="'.$name.'" '.$required.'>';
}


function menu_sidebar($config){
	
	$app = isset($config['app']) ? $config['app'] : null;
	$icon = isset($config['icon']) ? $config['icon'] : null;
	$route = isset($config['route']) ? $config['route'] : null;
	$title = isset($config['title']) ? $config['title'] : null;
	
	return '<a href="#" class="np-bar-item np-button np-padding"><i class="material-icons">supervisor_account</i>'.$title.'</a>';
}

/*Função de alerta de mensagens*/
function alert($msg,$type='info'){
	
	$msg = lang($msg);
	
	if($type == 'success'){
		 $msg = (['msg'=>$msg,'type'=>'success']);
	}elseif($type == 'danger' || $type == 'error'){
		  $msg = (['msg'=>$msg,'type'=>'error']);
	}else{
		 $msg = (['msg'=>$msg,'type'=>'info']);
	}
	return json_encode($msg);
}
function view_header($title,$callback=null){
	$header = '<div class="center white" style="z-index:3;width:100%;position:fixed;left:0px;top:0px;padding:5px;min-height:50px">';
	$header .= '<h5>'.$title.'</h5>';
	
	if(is_array($callback)){
	foreach($callback as $item){
		$header .= $item;
	  }
	}else{
		$header .= $callback;
	}
	
  $header .= '</div><br>';
  return $header;
}

/*Cria um botão de envio com janela de confirmação*/
function painel_btn_confirm($config)
{
	$btn_id = isset($config['id']) ? $config['id'] : 'painel-btn-id';
	$btn_text = isset($config['text']) ? $config['text'] : 'Btn Text';
	$btn_color = isset($config['color']) ? $config['color'] : 'blue';
	
	$html = '<a href="#" id="'.$btn_id.'" class="waves-effect waves-light btn-small '.$btn_color.'">'.$btn_text.'</a>
	<script>
	    $(function(){

		$("#'.$btn_id.'").click(function(){
			
			swal({
            title: "Informe a descrição: ",
            icon: "warning",
            content:{
                element: "input",
                attributes: {
                    placeholder: "Escreva aqui...",
                    type: "text"
                }
            },
            buttons:{
                cancel:"Cancelar",
                confirm: {
                    text: "Enviar",
                    value: true,
                    visible: true,
                    className: "green z-depth-2",
                    closeModal: false
                }
            }
        }).then((value) => {
            if(value.length < 5){
                swal({
                    title: "Erro ao enviar",
                    text: "Sua descrição é muito curta.",
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "red z-depth-2"
                    },
                });
            }else{
                //Aqui sera realizado o AJAX 
                //A resposta do AJAX deve ser atribuido a esta variavel
                
                return true;
            }
        }).then((response) => {
            if(response == true){
                swal({
                    title: "Enviado com sucesso",
                    icon: "success",
                    button: {
                        text: "OK",
                        className: "green z-depth-2"
                    },
                });
            }else if(response == false){
                swal({
                    title: "Erro ao enviar",
                    text: "Erro ao se comunicar com o servidor.",
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "red z-depth-2"
                    },
                });
            }
        });
			
		});
		
		});
    </script>';
	return $html;
}



/*Cria um botão*/
function painel_btn($btn_name,$url=null,$class_more=null)
{
	if($btn_name == 'pdf'){
		return "<a target='_blank' href='".url('np-painel-pdf-export/np-painel-view')."' class='btn-pdf btn-small red'  title='Converter em PDF'>PDF</a>";
		
	}elseif($btn_name == 'doc'){
		return "<a href='".url('np-painel-doc-export/np-painel-view')."' class='btn-doc btn-small blue'  title='Converter em documento'>DOC</a>";
		
	}
	elseif($btn_name == 'xls'){
		return "<a href='".url('np-painel-xls-export/np-painel-view')."' class='btn-xls btn-small green'  title='Converter em planilha'>XLS</a>";
		
	}
	else{
	$btn_name = explode('|',$btn_name);
	$text = $btn_name[0];
	$class = isset($btn_name[1]) ? $btn_name[1] : 'btn-small';
	$target = isset($btn_name[2]) ? $btn_name[2] : '_self';
	$url = url($url);
	
	return "<a href='{$url}' class='{$class} {$class_more}' target='{$target}'>{$text}</a>";
	}
	
}

function painel_btn_edit($url)
{
  return painel_btn('<i class="material-icons">edit</i>',$url,'btn-edit');
}

function painel_delete($url,$id=null,$class='btn-small')
{
	$url = url($url);
	$size = 'width:18px;height:18px;position:relative;top:5px;left:-5px;';
	$load = '<div id="np-item-view-page-load" class="preloader-wrapper active" style="'.$size.'">
    <div class="spinner-layer spinner-red-only">
      <div class="circle-clipper left">
        <div class="circle"></div>
      </div><div class="gap-patch">
        <div class="circle"></div>
      </div><div class="circle-clipper right">
        <div class="circle"></div>
      </div>
    </div>
  </div>';
	$title = lang('delete');
	$title_load = 'Apagando...';
	$btn = "<a id='np-item-view-page' class='{$class}' title='{$title}'><i class='material-icons'>delete</i></a>
	<a id='np-item-view-page-show' style='display:none' class='{$class}'>{$load}{$title_load}</a><div id='np-item-view-page-show-div'></div>";

$btn .= "<script>
	      $(function(){
		   var btnDelete = $('#np-item-view-page');
		   var div = $('#np-item-view-page-show-div');
		   btnDelete.click(function(){
			  $.ajax({
				  url:'{$url}',
				  data:{id:{$id}},
				  type:'DELETE',
				  dataType:'json',
				  beforeSend:function(){
					 $('#np-item-view-page').hide();
                     $('#np-item-view-page-show').show();					 
				  },
				  success:function(data){
					          $('.np-alert-msg-delete').show('fast'); 
					          $('#np-item-view-page').hide();
                              $('#np-item-view-page-show').hide();
					          var type = data.type;
							  var msg = data.msg;
							  if(type == 'success'){
								  $('.np-painel-item-'+{$id}).hide('fast');
								  $('.np-painel-del-'+{$id}).hide('fast');
								  $('.btn-edit, .btn-pdf,.btn-xls,.btn-doc').hide('fast');
								  div.html('<div style=\"padding:10px\"class=\"card  green lighten-5 green-text col m12\">'+msg+'</div>');
							  }else if(type == 'error'){
								  div.html('<div style=\"padding:10px\"class=\"card  red lighten-5 red-text col m12\">'+msg+'</div>');
							  }else{
                                div.html('<div style=\"padding:10px\"class=\"card  blue lighten-5 blue-text col m12\">'+msg+'</div>');
					    }
				   }
			  });
		   });
	   });
	</script>";
   return $btn;
}

/*Executa ações do sistema ao clicar em um botão*/
function painel_click($btn_name,$url,$id,$class_hide=null)
{
	$btn_id = str_ireplace(['/','.'],'',$btn_name);
	$btn_id = str_url("np-painel-click-{$btn_id}-{$id}");
	$url = url($url);
	
	$btn_name = explode('|',$btn_name);
	
	$first_text = $btn_name[0];
	$last_text = isset($btn_name[1]) ? $btn_name[1] : $first_text;
	$first_class = isset($btn_name[2]) ? $btn_name[2] : null;
	$last_class = isset($btn_name[3]) ? $btn_name[3] : $first_class;
	
	return '
	<a href="#" class="'.trim($first_class.' '.$class_hide).'" id="'.$btn_id.'">'.$first_text.'</a>
	<script>
	     $(function(){
			 
			var btn = $("#'.$btn_id.'");
			btn.click(function(){

			var loading = document.createElement("div");
            loading.innerHTML = "<div class=\"preloader-wrapper big active\"><div class=\"spinner-layer spinner-blue-only\"><div class=\"circle-clipper left\"><div class=\"circle\"></div></div><div class=\"gap-patch\"><div class=\"circle\"></div></div><div class=\"circle-clipper right\"><div class=\"circle\"></div></div></div></div>";
					$.ajax({
					url : "'.$url.'",
                    type :"POST",
                    data :{id:'.$id.',_token:"'.csrf_token().'"},
					dataType: "json",
                    beforeSend : function(){ 
                         
						  swal({
                            content: loading,
							title: "Processando...",
                            button: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                            });

					},
					success : function(data){ 
                              var type = data.type;
							  var msg  = data.msg;
							  if(type == "success"){

								  swal("'.text(':success').'", msg, "success");
								  
							  }else if(type == "error"){
								   swal("'.text(':error').'", msg, "error");
							  }else{
								 swal("'.text(':info').'", msg, "info") 
							  }
					}
				});
				
                $(this).text("'.$last_text.'");
				$(this).removeClass("'.$first_class.'");
				$(this).addClass("'.$last_class.'");
				$("'.$class_hide.'").hide("fast");
			 });
		 });
	</script>';
}

function painel_view($values,$layout=null){
	
	$table1 = "<div class='np-scroll p-2'>";
	$table1 .= "<table class='table table-hover'>";
	$print = null;
	
	foreach($values as $key=>$value)
	{
		$key = explode('|',$key);
		$name = $key[0];
       
	    if(!empty($value)){
		if(isset($key[1]))
		{
			switch($key[1])
			{
				case 'money' : $value = format_money($value); break;
				case 'date' : $value = format($value,'date'); break;
				case 'datetime' : $value = format($value,'datetime'); break;
			}
		  }
		}else
		{
			$value = "----------";
		}
		
		if($name == 'btns'){
			   $btns =null;
				foreach($value as $link=>$text)
				{
                    $text = explode('#',$text);
					$id = isset($text[1])? $text[1] : null;
					$text = $text[0];
					
					$btns .= "<a id='{$id}' class='btn-sm btn' href='{$link}'>{$text}</a>";
				}
                $value = $btns;
				
				
				$table1 .= "<tr style='text-align:center'>
		                     <td colspan='2'><b>{$value}</b><td>
		                   </tr>";
						   
			    
		}else{
			
			$table1 .= "<tr>
		               <td><b>{$name}</b><td>
		               <td>{$value}<td>
		          </tr>";
				  
		  $print .=  "<tr><td><b>{$name}</b><td><td>{$value}<td></tr>";
			
		}
	
				  
	}
	$datetime = format(NP_DATETIME,'datetime');
	$print .=  "<tr><td><b>Horário do sistema</b><td><td>{$datetime}<td></tr>";
	
	$table1 .= "</div>";

	 $action_export = url('api/np/print/table');
	
	 
	 $print .= "<table>{$print}</table>";
	 
	 $table1 .= "<form target='_blank' action='{$action_export}' method='post' id='np-form-export'>
	             <textarea hidden id='np-value-export' name='content'>{$print}</textarea></form>";
				 
	$action_export = url('api/np/xls/table');
	 $table1 .= "<form action='{$action_export}' method='post' id='np-form-xls'>
	             <textarea hidden id='np-value-export' name='content'>{$print}</textarea></form>";
	
	$table1 .= '<script>
	            $(function(){
					 /*Função para exportação de dados via PDF ou Excel*/
	$("#np-btn-pdf").click(function()
	{
       $("#np-form-export").submit();
	});
	
	$("#np-btn-xls").click(function()
	{
       $("#np-form-xls").submit();
	});
				});
	          </script>';
    

	return $table1;
}


function painel_title($title,$btns=null,$modal_content=null,$title_modal=null)
{
	set_var('np_table_title',$title);
	$title_modal = is_null($title_modal) ? 'Filtro' : $title_modal;
	$html = "<div class='fixed-header'>
        <div class='d-flex align-items-center'>
            <button class='btn-back' onclick='history.go(-1);'><i class='material-icons'>chevron_left</i></button>
            <h2 class='header-title'>{$title}</h2>
        </div>
        
        <div class='d-flex'>";
		
	  if(!is_null($modal_content))
	  {
            $html .= "<button  data-bs-toggle='modal' data-bs-target='#exampleModal' class='btn-filter'>
                <i class='material-icons'>menu</i></button>"; 
	  }

      $html .= "<div class='dropdown mx-2'>";
	  
	  if(!is_null($btns))
	  {
          $html .= "<button  class='btn btn-primary dropdown-toggle py-1 p-2' href='#' role='button' id='dropdownMenuLink' data-bs-toggle='dropdown' aria-expanded='false'>
                  Ações
	          </button>";
	  }

       $btn_item = null;		
		 if(is_array($btns))
		 {
			foreach($btns as $key=>$text){
		      
              $key = explode('|',$key);	
		      $id = null;
			  
		      if(substr($key[0],0,1) == '#')
			  {
				  
				$id = str_ireplace('#','',$key[0]);
                $url = $key[0];	
				
			  }else{
				$url = url($key[0]);  
			  }
	
			  $target = isset($key[1]) ? $url : '_self';

		      $btn_item .= "<li><a target='{$target}' id='{$id}' class='dropdown-item' href='{$url}'>{$text}</a></li>";
			} 
		 }
              
         $html .= "<ul class='dropdown-menu' aria-labelledby='dropdownMenuLink'>
		            {$btn_item}
                </ul>
			 </div>
           </div>
       </div>"; 
	
	if(!is_null($modal_content)){
  
    $html .= "<div class='modal fade' id='exampleModal' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
        <div class='modal-dialog modal-dialog-scrollable modal-lg'>
          <div class='modal-content'>
            <div class='modal-header'>
              <h5 class='modal-title' id='exampleModalLabel'>
			    {$title_modal}
			  </h5>
              <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
			   {$modal_content}
            </div>
          </div>
        </div>
    </div>"; 
	}
	
	return $html;
}
/*Loop de array*/
function painel_table($headers,$datas=null,$options=null,$replace=null){
	$tr = '<thead class="custom-thead"><tr>';
	$h = array();
	$print = null;
	
	$action = (isset($options['edit']) || isset($options['view']) || isset($options['select'])) ? true : false;
	
	 $tr .= "<th><input class='form-check-input' type='checkbox' value='' id='np-table-select-all'></th>";
	
	 if($action && isset($options['view']))
	 {
	      $tr .= '<th class="np-hide-element"></th>';
	 }
	 
	$count_header = count($headers);
	
	$print .= "<tr>";
	foreach($headers as $header)
	{
		$legend = explode('|',$header);
		$header = $legend[0];
		$h[] = $header;
	
		$legend = isset($legend[1]) ? $legend[1] : $legend[0];
		$legend = (substr($legend,0,1) == '!') ? substr($legend,1) : text(':'.$legend);
		
		$tr .= '<th>'.$legend.'</th>';
		
		$print .= '<th>'.$legend.'</th>';
	}
	$print .= "</tr>";
	if($action && isset($options['edit']))
	{
	      $tr .= '<th class="np-hide-element"></th>';
	}
	 
	 if($action && isset($options['delete']))
	 {
	      $tr .= '<th class="np-hide-element"></th>';
	 }
	
	$tr .= '</tr></thead><tbody>';
	
	
	
    
    if(count($datas->results) > 0){
    foreach($datas->results as $data){
		$id = $data['id'];
		$class_item = 'np-painel-item-'.$id;
		$tr .= '<tr class="'.$class_item.'">';

        $tr .= "<td><input class='form-check-input np-table-select' type='checkbox' value='{$id}' id='{$id}'></td>";
		
		$print .= "<tr>";
		
		
	    if($action && isset($options['view']))
		{
	      $tr .= '<td class="np-hide-element">
		   <a href="'.get_uri(false).'/'.$id.'" title="'.text(':'.'to_view').'" id="'.$id.'" href="#modal1"><i class="material-icons">remove_red_eye</i></a></td>';
		}
		
		foreach($h as $row)
		{
			//href="'.get_uri(false).'/'.$id.'"
			if(isset($replace[$row])){

				$data[$row] = call_user_func($replace[$row], $data,$id);
				
			}
			
			$data[$row] = empty($data[$row]) && !is_numeric($data[$row]) ? '<span class="orange-text">------</span>' : $data[$row];
			
			
			$print .= "<td>{$data[$row]}</td>";
			
			$tr .= "<td>{$data[$row]}</td>";
			
		}
		
		if($action && isset($options['edit'])){
	      $tr .= '<td class="np-hide-element"><a title="'.text(':'.'edit').'" href="'.get_uri(false).'/'.$id.'/edit"><i class="material-icons">edit</i></a></td>';
		}
		
		if($action && isset($options['delete'])){
	      $tr .= '<td class="np-hide-element"><a class="np-btn-delete-item" id="'.$id.'" title="'.text(':'.'delete').'" href="#"><i class="material-icons">delete</i></a></td>';
		}
		
		$tr .= '</tr>';
		$print .= "</tr>";
		
	}}else{
		$tr .= '<td colspan="'.$count_header.'"><h6 class="red-text">Não há registros para mostrar.</h6></td></tr>';
	}
	
	  //$print = htmlspecialchars($print, ENT_QUOTES);
      $tr .= '</tbody>';
	  
	  
	  $print = "<table class='table'>{$print}</table>";
	  
	  $np_table_title = get_var('np_table_title','Tabela sem título');
	  
	  $user_name = user_name() ? user_name() : "Usuário anônimo";
	  $print = "<h3>{$np_table_title}</h3><hr>{$print}<hr><span>Consulta gerada por: <b>{$user_name}</b>, às ".format(NP_DATETIME,'datetime').".</span>";
	  
	  $action_export = url('api/np/print/table');
	  
	  $tr .= "<form target='_blank' action='{$action_export}' method='post' id='np-form-export'>
	             <textarea hidden id='np-value-export' name='content'>{$print}</textarea>
			  </form>";
	
      $action_export = url('api/np/xls/table');	
	  $tr .= "<form action='{$action_export}' method='post' id='np-form-xls'>
	             <textarea hidden id='np-value-export' name='content'>{$print}</textarea>
			  </form>";
	  
	 
	  
	  $tr .= '
  <!-- Modal Structure -->
      <style>
	   @media (min-width: 993px){
		  .modal-fixed-footer { width: 75% !important; } 
	   }
	   @media (max-width: 600px){
		  .modal-fixed-footer { width: 100% !important; } 
	   }
	  </style>
      <div id="modal1" class="modal modal-fixed-footer">
         <div class="modal-content">
            <div id="np-table-view-more"></div>
         </div>
       <div class="modal-footer">
         <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat ">'.lang('close').'</a>
        </div>
  </div>';
  

	  /*Script JavaScript para deletar item*/
	 $tr .= '<script>
	 let selectAll = $("#np-table-select-all");
	  selectAll.click(function(){
		 if ($(this).is(":checked") ){
               $(".np-table-select").prop("checked", true);
           }else{
               $(".np-table-select").prop("checked", false);
           } 
	  });
	  
	 var loading = document.createElement("div");
     loading.innerHTML = \'<div class="spinner-grow" role="status"><span class="visually-hidden">Loading...</span></div>\';
	 
	 /*Função para exportação de dados via PDF ou Excel*/
	$("#np-btn-pdf").click(function()
	{
       $("#np-form-export").submit();
	});
	
	$("#np-btn-xls").click(function()
	{
       $("#np-form-xls").submit();
	});
	
	 $(function(){
		$(".np-btn-view").click(function(){
			var view = $(this).attr("id");
			var url = "'.get_uri(false).'/"+view;
			
			$.ajax({
					url : url,
                    type :"GET",
					dataType: "html",
                    beforeSend : function(){ 
                        $("#np-table-view-more").html(loading);  
					},
					success : function(data){ 
                          $("#np-table-view-more").html(data); 
					}
				});
		});
		 
        var btnDelete = $(".np-btn-delete-item");
		btnDelete.click(function(){
        var id = $(this).attr("id");
		
		 //Função para excluir
		 function deleteItem(id)
		 {
			$.ajax({
					url : "'.get_uri(false).'",
                    type :"DELETE",
                    data :{id:id},
					dataType: "json",
                    beforeSend : function(){ 
                           //$(".np-progress").show();
						    swal({
                            content: loading,
							title: "Por favor, aguarde...",
                            button: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                            });
					},
					success : function(data){ 
                              var type = data.type;
							  var msg  = data.msg;
							  if(type == "success"){
								  swal("'.text(':success').'", msg, "success");
								  $(".np-painel-item-"+id).hide("fast");
							  }else if(type == "error"){
								   swal("'.text(':error').'", msg, "error");
							  }else{
								 swal("'.text(':info').'", msg, "info") 
							  }
					},
					complete : function(){ $(".np-progress").hide();  }
				});
		 }
       
		  swal({
          title: "Deseja Excluir?",
          icon: "warning",
          buttons: [
            "Cancelar",
            "Excluir"
          ],
          dangerMode: true,
        }).then(function(isConfirm) {
          if (isConfirm) {
          
		      deleteItem(id)
		  
          } else {
           
          }
        });
					   
			   });
		 });
	  </script>';
	  
	  
	return $tr;
}


function paginate($datas){
	
	 if($datas->links)
	  {
	    return '<div class="col-2  right">'.$datas->links.'</div>';
	  }
	  
}



