<script>
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import { router } from '@inertiajs/vue3';
export default {
    name: "NotificationComponent",
    props: [
        'openModel',
        'notificationFriendInfo',
        'notificationIviteGameInfo'
    ],
    components: {
        Link
    },
    data() {
        return {
            notificationModalLocal: this.openModel,
            fullFriendList: null,
            inGame: []
        }
    },
    methods:{
        closeModal() {
            this.$emit('close-modal', true);
        },
        addFriend(id){
            axios.post('/friends/add',{
                id: id
            }).then(res => {
                if(res.data.added){
                    let request = this.notificationFriendInfo.find(notification => notification.user.id == id)
                    this.notificationFriendInfo.splice(request, 1);
                }
                if(res.data.fullFriendList){
                    this.fullFriendList = res.data.fullFriendList
                }
            });
        },
        refuseFriend(id){
            axios.post('/friends/refuse', {
                id: id
            }).then(res =>{
                if(res.data.refused){
                    let request = this.notificationFriendInfo.find(notification => notification.user.id == id)
                    this.notificationFriendInfo.splice(request, 1);
                }
            })
        },
        acceptGame(friendId, time){
            axios.post('/game/createWithFriend', {
                friendId: friendId,
                time: time 
            }).then(res => {
                if(res.data.game){
                    router.visit(`/game/${res.data.game.id}`);
                }
                if(res.data.in_game){
                    this.inGame.push(friendId);
                }
            });
        }
    }

}
</script>
<template>
    <transition name="modal">
    <div v-if="openModel" class="modal-overlay" @click.self="closeModal">
        <div class="modal-window">
            <div class="text-white text-xl text-center border-b border-cyan-300 p-2">УВЕДОМЛЕНИЯ</div>
            <div class="h-96 pr-4 pl-4 pb-4 overflow-y-auto">
                <div v-if="notificationFriendInfo.length">
                    <div class="w-full h-20 rounded-lg bg-custom-black shadow-custom-white mt-6" v-for="notification in notificationFriendInfo" :key="notification">
                        <div class="text-white text-lg pl-4">{{ notification.text }} <Link :href="route('user.show', notification.user.id)" class="text-indigo-500 text-lg">{{ $truncate(notification.user.name, 12, '...') }}</Link></div>
                        <div class="flex justify-between items-center px-4 mt-2">
                            <button @click.prevent="refuseFriend(notification.user.id)" class="border border-red-500 pb-1 text-red-500 text-lg w-28 h-8 rounded-lg bg-inherit hover:bg-red-500 hover:text-white transition ease-in">отказать</button>
                            <p v-if="fullFriendList" class="text-red-500 text-sm">{{ fullFriendList }}</p>
                            <button @click.prevent="addFriend(notification.user.id)" class="border border-lime-500 pb-1 bg-lime-500 text-white text-lg w-28 h-8 rounded-lg hover:bg-inherit hover:text-lime-500 hover:border hover:border-lime-500 transition ease-in">принять</button>
                        </div>
                    </div>
                </div>
                <div v-if="notificationIviteGameInfo.length">
                    <div class="w-full h-20 rounded-lg bg-custom-black shadow-custom-white mt-6" v-for="notificationGame in notificationIviteGameInfo" :key="notificationGame">
                        <div class="text-white text-lg pl-4">{{ notificationGame.text }} <Link :href="route('user.show', notificationGame.id)" class="text-indigo-500 text-lg">{{ $truncate(notificationGame.name, 12, '...') }}</Link></div>
                        <div class="flex justify-end items-center px-4 mt-2">
                            <div class="text-sm text-orange-300 mr-4" v-if="inGame.includes(notificationGame.id)">Уже в игре</div>
                            <button @click.prevent="acceptGame(notificationGame.id, notificationGame.time)" class="border border-lime-500 pb-1 bg-lime-500 text-white text-lg w-28 h-8 rounded-lg hover:bg-inherit hover:text-lime-500 hover:border hover:border-lime-500 transition ease-in">принять</button>
                        </div>
                    </div>
                </div>
                <div v-if="notificationIviteGameInfo.length == 0 && notificationFriendInfo.length == 0" class="text-white text-center text-lg mt-4">
                    Уведомлений нет
                </div>
            </div>
            <div class="border-t border-cyan-300 p-2">
                <button @click.prevent="closeModal" class="block m-auto w-28 h-8 border border-indigo-500 text-indigo-500 bg-inherit rounded-lg hover:bg-indigo-500 hover:text-white hover:border hover:border-indigo-500 transition ease-in">ЗАКРЫТЬ</button>
            </div>
        </div>
    </div>
    </transition>
</template>
<style scoped>
@media (max-width: 510px){
    .text-xl{
        font-size: 1.1rem;
    }
    .h-20{
        height: 7rem;
    }
}
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
  width: 550px;
  height: 481px;
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
</style>