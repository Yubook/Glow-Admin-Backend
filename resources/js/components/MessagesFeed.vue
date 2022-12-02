<template>
  <div class="feed" ref="feed">
    <ul v-if="contact">
      <li
        v-for="message in messages"
        :class="`message ${message.to == contact.id ? 'sent' : 'received'}`"
        :key="message.id"
      >
        <div class="text">
          {{ message.message }}
          <br />
          <span  v-if="message.read == 1" style="font-size: 10px"
            >{{ message.created_at }} &#10004;</span
          >
          <span  v-else style="font-size: 10px"
            >{{ message.created_at }} </span
          >
        </div>
        <div class="image-container">
          <img v-if="message.image" :src="'/storage/' + message.image" alt="" />
        </div>
      </li>
    </ul>
  </div>
</template>

<script>
import { setTimeout } from "timers";

export default {
  props: {
    contact: {
      type: Object,
    },
    messages: {
      type: Array,
      required: true,
    },
  },
  methods: {
    scrollToBottom() {
      setTimeout(() => {
        this.$refs.feed.scrollTop =
          this.$refs.feed.scrollHeight - this.$refs.feed.clientHeight;
      }, 10);
    },
  },
  watch: {
    contact(contact) {
      this.scrollToBottom();
    },
    messages(messages) {
      this.scrollToBottom();
    },
  },
};
</script>

<style lang="scss" scoped>
/* width */
::-webkit-scrollbar {
  width: 10px;
}

/* Track */
::-webkit-scrollbar-track {
  box-shadow: inset 0 0 5px grey;
  border-radius: 5px;
}

/* Handle */
::-webkit-scrollbar-thumb {
  background: rgb(144, 144, 211);
  border-radius: 10px;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: #eec4c4;
}

.feed {
  background: white;
  height: 100%;
  max-height: 250px;
  overflow-y: scroll;

  ul {
    list-style-type: none;
    padding: 5px;

    li {
      &.message {
        margin: 10px 0;
        width: 100%;

        .text {
          max-width: 300px;
          border-radius: 5px;
          padding: 12px;
          display: inline-block;
        }

        &.received {
          text-align: left;

          .text {
            background: linear-gradient(to left, #66ffcc 0%, #ec9dec 100%);
          }
        }
        &.sent {
          text-align: right;

          .text {
            background: linear-gradient(to right, #adadf0 11%, #8ee4c1 100%);
          }
        }
      }
    }
  }
}
</style>

