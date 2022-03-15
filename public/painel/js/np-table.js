(function($){ 
 $.fn.npTableClick = function(npSelect,npCallback)
 {
	 $(this).on('click','table > tbody > tr > td > '+npSelect,npCallback);
 }

 $.fn.npForm = function()
 {
  var renderLocal = $(this);

  

 }

 $.fn.npView = function(params)
 {
  var renderLocal = $(this);
  var url = params.url; 
  var method = params.method != undefined ? params.method : 'GET';
  var type = params.type != undefined ? params.type : 'JSON';
  var data = params.data != undefined ? params.data : {};
  var npCallback = params.content;

  var loading = '<div class="p-4 text-center"><div class="spinner-border" role="status" style="width:5rem; height:5rem;"><span class="visually-hidden">Loading...</span></div></div>';
 
  $.ajax({
    method:'get',
    cache:false,
    data:data,
    url:url,
    dataType: type,
    beforeSend:function(){
      renderLocal.html(loading);
    },
    success:function(data)
    {
      renderLocal.html(npCallback(data));
    }
  });

 }

 $.fn.npTable = function(params){
   
        var renderLocal = $(this);
        var array = params.rows;
        var url = params.url;
		    var method = params.method != undefined ? params.method : 'GET';
        var data = params.data;
        var legends = params.legends;
        var filters = params.filters;
	    	var edit = params.edit;
	    	var del = params.del;
	    	var view = params.view;
		    var btn = params.btn;
		    var tdTotal = 0;
		
		renderLocal.html('');
        var loading = '<div class="p-4 text-center"><div class="spinner-border" role="status" style="width: 3rem; height: 3rem;"><span class="visually-hidden">Loading...</span></div></div>';

        $.ajax({
          method:method,
		  cache:false,
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
			 
			 if(view != undefined){ render += '<th></th>'; tdTotal++; }
			 
             for (let i = 0; i < array.length; i++)
             {     
                    let nameHead = legends[array[i]] != undefined ? legends[array[i]] : array[i];
                    render += '<th>'+nameHead+'</th>';
             }
			 if(edit != undefined){ render += '<th></th>'; }
			 if(del != undefined){ render += '<th></th>'; }
			 if(btn != undefined){ render += '<th></th>'; }
			 
             render += '</tread><tbody>';
             $.each(data.results,function(index,value){
                render += '<tr>';
				
				render += '<td style="width:25px"><input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"></td>'; tdTotal++;
				
				
				if(view != undefined)
				{
					 render += '<td>'+view(value)+'</td>';
				}
				
                for(let i = 0; i < array.length; i++)
                {    
                    if(filters[array[i]] != undefined){
                      render += '<td>'+ filters[array[i]](value) +'</td>';
                    }else{
                      render += '<td>'+value[array[i]]+'</td>';
                    }
                    
                }
                if(edit != undefined)
				{
					 render += '<td>'+edit(value)+'</td>'; tdTotal++;
				}
				
				if(del != undefined)
				{
					 render += '<td>'+del(value)+'</td>'; tdTotal++;
				}
				
				if(btn != undefined)
				{
					 render += '<td>'+btn(value)+'</td>'; tdTotal++;
				}
				
                render += '</tr>';
             }); 
             
             render += '<tr><td colspan="'+array.length+tdTotal+'">';
             render += '<nav><ul class="pagination">';

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
				  var linkActive = (data.page == numberPage) ? '#' : data.numbers[i];
				  
                    render += '<li class="page-item '+classActive+'"><a class="page-link" href="'+linkActive+'">'+numberPage+'</a></li>';
                 
                  numberPage++;
                }
             }


             if(data.next)
             {
               render += '<li class="page-item"><a class="page-link" href="'+data.next+'">&raquo;</a></li>';
             }

             render += '</ul></nav>';
			 
			 render += '<span class="badge bg-success">';
		     render += data.count;
             render += '</span> ';
			 
             render += '<span class="badge bg-primary">';
		     render += data.page+'/'+data.total;
             render += '</span>';
		
             render += '</td></tr>';

             render += '</tbody></table>';
             renderLocal.html(render);
          }
          });
		  
          renderLocal.on('click','table > tbody > tr > td > nav > ul > li > a',function(event){
			 $(this).prop('disabled', true);
             var link = $(this).attr('href');
			 link = link.split('&_=');
			 link = link[0];
			 
			 if(link != '#'){

            $(renderLocal).npTable({
               url:link,
			   edit:edit,
			   view:view,
			   del:del,
               rows:array,
               legends:legends,
			   filters:filters
             });
			 }
			 return false;
          });
 };

})(jQuery);