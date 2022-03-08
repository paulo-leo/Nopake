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
       
       function renderTable(params)
       {
        var renderLocal = $('#app-render');
        var array = params.rows;
        var url = params.url;
        var data = params.data;
        var legends = params.legends;
        var filters = params.filters;
        var loading = '<div class="p-4 text-center"><div class="spinner-border" role="status" style="width: 3rem; height: 3rem;"><span class="visually-hidden">Loading...</span></div></div>';

        $.ajax({
          method:'get',
          url:url,
          data:data,
          dataType: 'json',
          beforeSend:function(){
            renderLocal.html(loading);
          },
          success:function(data)
          {
             let render = '<table class="table">';
             render += '<tread>';
             for (let i = 0; i < array.length; i++)
             {     
                    let nameHead = legends[array[i]] != undefined ? legends[array[i]] : array[i];
                    render += '<td>'+nameHead+'</td>';
             }
             render += '</tread><tbody>';
             $.each(data.results,function(index,value){
                render += '<tr>';
                for (let i = 0; i < array.length; i++)
                {    
                    if(filters[array[i]] != undefined){
                      render += '<td>'+ filters[array[i]](value[array[i]]) +'</td>';
                    }else{
                      render += '<td>'+value[array[i]]+'</td>';
                    }
                    
                }
                render += '</tr>';
             }); 
             
             render += '<tr><td colspan="'+array.length+'">';
             render += '<nav><ul class="pagination justify-content-center">';

             if(data.previous)
             {
               render += '<li class="page-item"><a class="page-link" href="'+data.previous+'">&laquo;</a></li>';
             }

             if(data.numbers != undefined)
             {
                let numberPage = 1;
                for (let i = 0; i < data.numbers.length; i++)
                { 
                  var classActive = (data.page == numberPage) ? 'active' : '';
                    render += '<li class="page-item '+classActive+'"><a class="page-link" href="'+data.numbers[i]+'">'+numberPage+'</a></li>';
                 
                  numberPage++;
                }
             }


             if(data.next)
             {
               render += '<li class="page-item"><a class="page-link" href="'+data.next+'">&raquo;</a></li>';
             }

             render += '</ul></nav>';
             render += '<button type="button" class="btn btn-sm btn-primary position-relative">';
             render += data.page+'/'+data.total;
             render += '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">';
             render += data.count;
             render += '</span></button>';
             
             render += '</td></tr>';

             render += '</tbody></table>';
             renderLocal.html(render);
          }
          });

          renderLocal.on('click','table > tbody > tr > td > nav > ul > li > a',function(event){
             event.preventDefault();
             var link = $(this).attr('href');
             renderTable({
               url:link,
               rows:array,
               legends:legends
             });
          });
       }

        renderTable({
         url:'{{url('dashboard/books')}}',
         rows:['name','status','privacy'],
         legends:{name:'Nome do livo',status:'Situação do livro',privacy:'Compartilhamento'},
         filters:{
          privacy:function(e){
             if(e == 1) return 'Publico';
             else return 'Privado';
             
           }
         }
        });

     });
   </script>

  </body>
</html>
