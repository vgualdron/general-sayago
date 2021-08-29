<template>
  <div>
		<div v-if="tipo === 'UNICA'">
			<template v-for="(opcion, i) in opciones">
				<label :key="'label_' + idPregunta + '_' + opcion.idOpcion" class="container-radio">{{opcion.descripcion}}
				  <input :id="respSeleccionada" type="radio" v-model="respSeleccionada" :name="'opcion_pregunta_' + idPregunta+ '_' + index" class="text-justify" :value="opcion.idOpcion" :key="idPregunta + '_' + opcion.idOpcion" :disabled="disabledOptions">
					<span class="checkmark-radio"></span>
				</label>
				<div :key="'div_' + idPregunta + '_' + opcion.idOpcion" v-if="opcion.pregunta" style="padding-left: 35px;">
					<pregunta v-if="respSeleccionada === opcion.idOpcion" :pregunta="opcion.pregunta" :idPregunta="opcion.pregunta.idPregunta" :disabledOptions="disabledOptions"></pregunta>
				</div>
			</template>
		</div>
		<div v-else>
			<template v-for="opcion in opciones">
				<label :key="'label_' + idPregunta + '_' + opcion.idOpcion" class="container-checkbox">{{opcion.descripcion}}
					<input type="checkbox" @click="validarCampoCap(idPregunta + '_' + opcion.idOpcion)" :ref="idPregunta + '_' + opcion.idOpcion" v-model="respSeleccionada" :name="'opcion_pregunta_' + idPregunta" class="text-justify" :value="opcion.idOpcion" :key="idPregunta + '_' + opcion.idOpcion" :disabled="disabledOptions">
					<span class="checkmark-checkbox"></span>
				</label>
				<b-form-textarea class="descripcion-cap" v-if="tieneCap && !disabledOptions"
					placeholder="Ingrese una descripción"
					v-model="opcion.descripcionCap"
					:rows="2"
					:ref="idPregunta + '_' + opcion.idOpcion + '_descripcion'"
					:max-rows="4">
				</b-form-textarea>
				<div :key="'div_' + idPregunta + '_' + opcion.idOpcion" v-if="opcion.pregunta" style="padding-left: 35px;">
				  <pregunta v-if="respSeleccionada && respSeleccionada.indexOf(opcion.idOpcion) > -1" :pregunta="opcion.pregunta" :idPregunta="opcion.pregunta.idPregunta" :disabledOptions="disabledOptions"></pregunta>
				</div>
			</template>
		</div>
  </div>
</template>
<script>
export default {
  name: 'opciones',
  props: ['opciones', 'tipo', 'value', 'disabledOptions', 'tieneCap', 'idPregunta', 'index'],
  data: function () {
    return {
      respSeleccionada: this.value,
      variable: 0
    }
  },
  mounted: function () {
    if (this.tieneCap && this.opciones && this.opciones.length) {
      for (var i = 0; i < this.opciones.length; i++) {
        this.validarCampoCap(this.idPregunta + '_' + this.opciones[i].idOpcion)
      }
    }
  },
  methods: {
    validarCampoCap: function (id) {
      if (this.tieneCap && id && this.$refs[id] && this.$refs[id][0]) {
        if (this.$refs[id][0].checked) {
          this.$refs[id + '_descripcion'][0].$el.style.display = 'block'
        } else {
          // Limpiar la descripcion del cap desmarcado
          var valores = id.split('_')
          if (valores.length > 1) {
            var opcionFiltrada = this.opciones.filter(function (elemento) {
              return elemento.idOpcion === id.split('_')[1]
            })
            if (opcionFiltrada.length > 0) {
              opcionFiltrada[0].descripcionCap = ''
            }
          }
          this.$refs[id + '_descripcion'][0].$el.style.display = 'none'
        }
      }
    }
  },
  watch: {
    respSeleccionada: function (newValue, oldValue) {
      this.$emit('input', newValue)
    }
  }
}
</script>
<style scoped>
  /* The container */
.container-radio {
	text-align: left;
	display: block;
	position: relative;
	padding-left: 35px;
	margin-bottom: 12px;
	cursor: pointer;
	font-size: 15px;
	-webkit-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
}

/* Hide the browser's default radio button */
.container-radio input {
	position: absolute;
	opacity: 0;
	cursor: pointer;
}

/* Create a custom radio button */
.checkmark-radio {
	margin-top: 3px;
	position: absolute;
	top: 0;
	left: 0;
	height: 16px;
	width: 16px;
	background-color: #ccc;
	border-radius: 50%;
}

/* On mouse-over, add a grey background color */
.container-radio:hover input ~ .checkmark-radio {
	background-color: rgba(136, 128, 128, 0.411);
}

/* When the radio button is checked, add a blue background */
.container-radio input:checked ~ .checkmark-radio {
	background-color: #2196F3;
}

/* Create the indicator (the dot/circle - hidden when not checked) */
.checkmark-radio:after {
	content: "";
	position: absolute;
	display: none;
}

/* Show the indicator (dot/circle) when checked */
.container-radio input:checked ~ .checkmark-radio:after {
	display: block;
}

/* Style the indicator (dot/circle) */
.container-radio .checkmark-radio:after {
 	top: 5px;
	left: 5px;
	width: 6px;
	height: 6px;
	border-radius: 50%;
	background: white;
}

/* The container */
.container-checkbox {
	text-align: left;
	display: block;
	position: relative;
	padding-left: 35px;
	margin-bottom: 12px;
	cursor: pointer;
	font-size: 15px;
	-webkit-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
}

/* Hide the browser's default checkbox */
.container-checkbox input {
	position: absolute;
	opacity: 0;
	cursor: pointer;
}

/* Create a custom checkbox */
.checkmark-checkbox {
	margin-top: 3px;
	position: absolute;
	top: 0;
	left: 0;
	height: 15px;
	width: 15px;
	background-color: #ccc;
	border-radius: 10%;
}

/* On mouse-over, add a grey background color */
.container-checkbox:hover input ~ .checkmark-checkbox {
	background-color: rgba(136, 128, 128, 0.411);
}

/* When the checkbox is checked, add a blue background */
.container-checkbox input:checked ~ .checkmark-checkbox {
	background-color: #2196F3;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark-checkbox:after {
	content: "";
	position: absolute;
	display: none;
}

/* Show the checkmark when checked */
.container-checkbox input:checked ~ .checkmark-checkbox:after {
	display: block;
}

/* Style the checkmark/indicator */
.container-checkbox .checkmark-checkbox:after {
	left: 5px;
	top: 2px;
	width: 5px;
	height: 9px;
	border: solid white;
	border-width: 0 3px 3px 0;
	-webkit-transform: rotate(45deg);
	-ms-transform: rotate(45deg);
	transform: rotate(45deg);
}
.descripcion-cap {
	margin-bottom:15px;
	display: none;
}
</style>
