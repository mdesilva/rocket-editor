<template>
  <v-app>
    <v-content>
      <Editor/>
    </v-content>
  </v-app>
</template>

<script>
import Editor from "./views/Editor"

export default {
  name: 'App',
  components: {
    Editor,
  },

  data: () => ({
    //
  }),
};
</script>
