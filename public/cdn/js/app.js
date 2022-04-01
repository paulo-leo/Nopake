const Counter = {
  data() {
    return {
      counter: 0
    }
  },
  mounted() {
    setInterval(() => {
      this.counter++
    }, 1000)
  }
}

const app = Vue.createApp(Counter);

app.component('admin-pagination', {
  props:{
	headers:{
		type: [Array],
        default: ['Um','Dois','Tres'],
	}
  },
  mounted() {
    this.getList();
	console.log(this.results); 
	console.log(this.url); 
  },
  methods:{
	
	getList(){
		
      axios
      .get('https://api.coindesk.com/v1/bpi/currentprice.json',{proxy: {
      host: 'http://localhost',
      port: 12345
   }})
      .then(response => (this.info = response))
	  
	}  
	
  },
  data() {
    return {
	info:null,
      lista: 155,
	  results:[],
	  url:'https://pokeapi.co/api/v2/pokemon?limit=10&offset=10'
    }
  },
  template:'<table class="table"><tr><td v-for="h in headers">{{h}}</td></tr></table>'
})

app.mount('#app');