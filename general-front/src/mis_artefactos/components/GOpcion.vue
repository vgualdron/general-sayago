<template>
	<b-container>
		<b-row>
			<b-col>
				<div v-if="tipo === 'UNICA'">
					<label class="container-radio">{{opcion.descripcion}}
					  	<input type="radio" :disabled="!editable" :name="nombre" class="text-justify" :value="opcion.idOpcion">
						<span class="checkmark-radio"></span>
					</label>
				</div>
				<div v-else>
					<label class="container-checkbox">{{opcion.descripcion}} (<span class="font-weight-bold">Valor: </span> {{opcion.valor}})
					  	<input type="checkbox" :disabled="!editable" :name="nombre" class="text-justify" :value="opcion.idOpcion">
						<span class="checkmark-checkbox"></span>
					</label>
				</div>

			</b-col>
		</b-row>
		<b-row v-if="opcion.pregunta">
			<b-col cols="10">
				<g-pregunta :editable="editable" :idPregunta="grupo + '_subpregunta_1'" v-model="opcion.pregunta"/>
			</b-col>
			<b-col cols="2">
				<b-button :disabled="!editable" @click="eliminarSubpregunta(opcion)" style="margin-left:10px !important;" size="sm" variant="danger"><i class="fa fa-trash"></i>
				</b-button>
			</b-col>
		</b-row>
		<b-row class="mb-3">
			<b-col>
				<b-button :disabled="!editable" size="sm" variant="primary" v-if="!opcion.pregunta" @click="agregarSubpregunta(opcion)">Agregar Subpregunta</b-button>
			</b-col>
		</b-row>
	</b-container>
</template>
<script>
export default {
  name: 'g-opcion',
  props: ['value', 'tipo', 'grupo', 'editable'],
  data: function () {
    return {
      nombre: this.grupo,
      opcion: this.value
    }
  },
  watch: {
    opcion: function (nuevo, antiguo) {
      this.$emit('input', nuevo)
    }
  },
  methods: {
    agregarSubpregunta: function (opcion) {
      this.$set(opcion, 'pregunta', {
        nombre: '',
        tipo: '',
        estrategiaHallazgo: '',
        escalaFormato: null
      })
    },
    eliminarSubpregunta: function (opcion) {
      this.$set(opcion, 'pregunta', null)
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
