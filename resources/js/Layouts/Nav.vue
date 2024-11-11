<script>
import { Link } from '@inertiajs/vue3';
import NotificationComponent from '@/Components/Models/Notification.vue';
import NotificationMessageComponent from '@/Components/Models/NotificationMessage.vue';
import CreateLobbyComponent from '@/Components/Models/CreateLobby.vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';
export default{
    name: "NavComponent",
    components: {
        Link,
        NotificationComponent,
        NotificationMessageComponent,
        CreateLobbyComponent
    },
    props: [
        'nowChat',
    ],
    data() {
        return {
            userId: null,
            AudioPath: `${import.meta.env.BASE_URL}audios/friend-invite.mp3`,
            AudioPathMessage: `${import.meta.env.BASE_URL}audios/message-sent.mp3`,
            playAudioFriend: false,
            playAudioMessage: false,
            logoPath: `${import.meta.env.BASE_URL}logo.svg`,
            openBurger: false,
            notificationModal: false,
            notificationMessageModal: false,
            notificationFriend: [],
            notificationMessage: [],
            notificationInviteGame: [],
            openCreateLobyyModal: false

        }
  },
  mounted(){
        this.getUserId();
        this.getRequestsFriend();
},
  methods: {
    closeNotificationModal(data) {
        this.notificationModal = false
    },
    closeNotificationMessageModal(data) {
        this.notificationMessageModal = false
    },
    getUserId(){
        axios.post('/getuser').then(res => {
            this.userId = res.data.id
            this.initializeWebSocket();
        })
    },
    initializeWebSocket() {
        window.Echo.channel(`notification-friend.${this.userId}`).listen('.notification-friend', res => {
            this.notificationFriend = [...this.notificationFriend, res.notificationFriend].reverse();
            this.playAudioFriend = true;
            setTimeout(() => {
                this.playAudioFriend = false
            }, 1000);
        });
        window.Echo.channel(`notification-message.${this.userId}`).listen('.notification-message', res => {
            if(res.notificationMessage.chat_id == this.nowChat){
                //
            } else{
                this.notificationMessage = [...this.notificationMessage, res.notificationMessage].reverse();
                this.playAudioMessage = true;
                setTimeout(() => {
                    this.playAudioMessage = false
                }, 1000); 
            }
            
        });
        window.Echo.channel(`game-invite.${this.userId}`).listen('.game-invite', res => {
            this.notificationInviteGame = [...this.notificationInviteGame, res.gameInvite].reverse();
            this.playAudioFriend = true;
            setTimeout(() => {
                this.playAudioMessage = false
            }, 1000); 
        });
        window.Echo.channel(`lobby-game.${this.userId}`).listen('.lobby-game', res => {
            router.visit(`/game/${res.lobby_start}`);
        });
    },
    getRequestsFriend() {
        axios.post('/getRequestsFriend').then(res =>{
            this.notificationFriend = [...this.notificationFriend, ...res.data.requests].reverse();
        })
    }
  },
}
</script>
<template>
    <NotificationComponent :openModel="notificationModal" :notificationIviteGameInfo="notificationInviteGame" :notificationFriendInfo="notificationFriend" @close-modal="closeNotificationModal"></NotificationComponent>
    <NotificationMessageComponent :openModel="notificationMessageModal" :notificationMessageInfo="notificationMessage" @close-modal-message="closeNotificationMessageModal"></NotificationMessageComponent>
    <CreateLobbyComponent v-if="openCreateLobyyModal" style="z-index: 9999" @close-modal-create="openCreateLobyyModal = false"></CreateLobbyComponent>
    <audio :src="AudioPath" autoplay="autoplay" v-if="playAudioFriend"></audio>
    <audio :src="AudioPathMessage" autoplay="autoplay" v-if="playAudioMessage"></audio>
    <transition name="modal">
        <div v-if="openBurger" class="modal-overlay" @click.self="openBurger = false">
            <div class="modal-window">
                <div class="flex justify-end border-b border-white p-4">
                    <div @click.prevent="openBurger = false" class="cursor-pointer">
                        <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.405643 0.44083C0.665712 0.181455 1.01825 0.0357677 1.38581 0.0357677C1.75338 0.0357677 2.10592 0.181455 2.36599 0.44083L12.4821 10.5425L22.5982 0.44083C22.7252 0.30475 22.8783 0.195604 23.0484 0.119903C23.2186 0.0442016 23.4023 0.0034968 23.5885 0.000215555C23.7747 -0.00306569 23.9597 0.0311439 24.1324 0.100805C24.3051 0.170465 24.462 0.274149 24.5937 0.405671C24.7255 0.537194 24.8293 0.693859 24.8991 0.866322C24.9688 1.03878 25.0031 1.22351 24.9998 1.40948C24.9965 1.59546 24.9557 1.77886 24.8799 1.94876C24.8041 2.11866 24.6948 2.27157 24.5585 2.39837L14.4424 12.5L24.5585 22.6016C24.6948 22.7284 24.8041 22.8813 24.8799 23.0512C24.9557 23.2211 24.9965 23.4045 24.9998 23.5905C25.0031 23.7765 24.9688 23.9612 24.8991 24.1337C24.8293 24.3061 24.7255 24.4628 24.5937 24.5943C24.462 24.7259 24.3051 24.8295 24.1324 24.8992C23.9597 24.9689 23.7747 25.0031 23.5885 24.9998C23.4023 24.9965 23.2186 24.9558 23.0484 24.8801C22.8783 24.8044 22.7252 24.6953 22.5982 24.5592L12.4821 14.4575L2.36599 24.5592C2.10305 24.8038 1.75528 24.937 1.39594 24.9307C1.03661 24.9244 0.69376 24.779 0.439631 24.5252C0.185502 24.2715 0.0399343 23.9291 0.0335942 23.5703C0.0272541 23.2115 0.160637 22.8642 0.405643 22.6016L10.5217 12.5L0.405643 2.39837C0.145896 2.13867 0 1.78664 0 1.4196C0 1.05256 0.145896 0.700527 0.405643 0.44083Z" fill="white" />
                        </svg>
                    </div>
                </div>
                <Link :href="route('start')">
                    <div class="text-white text-xl text-center border-b border-white p-3 cursor-pointer">НА ГЛАВНУЮ</div>
                </Link>
                <Link :href="route('user.friend')">
                    <div class="text-white text-xl text-center border-b border-white p-3 cursor-pointer">ДРУЗЬЯ</div>
                </Link>
                <div @click.prevent="openCreateLobyyModal = true, openBurger = false" class="text-white text-xl text-center border-b border-white p-3 cursor-pointer">СОЗДАТЬ ЛОББИ</div>
                <Link :href="route('about')">
                <div class="text-white text-xl text-center border-b border-white p-3 cursor-pointer">О НАС</div>
                </Link>
            </div>
        </div>
    </transition>
    <div style="height: 64px;" class="w-full bg-nav flex">
        <div class="w-1/2 flex justify-between items-center">
            <div @click.prevent="openBurger = true" class="p-2 cursor-pointer">
                <svg width="38" height="30" viewBox="0 0 38 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_19_254)">
                    <path d="M1 1H37" stroke="black" stroke-width="2" stroke-linecap="round"/>
                    <path d="M1 15H37" stroke="black" stroke-width="2" stroke-linecap="round"/>
                    <path d="M1 29H37" stroke="black" stroke-width="2" stroke-linecap="round"/>
                    </g>
                    <defs>
                    <clipPath id="clip0_19_254">
                    <rect width="38" height="30" fill="white"/>
                    </clipPath>
                    </defs>
                </svg>
            </div>
        </div>
        <div class="m-auto">
            <img :src="logoPath" alt="logo">
        </div>
        <div class="flex justify-end items-center w-1/2">
            <div class="flex">
                <div @click.prevent="notificationModal = true" class="mr-4 relative cursor-pointer">
                    <div v-if="notificationFriend.length || notificationInviteGame.length" class="w-3 h-3 rounded-full bg-red-500 absolute left-3.5 text-white text-xs flex justify-center items-center">{{ notificationFriend.length + notificationInviteGame.length }}</div>
                    <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.05855 8.81894C3.05855 6.54567 3.99964 4.3655 5.67481 2.75806C7.34997 1.15061 9.62198 0.247559 11.991 0.247559C14.3601 0.247559 16.6321 1.15061 18.3072 2.75806C19.9824 4.3655 20.9235 6.54567 20.9235 8.81894V9.77132C20.9235 12.4672 21.9822 14.923 23.7263 16.7808C23.8349 16.8963 23.9124 17.0357 23.952 17.1866C23.9915 17.3375 23.9919 17.4954 23.9531 17.6466C23.9144 17.7977 23.8376 17.9374 23.7295 18.0534C23.6215 18.1694 23.4854 18.2583 23.3333 18.3122C21.2901 19.036 19.1516 19.5694 16.9403 19.8906C16.9901 20.5435 16.899 21.1992 16.6729 21.8168C16.4467 22.4344 16.0903 23.0006 15.6258 23.4801C15.1614 23.9596 14.5989 24.342 13.9735 24.6035C13.3481 24.8651 12.6732 25 11.991 25C11.3088 25 10.634 24.8651 10.0086 24.6035C9.38316 24.342 8.82067 23.9596 8.35622 23.4801C7.89177 23.0006 7.53533 22.4344 7.30917 21.8168C7.08301 21.1992 6.99198 20.5435 7.04177 19.8906C4.86046 19.5734 2.71844 19.0441 0.648765 18.311C0.496759 18.2571 0.360804 18.1684 0.252774 18.0525C0.144744 17.9366 0.0679248 17.7971 0.0290252 17.6462C-0.00987439 17.4952 -0.00967112 17.3374 0.0296174 17.1866C0.0689059 17.0357 0.146085 16.8964 0.254413 16.7808C2.06315 14.8591 3.06262 12.3608 3.05855 9.77132V8.81894ZM9.01617 20.1205C8.99925 20.5055 9.06365 20.8898 9.20552 21.2503C9.34739 21.6108 9.56379 21.94 9.8417 22.2182C10.1196 22.4963 10.4533 22.7177 10.8226 22.8689C11.192 23.0201 11.5894 23.098 11.991 23.098C12.3926 23.098 12.79 23.0201 13.1594 22.8689C13.5288 22.7177 13.8624 22.4963 14.1403 22.2182C14.4182 21.94 14.6346 21.6108 14.7765 21.2503C14.9184 20.8898 14.9828 20.5055 14.9659 20.1205C12.9866 20.2915 10.9954 20.2915 9.01617 20.1205Z" fill="white" />
                    </svg>
                </div>
                <div @click.prevent="notificationMessageModal = true" class="mr-8 relative cursor-pointer">
                    <div v-if="notificationMessage.length" class="w-3 h-3 rounded-full bg-red-500 absolute left-2/3 flex justify-center items-center text-white text-xs">{{ notificationMessage.length }}</div>
                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.95769 24.9554C3.72874 24.932 3.50078 24.8991 3.27436 24.8567C3.10807 24.8257 2.95257 24.7497 2.8235 24.6364C2.69444 24.523 2.59638 24.3763 2.53919 24.211C2.482 24.0457 2.4677 23.8676 2.49773 23.6947C2.52776 23.5218 2.60106 23.3602 2.71026 23.2262C3.23623 22.5814 3.59541 21.8077 3.75385 20.9784C3.78333 20.825 3.72564 20.5557 3.42821 20.2544C1.31282 18.1146 0 15.2121 0 11.9991C0 5.29292 5.67692 0 12.5 0C19.3231 0 25 5.29292 25 11.9991C25 18.7052 19.3231 23.9981 12.5 23.9981C11.4321 23.9981 10.3936 23.8688 9.40128 23.6262C7.77929 24.6862 5.86386 25.1539 3.95769 24.9554Z" fill="white" />
                    </svg>
                </div>
            </div>
            <Link :href="route('user.profile')" class="mr-2">ПРОФИЛЬ</Link>
        </div>
    </div>
</template>
<style scoped>
@media (max-width: 476px){
    .notification{
        flex-direction: column;
    }
    img{
        display: none;
    }
}
.modal-overlay {
   position: absolute;
   z-index: 20;
   width: 100%;
   height: 100%;
   transition: all 0.5s ease;
   background: rgba(0, 0, 0, 0.6);
}
.modal-window {
  position: relative;
  width: 330px;
  z-index: 30;
  height: 100%;
  background-color: #1e1e1e;
  transition: all 0.5s ease;
  opacity: 1;
  transform: translateX(0); /* Начальная позиция вне экрана */
}
.modal-enter-from .modal-window {
    transform: translateX(-100%); /* Плавный вход */
}
.modal-leave-to .modal-window {
    transform: translateX(-100%); /* Плавный выход */
}
</style>