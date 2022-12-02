<template>
  <div class="composer flex row">
    <span v-if="isTyping">is Typing . . .</span>
    <textarea
      autofocus
      v-model="message"
      @keydown.enter="send"
      placeholder="Type your message..."
    ></textarea>
    <input id="file" type="file" name="file" />
    <!--  <textarea autofocus v-model="message" placeholder="Type your message..."></textarea>-->
    <button class="send-button" @click.prevent="send">
      <i class="fa fa-paper-plane"></i>
    </button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      message: "",
      isTyping: false
    };
  },
  methods: {
    send(e) {
      e.preventDefault();
      if (this.message == "") {
        return;
      }
      this.$emit("send", this.message);
      this.message = "";
    },
  },
};
</script>

<style lang="scss" scoped>
.composer {
  display: flex;
  justify-content: center;
  textarea {
    width: 85%;
    margin: 7px;
    resize: none;
    border-radius: 7px;
    border: 1px solid lightgray;
    padding: 4px;
  }
  .send-button {
    border: 0;
    background: none;
    i {
      color: rgb(182, 182, 212);
      font-size: 28px;
    }
  }
}
</style>

