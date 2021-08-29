<template>
	<div>
	  <b-list-group>
	  	<template v-for="item in items">
	  	  <b-list-group-item>
	  	  	<label class="container-checkbox inline-block mr-3">{{item.nombre}}
              <input type="checkbox" :value="item" name="modalidades" v-model="opcionesMarcadas" class="text-justify">
              <span class="checkmark-checkbox"></span>
            </label>
            <input type="number" v-model="item.cantidadArchivos" min="0" :max="cantidadMaxima">
	  	  </b-list-group-item>
	    </template>
	  </b-list-group>
	</div>
</template>
<script>
export default {
  name: 'item-checkbox',
  props: ['items', 'value', 'cantidadMaxima'],
  data () {
    return {
      opcionesMarcadas: this.value
    }
  },
  methods: {
    itemHabilitado: function (item) {
      	return this.opcionesMarcadas.filter((elemento) => { return elemento.idModalidad === item.idModalidad }).length > 0
    }
  },
  watch: {
    opcionesMarcadas: function (valorNuevo, valorAnterior) {
      this.$emit('input', valorNuevo)
    }
  }
}
</script>
<style scoped>
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
.inline-block {
	display:inline-block;
}
</style>
