<template>
  <div class="conversation">
      <h1 v-if="contact"><img :src="getAvatar(contact.image)" :alt="contact.name" height="42" width="42"/> {{ contact ? contact.name : "Select Person" }}</h1>
      <h1 v-else>Select Person</h1>
    <MessagesFeed :contact="contact" :messages="messages" />
    <MessageComposer @send="sendMessage" />
  </div>
</template>

<script>
import MessagesFeed from "./MessagesFeed";
import MessageComposer from "./MessageComposer";
export default {
  props: {
    contact: {
      type: Object,
      default: null,
    },
    messages: {
      type: Array,
      default: [],
    },
  },
  methods: {
    getAvatar(avatar) {
      var newavatar = avatar.replace("public", "storage");
      return newavatar;
    },
    sendMessage(text) {
      // console.log(text);
      if (!this.contact) {
        return;
      }
      axios
        .post("/conversation/send", {
          contact_id: this.contact.id,
          text: text,
        })
        .then((response) => {
          this.$emit("new", response.data);
        });
    },
  },
  components: { MessagesFeed, MessageComposer },
};
</script>

<style lang="scss" scoped>
.conversation {
  flex: 5;
  display: flex;
  flex-direction: column;
  justify-content: space-between;

  h1 {
    color: rgb(9, 12, 12);
    font-size: 16px;
    font-weight: bold;
    padding: 10px;
    margin: 0;
    border-bottom: 2px dashed lightgray;
  }
}
</style>

