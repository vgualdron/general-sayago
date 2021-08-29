<template>
  <ol class="breadcrumb">
    <li class="breadcrumb-item" v-for="(item, index) in list" v-if="item.name !== null">
      <span class="active" v-if="isLast(index)">{{ item.name }}</span>
      <router-link :to="item" v-else>{{ item.name }}</router-link>
    </li>
  </ol>
</template>

<script>
export default {
  props: {
    list: {
      type: Array,
      required: true,
      default: () => []
    }
  },
  methods: {
    isLast (index) {
      return index === this.list.length - 1
    },
    showName (item) {
      return item.name
    }
  }
}
</script>
