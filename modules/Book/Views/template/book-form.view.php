@template(modal_create_book) 

$token = csrf_token();

$html = "<div class='modal fade' id='exampleModal' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
  <div class='modal-dialog'>
  <form id='app-book-form-create'>
  <input type='hidden' name='_token' value='{$token}' > 
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='exampleModalLabel'>Novo livro</h5>
        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
      </div>
      <div class='modal-body'>
      <div class='mb-3'>
          <label for='nome-l' class='form-label'>Nome do livro</label>
           <input name='name' required type='text' class='form-control' id='nome-l' placeholder=''>
        </div>
       <div class='mb-3'>
         <label for='nome-l2' class='form-label'>Descrição do livro</label>
        <textarea name='description' required class='form-control' id='nome-l2' rows='3'></textarea>
       </div>
       <div class='form-check form-check-inline'>
           <input class='form-check-input' type='radio' name='privacy' id='flexRadioDefault1' value='1'>
         <label class='form-check-label' for='flexRadioDefault1'>
         Publico
       </label>
      </div>
     <div class='form-check form-check-inline'>
       <input class='form-check-input' type='radio' name='privacy' id='flexRadioDefault2' value='2'>
      <label class='form-check-label' for='flexRadioDefault2'>
        Protegido
     </label>
    </div>
    <div class='form-check form-check-inline'>
       <input class='form-check-input' type='radio' name='privacy' id='flexRadioDefault2' value='3'>
      <label class='form-check-label' for='flexRadioDefault2'>
        Privado
     </label>
    </div>
    

      </div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
        <button type='submit' class='btn btn-primary'>Save changes</button>
      </div>
    </div>
    </form>
  </div>
</div>";


$html .= "
<script>
    $(function(){
        $('#app-book-form-create').submit(function(){

            $.ajax({
                method:'post',
                url:'".url('dashboard/books')."',
                data:$(this).serialize(),
                success:function(data){
                    alert(data);
                }
            });
            return false;
        });
    });
</script>
";

return $html;

@endtemplate 