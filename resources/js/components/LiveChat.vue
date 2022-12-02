<template>
  <div class="chat-app">
    <ContactsList :contacts="contacts" @selected="startConversationWith" />
    <Conversation
      :contact="selectedContact"
      :messages="messages"
      @new="saveNewMessage"
    />
  </div>
</template>

<script>
import Conversation from "./Conversation";
import ContactsList from "./ContactsList";

export default {
  props: {
    user: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      selectedContact: null,
      messages: [],
      contacts: [],
    };
  },
  mounted() {
    Echo.private(`messages.${this.user.id}`).listen("NewMessage", (e) => {
      this.handleIncoming(e.message).whisper("typing", {
        name: this.user.name,
      });
    });
    axios.get("/contacts").then((response) => {
      console.log(response.data);
      this.contacts = response.data;
    });
  },

  created() {
    Echo.private(`messages.${this.user.id}`).listenForWhisper("typing", (e) => {
      this.isTyping = true;
      setTimeout(() => {
        this.isTyping = false;
      }, 2000);
    });
  },

  methods: {
    startConversationWith(contact) {
      this.updateUnreadCount(contact, true);
      axios.get(`/conversation/${contact.id}`).then((response) => {
        this.messages = response.data;
        this.selectedContact = contact;
      });
    },
    saveNewMessage(message) {
      this.messages.push(message);
    },
    handleIncoming(message) {
      if (this.selectedContact && message.from == this.selectedContact.id) {
        this.saveNewMessage(message);
        return;
        // this.messages.push(message) //does same same thing of above
      }
      // alert(message.text);
      // console.log(message);
      this.updateUnreadCount(message.from_contact, false);
    },
    updateUnreadCount(contact, reset) {
      this.contacts = this.contacts.map((single) => {
        if (single.id != contact.id) {
          return single;
        }
        if (reset) single.unread = 0;
        else single.unread += 1;
        return single;
      });
    },
  },
  components: { Conversation, ContactsList },
};
</script>

<style lang="scss" scoped>
.chat-app {
  display: flex;
  width: 100%;
  height: 100%;
}

//for scroll

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
  background: rgb(147, 147, 175);
  border-radius: 10px;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: #ca7b7b;
}
</style>

