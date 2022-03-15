@import([
   '@Book/Views/template/book-form'
  ])
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="https://unpkg.com/vue@next"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
	
	<script src="{{asset('painel/js/np-table.js')}}"></script>


    <title>Book</title>
  </head>
  <body>
    

    <div class="row" id="app">


        <nav class="nav bg-light border-bottom">
            <div class="dropdown align-items-end m-2">
                <a href="#" class="align-items-end justify-content-center p-3 link-dark text-decoration-none dropdown-toggle" id="dropdownUser3" data-bs-toggle="dropdown" aria-expanded="false">
                  <img src="https://avatars.githubusercontent.com/u/39014913?v=4" alt="mdo" class="rounded-circle" width="25" height="25">
                </a>
                <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser3">
                  <li><a class="dropdown-item" href="#">New project...</a></li>
                  <li><a class="dropdown-item" href="#">Settings</a></li>
                  <li><a class="dropdown-item" href="#">Profile</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="#">Sign out</a></li>
                </ul>
              </div>

              <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Livro
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" data-bs-toggle='modal' data-bs-target='#exampleModal' href="#">Novo</a></li>
            <li><a class="dropdown-item" href="#">Listar</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
          </ul>
        </li>
              <a class="nav-link active" aria-current="page" href="#">Active</a>
              <a class="nav-link" href="#">Link</a>
              <a class="nav-link" href="#">Link</a>
              <a class="nav-link disabled">Disabled</a>

          </nav>

        
       



          </div>


           <div id="app-render"></div>
		  
     
           @component('modal-create-book')        

     
  </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>


   <script>
     $(function(){

       var objeto = {};
       var x = 0;
       $('#btn-teste').click(function(){
         x++;
        objeto['mais'+x] = 'um'+x;
        var texto = JSON.stringify(objeto);

          $('#btn-teste-tx').text(texto);

       });


        let render = '#app-render';
		
	   $(render).npTableClick('.btn-view',function(){
		   
		   let id = $(this).attr('id');
		   
      
		   
		   $('#render-view').npView({
         url:'{{url("dashboard/books/teste")}}',
         content:function(data){
            return '<h1>'+data.name+'</h1>';
         }
       });
		   
	   });

	  
        var obj = {
         url:'{{url('dashboard/books')}}',
         rows:['name','status','privacy'],
         legends:{name:'Nome do livo',status:'Situação do livro',privacy:'Compartilhamento'},
		 btn:function(e){
			 
			 
			 
			 return "<button id='"+e.id+"' data-bs-toggle='offcanvas' data-bs-target='#offcanvasRight' aria-controls='offcanvasRight' class='btn btn-view btn-sm btn-primary'>Editar</button>";
			 
			 
			 
		 },
		 view:function(e){
			 return "<a class='btn btn-sm btn-danger' href='"+e.name+"'>Visualizar</a>";
		 },
         filters:{
          privacy:function(e){
             if(e.privacy == 1) return 'Publico';
             else return 'Privado';
             
           },
		  status:function(e){
             if(e.status == 1) return '<span class="badge bg-success">Ativo</span>';
             else return 'Não';
             
           }
         }
        };

        $(render).npTable(obj);
		
		
		
     });
   </script>
   
   
   <button id="btn-teste">Click</button><h1 id="btn-teste-tx"></h1>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
  <div class="offcanvas-header">
    <h5 id="offcanvasRightLabel">Offcanvas right</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div id="render-view"></div>
  </div>
</div>
   

  </body>
</html>
