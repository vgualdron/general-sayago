<template>
	<div>
		<pregunta v-for="pregunta in preguntas"
      :pregunta="pregunta"
      indice="orden"
      :key="pregunta.idPregunta"
      :disabledOptions="disabledOptions"
      :index="index">
    </pregunta>
	</div>
</template>
<script>
export default {
  name: 'seccion',
  props: ['preguntas', 'disabledOptions', 'index'],
  data: function () {
    return {
    }
  }
}
</script>
