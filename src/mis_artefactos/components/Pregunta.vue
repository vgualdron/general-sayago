<template>
	<div>
		<p class="text-justify" v-if="indice === 'orden'">{{pregunta.orden}}) {{pregunta.descripcion}} <span class="tiene-cap" v-if="pregunta.tieneCap" >Tiene CAP</span></p>
		<p class="text-justify" v-else><i class="fa fa-minus" style="color:#2ed573;"></i> {{pregunta.descripcion}} <span class="tiene-cap" v-if="pregunta.tieneCap" >Tiene CAP</span></p>
		<opciones v-if="pregunta.tipo === 'CERRADA'"
		  :opciones="pregunta.opciones"
			:idPregunta="pregunta.idPregunta"
			:tipo="pregunta.tipoEscalaFormato"
			:tieneCap="pregunta.tieneCap"
			v-model="pregunta.respuesta"
			:disabledOptions="disabledOptions"
			:index="index">
		</opciones>
		<b-form-input v-else-if="pregunta.tipo === 'ABIERTAN' && !disabledOptions" :type="'number'"
		  style="margin-bottom: 25px;"
			placeholder="Ingrese una respuesta"
			v-model="pregunta.respuesta"
			:disabled="disabledOptions">
		</b-form-input>
		<b-form-textarea style="margin-bottom:15px;" v-else-if="pregunta.tipo === 'ABIERTAT' && !disabledOptions"
			v-model="pregunta.respuesta"
			placeholder="Ingrese una respuesta"
			:rows="2"
			:max-rows="4">
    </b-form-textarea>
		<p style="text-align:left;" v-else>{{pregunta.respuesta}}</p>
	</div>
</template>
<script>
export default {
  name: 'pregunta',
  props: ['pregunta', 'indice', 'disabledOptions', 'index'],
  data: function () {
    return {
    }
  },
  methods: {
    alerta: function (mensaje) {
      alert(mensaje)
    }
  }
}
</script>
<style scoped>
	.tiene-cap {
		color:red;
		font-weight:700;
	}
</style>
