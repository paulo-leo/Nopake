<?php 
namespace Nopadi\MVC;

class Form
{
  public static function switch($name,$value=null,$label=null)
  {
    $value = trim($value);
	$value = strtolower($value);
	$value = $value == 'on' || $value == 1 ? 'on' : 'off';
	$checked = ($value == 'on') ? ' checked' : null;
	$label = is_null($label) ? 'Default switch checkbox' : $label;
	
	$html = "<div class='form-check form-switch'>
      <input class='form-check-input' type='checkbox' name='{$name}' id='npFlexSwitchCheck{$name}' {$checked}>
      <label class='form-check-label' for='npFlexSwitchCheck{$name}'>{$label}</label>
    </div>";
	
	return $html;
  }

  public static function header($attributes=array())
  {
	$id = isset($attributes['id']) ? $attributes['id'] : null;
	$text_btn = isset($attributes['text_btn']) ? $attributes['text_btn'] : null;
    $title = isset($attributes['title']) ? $attributes['title'] : null;	
	$method = isset($attributes['method']) ? $attributes['method'] : 'POST';
	$route = isset($attributes['route']) ? $attributes['route'] : null;
	$loading_text = isset($attributes['loading_text']) ? $attributes['loading_text'] : 'Enviando...';
	
	if(isset($attributes['url'])){ $route = $attributes['url']; }
		
	$args = isset($attributes['args']) ? $attributes['args'] : null;
	$max_file_size = isset($attributes['max_file_size']) ? $attributes['max_file_size'] : 30000;	
	$id_form = !is_null($id) ? "-{$id_form}" : null;
	$max = null;
	$file = null;
	
	if(isset($attributes['files']))
	{
		$attributes['file'] = $attributes['files'];
	}
	
	if(isset($attributes['file']))
	{
		if(is_bool($attributes['file']) && $attributes['file'] == true){
			$file = "enctype='multipart/form-data'";
			$max = "<input type='hidden' name='MAX_FILE_SIZE' value='{$max_file_size}' />";
		}
	}
	$debug =false;
	
	if(isset($attributes['debug']))
	{
		if(is_bool($attributes['debug']) && $attributes['debug'] == true)
		{
			$debug = true;
		}
	}
	
	$url = url($route);
	
	$reset = null;
	
	if(is_null($text_btn)){
		$text_btn = (strtolower($method) == strtolower('PUT')) ? text(':update') : text(':save');
	}
	
	$text_class_color = isset($attributes['text_class']) ? $attributes['text_class'] : 'btn-outline-primary';
   //id="np-form-open'.$id_form.'" action="'.url($url).'" method="'.$method.'" 
   $form = "<form id='np-form-open{$id_form}' {$file}>{$max}
            <div class='sticky-header'>
	         <div class='d-flex align-items-center'>
		      <a class='btn-back' href='javascript:history.back()'><i class='material-icons'>chevron_left</i></a>
		      <h2 class='sticky-title'>{$title}</h2>
	        </div>
	       <div>
		      <div class='btn btn-sticky btn-primary' id='np-form-loading{$id_form}' style='display:none'>
                <span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span>
				{$loading_text}
             </div>
		      <button id='np-form-submit{$id_form}' type='submit' class='btn btn-sticky {$text_class_color}'>{$text_btn}</button>
	      </div>
         </div>";
		 
		 
	 $form .= '<div id="form-local" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11"></div>';
		 
	  $form .= '<script>
	   $(document).ready(function() { 
            var form = $("#np-form-open'.$id_form.'"); 
			form.submit(function(){ 
			var values = form.serialize();
					$.ajax({
					url : "'.$url.'",
                    type :"'.$method.'",
                    data :values,
					cache: false,
					dataType: "json",
                    beforeSend : function(){ 
					
					      $("#np-form-loading'.$id_form.'").show();
						  $("#np-form-submit'.$id_form.'").hide();

					},
					error:function(data){
						 console.log(data);
						$("#form-local").append(\'<div class="alert alert-warning alert-dismissible" role="alert"><strong>'.text(':error').'!</strong> Verifique se o registo não está duplicado. Ou realize o debug da aplicação. <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>\');
						 
					},
					success : function(data){';
					
					          if($debug){ $form .= " console.log(data); ";}
					  
                              $form .= 'var type = data.type;
							  var msg  = data.msg;
							  if(type == "success"){
								 
							      $("#form-local").append(\'<div class="alert alert-success alert-dismissible" role="alert"><strong>'.text(':success').'!</strong> \'+msg+\'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>\');
								  
							  }else if(type == "error"){
								  
								    $("#form-local").append(\'<div class="alert alert-danger alert-dismissible" role="alert"><strong>'.text(':error').'!</strong> \'+msg+\'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>\');
								   
							  }else{
								 
								  $("#form-local").append(\'<div class="alert alert-primary alert-dismissible" role="alert"><strong>'.text(':info').'!</strong> \'+msg+\'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>\');
								 
							  }';
							  
					$form .= '},
				complete:function(data){
					
				    $("#np-form-loading'.$id_form.'").hide();
					$("#np-form-submit'.$id_form.'").show();'; 
					
					if($debug){ $form .= " console.log(data); ";}
                   				
				$form .= '}
				});
				return false;
			});
      });
	  </script>';
	  
	  
	  return $form;
	
  }
	
  public static function open($attributes=array())
  {

	$id = isset($attributes['id']) ? $attributes['id'] : ''; 
    $url = isset($attributes['url']) ? $attributes['url'] : ''; 
	$method = isset($attributes['method']) ? $attributes['method'] : 'POST'; 
    $url = url($url); 	
	$class = isset($attributes['class']) ? $attributes['class'] : 'form-label'; 
    $max_file_size = isset($attributes['max_file_size']) ? $attributes['max_file_size'] : 30000;	
	
	$max = null;
	$file = null;
	
	if(isset($attributes['files']))
	{
		$attributes['file'] = $attributes['files'];
	}
	
	if(isset($attributes['file'])){
		if(is_bool($attributes['file']) && $attributes['file'] == true){
			$file = "enctype='multipart/form-data'";
			$max = "<input type='hidden' name='MAX_FILE_SIZE' value='{$max_file_size}' />";
		}
	}
	
	return "<form id='{$id}' method='{$method}' class='{$class}' action='{$url}' {$file}>{$max}"; 
  }
  
  public static function close()
  {
	return "</form>"; 
  }
  
  public static function submit($value=null,$class='btn btn-primary')
  {
	 return "<input type='submit' class='{$class}' value='{$value}' />"; 
  }
  
  public static function label($attributes)
  {
	if(is_string($attributes))
	{
		$text = $attributes;
		$attributes = array();
	}else
	{
		$text = isset($attributes['text']) ? $attributes['text'] : '';
	}
	  
	$id = isset($attributes['id']) ? $attributes['id'] : 'np-input-'.$text; 
	
	$class = isset($attributes['class']) ? $attributes['class'] : 'form-label';  
	return "<label for='{$id}' class='{$class}'>{$text}</label>";
  }
  
  public static function input($attributes,$options=null)
  {
	 if(is_string($attributes))
	 {
		$name = $attributes;
		$attributes = array();
	 }else{
		$name = isset($attributes['name']) ? $attributes['name'] : ''; 
	 }
	 
	 
	 $id = isset($attributes['id']) ? $attributes['id'] : 'np-input-'.$name;
	 
	 $others = null;
	 if(isset($attributes['others']) && is_array($attributes['others']))
	 {
		 foreach($attributes['others'] as $akey=>$vkey)
		 {
		    $others .=  "{$akey}='{$vkey}' ";
		 }
	 }
	 
	 $type = isset($attributes['type']) ? $attributes['type'] : 'text';
	 
	 switch($type)
	 {
        case 'select' : $default_class  = 'form-select'; break;
		case 'color' : $default_class  = 'form-control-color'; break;
        case 'checkbox' : $default_class  = 'form-check-input'; break;
        default : $default_class  = 'form-control'; break;		
	 }
	 
	 $min = isset($attributes['min']) ? $attributes['min'] : '';
	 $max = isset($attributes['max']) ? $attributes['max'] : '';
	 
	 $class = isset($attributes['class']) ? $attributes['class'] : $default_class;
	 $style = isset($attributes['style']) ? $attributes['style'] : '';
	 $value = isset($attributes['value']) ? $attributes['value'] : '';
	 $accept = isset($attributes['accept']) ? $attributes['accept'] : '';
	 $capture = isset($attributes['capture']) ? $attributes['capture'] : '';
	 $multiple = isset($attributes['multiple']) ? $attributes['multiple'] : '';
	 
	 $placeholder = isset($attributes['placeholder']) ? $attributes['placeholder'] : '';
	 
	 $disabled = null;
	 if(isset($attributes['disabled']) && is_bool($attributes['disabled']))
	 {
		 if($attributes['disabled'] == true) $disabled = 'disabled';
	 }
	 $readonly = null;
	 if(isset($attributes['readonly']) && is_bool($attributes['readonly']))
	 {
		 if($attributes['readonly'] == true) $readonly = 'readonly';
	 }
	 
	 $required = null;
	 if(isset($attributes['required']) && is_bool($attributes['required']))
	 {
		 if($attributes['required'] == true) $required = 'required';
	 }
	 
	 
	 $checked = isset($attributes['checked']) ? $attributes['checked'] : '';
	 if($checked == 1 || strtolower($checked) == strtolower('on') || $checked == '1'){  $checked = true; }
	 
	 if(is_bool($checked))
	 {
		 if($checked == true) $checked = 'checked';
	 }
	 
	 if($type == 'select')
	 {
		 $option = null;
		 foreach($options as $key=>$val){
			 
			 if($value == $key){
				  $option .= "<option value='{$key}' selected>{$val}</option>";
			 }else
			 {
				  $option .= "<option value='{$key}'>{$val}</option>";
			 }
		 }
		 return "<select name='{$name}' id='{$id}' class='form-select' {$disabled} {$required}>{$option}</select>";
		 
	 }
	 elseif($type == 'number')
	 {
		  return "<input type='{$type}' min='{$min}' max='{$max}' name='{$name}' id='{$id}' class='{$class}' value='{$value}' placeholder='{$placeholder}' {$disabled} {$required} {$readonly} {$others} />";
	 }
	 elseif($type == 'file')
	 {
		  return "<input type='{$type}' name='{$name}' id='{$id}' class='{$class}' {$multiple} {$disabled} {$required} {$readonly} {$others} />";
	 }
	 elseif($type == 'textarea')
	 {
		  return "<textarea name='{$name}' id='{$id}' class='{$class}' placeholder='{$placeholder}' {$disabled} {$required} {$readonly} {$others}>{$value}</textarea>";
	 }
	 elseif($type == 'checkbox' || $type == 'radio')
	 {
		  return "<input type='{$type}' name='{$name}' id='{$id}' class='{$class}' value='{$value}' {$disabled} {$required} {$checked} {$others} />";
	 }
	 else
	 { 
	 return "<input type='{$type}' name='{$name}' id='{$id}' class='{$class}' value='{$value}' placeholder='{$placeholder}' {$disabled} {$required} {$readonly} {$others} />";
	 } 
  }
} 