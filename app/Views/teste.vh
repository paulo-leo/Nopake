{import ['head','script.js','style.css']}

{php}

  $ar = [10,40,55,15];

{/php}


<br>
{for $i=0;$i < count($ar);$i++}
   
     {{$ar[$i]}} <hr>
    
{/for}