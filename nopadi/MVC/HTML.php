<?php 
namespace Nopadi\MVC;

class HTML
{
  public function header($arr1=array(),$arry2=null)
  {
	  
	if(is_string($arr1))
	{
	  $title = $arr1;	
	}else{
		
	  $title = isset($arr1['title']) ? $arr1['title'] : null;
		
	}  
	  
	$html = "
	   <div class='fixed-header'>
        <div class='d-flex align-items-center'>
            <button class='btn-back'><i class='material-icons'>chevron_left</i></button>
            <h2 class='header-title'>{$title}</h2>
        </div>
        
        <div class='d-flex'>
            <button  data-bs-toggle='modal' data-bs-target='#npModalTitleHeader' class='btn-filter'>
                <i class='material-icons'>filter_alt</i>
            </button>

            <div class='dropdown mx-2'>
                <button  class='btn btn-secondary dropdown-toggle py-1 p-2' href='#' role='button' id='dropdownMenuLink' data-bs-toggle='dropdown' aria-expanded='false'>
                  Dropdown link
                </button >
              
                <ul class='dropdown-menu' aria-labelledby='dropdownMenuLink'>
                  <li><a class='dropdown-item' href='#'>Action</a></li>
                  <li><a class='dropdown-item' href='#'>Another action</a></li>
                  <li><a class='dropdown-item' href='#'>Something else here</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class='modal fade' id='npModalTitleHeader' tabindex='-1' aria-labelledby='npModalTitleHeaderLabel' aria-hidden='true'>
        <div class='modal-dialog modal-dialog-scrollable modal-lg'>
          <div class='modal-content'>
            <div class='modal-header'>
              <h5 class='modal-title' id='npModalTitleHeaderLabel'>Modal title</h5>
              <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
              ...
            </div>
            <div class='modal-footer'>
              <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
              <button type='button' class='btn btn-primary'>Save changes</button>
            </div>
          </div>
        </div>
    </div>";  
  }
  
} 