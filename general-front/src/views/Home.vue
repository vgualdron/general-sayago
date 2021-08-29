<template>
	<div>
		<b-row>
			<b-col cols="12">
				<b-card border-variant="primary" header-bg-variant="primary" header="Información"
		          tag="article"
		          class="m-3 mt-3">
		          <b-row align-v="center" class="text-center">
									
		          	<b-col sm="12" md="3">
		          	  <b-img fluid src="static/img/logo_login.jpg" alt="logo"/>
		          	</b-col>
		          	<b-col class=" texto-pae text-justify">
		          		<p class="card-text">
										Restaurante Pinchetas.
									</p>
		          	</b-col>
		          </b-row>
		  		</b-card>
			</b-col>
		</b-row>
	</div>
</template>
<style>
	@media (max-width:768px){
      .texto-pae{
      	margin-top:10px;
      }
    }
</style>
<script>
var self = this
export default {
  data () {
    return {
      aplicaciones: []
    }
  },
  methods: {
  },
  created: function () {
	},
	mounted: function () {
    this.$loader.close()
  }
}
</script>
