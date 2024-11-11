<script>
import axios from 'axios';
import { router } from '@inertiajs/vue3'

export default {
    name: "AcceptGameComponent",
    data() {
    return {
        AudioPathGame: `${import.meta.env.BASE_URL}audios/confirm-game.mp3`,
        countdown: 20, 
        showModal: true, 
        timer: null,
        accepted: false,
        gameId: null
    };
  },
  props: [
    'user_id'
  ],
  mounted() {
      this.startCountdown();
      window.Echo.channel(`start-game.${this.user_id}`).listen('.start-game', res => {
        if(res.start_game){
          clearInterval(this.timer);
          router.visit(`/game/${res.start_game}`);

        }
      });
    
  },
  methods: {
    startCountdown() {
      this.timer = setInterval(() => {
      if (this.countdown > 0) {
        this.countdown--;
      } else {
        this.closeModal();
      }
      }, 1000);
    },
    closeModal() {
      clearInterval(this.timer);
      axios.post('/noAcceptGame',{
        user_id: this.user_id
      }).then(res =>{
          this.showModal = false;
          router.visit('/');
      })

    },
    acceptGame(){
      this.accepted = true;
      axios.post('/acceptGame',{
        user_id: this.user_id
      }).then(res =>{
          this.gameId = res.data.start
      })
    }
  },
}
</script>
<template>
<transition name="modal">
    <div v-if="showModal" class="modal-overlay">
        <audio :src="AudioPathGame" autoplay="autoplay"></audio>
        <div class="modal-window">
            <div class="text-white text-xl text-center p-2 border-b border-cyan-300">ПОДТВЕРЖДЕНИЕ</div>
            <div class="text-white text-lg text-center mt-3">Подтвердите игру, чтобы продолжить, {{ countdown }}</div>
            <div class="flex justify-center m-3">
                <svg class="animate-spin" width="70" height="70" viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M70 35C70 54.33 54.33 70 35 70C15.67 70 0 54.33 0 35C0 15.67 15.67 0 35 0C54.33 0 70 15.67 70 35ZM5.68172 35C5.68172 51.192 18.808 64.3183 35 64.3183C51.192 64.3183 64.3183 51.192 64.3183 35C64.3183 18.808 51.192 5.68172 35 5.68172C18.808 5.68172 5.68172 18.808 5.68172 35Z" fill="#374151"/>
                    <mask id="path-2-inside-1_47_104" fill="white">
                    <path d="M70 35C70 54.33 54.33 70 35 70C15.67 70 0 54.33 0 35C0 15.67 15.67 0 35 0C54.33 0 70 15.67 70 35ZM5.80061 35C5.80061 51.1264 18.8736 64.1994 35 64.1994C51.1264 64.1994 64.1994 51.1264 64.1994 35C64.1994 18.8736 51.1264 5.80061 35 5.80061C18.8736 5.80061 5.80061 18.8736 5.80061 35Z"/>
                    </mask>
                    <path d="M70 35C70 54.33 54.33 70 35 70C15.67 70 0 54.33 0 35C0 15.67 15.67 0 35 0C54.33 0 70 15.67 70 35ZM5.80061 35C5.80061 51.1264 18.8736 64.1994 35 64.1994C51.1264 64.1994 64.1994 51.1264 64.1994 35C64.1994 18.8736 51.1264 5.80061 35 5.80061C18.8736 5.80061 5.80061 18.8736 5.80061 35Z" stroke="#67E8F9" mask="url(#path-2-inside-1_47_104)"/>
                </svg>
            </div>
            <div class="text-white text-center text-lg mt-3">Ожидание соперника...</div>
            <button v-if="!accepted" @click.prevent="acceptGame" class="block mt-3 m-auto w-48 h-10 border border-lime-500 text-lime-500 bg-inherit rounded-lg hover:bg-lime-500 hover:text-white hover:border hover:border-lime-500 transition ease-in">ПОДТВЕРДИТЬ</button>
            <div v-if="accepted" class="text-lime-500 text-xl mt-3 text-center">Игра подтверждена</div>
        </div>
    </div>
</transition>

</template>
<style scoped>
.modal-overlay {
  position: fixed;
  z-index: 999;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  background: rgba(41, 41, 41, 0.7);
}

.modal-window {
    width: 548px;
    height: 275px;
    z-index: 1001;
    background: #1e1e1e;
    border-radius: 20px;
    box-shadow: 0 4px 24px 0 rgba(103, 232, 249, 0.5);
    transition: all 0.3s ease;
}
.modal-enter-from .modal-window {
  opacity: 0;
  transform: scale(0.5);
}
.modal-leave-to .modal-window {
  opacity: 0;
  transform: scale(1.5);
}
@media (max-width: 401px){
 .text-lg{
     font-size: 1rem;
 }
 .text-xl{
  font-size: 1.1rem;
 }
}
</style>
