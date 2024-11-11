<script>
import axios from 'axios';
import { Link } from '@inertiajs/vue3';

export default {
    name: "CreateLobbyComponent",
    components: {
        Link
    },
    data(){
        return {
            sizeBoard: "3",
            runningTime: "5",
            friendList: [],
            friednInvited: [],
            searchQuery: '',
        }
    },
    mounted(){
        this.getFriendList();
    },
    computed: {
    filteredFriends() {
      return this.friendList.filter(friend => {
        return friend.name.toLowerCase().includes(this.searchQuery.toLowerCase());
      });
    }
  },
    methods: {
        closeModal() {
            this.$emit('close-modal-create', true);
        },
        getFriendList(){
            axios.post('/getFriendList').then(res=>{
                this.friendList = res.data.friendList;
            })
        },
        inviteFriend(id){
            axios.post('/game/invite', {
                user_id: id,
                time: this.runningTime,
            });
        }
    }
}
</script>
<template>
<transition name="modal">
    <div class="modal-overlay" @click.self="closeModal">
        <div class="modal-window">
            <div class="text-white text-xl text-center p-2 border-b border-cyan-300">СОЗДАНИЕ ЛОББИ</div>
            <div class="px-24 mt-10">
                <div class="flex justify-between items-center">
                    <div class="text-white text-xl mr-6">Выберите размер поля: </div>
                    <select v-model="sizeBoard" class="rounded-md w-20 text-center outline-none">
                        <option value="3">3 на 3</option>
                    </select>
                </div>
                <div class="flex justify-between items-center mt-6">
                    <div class="text-white text-xl mr-6">Выберите время хода: </div>
                    <select v-model="runningTime" class="rounded-md w-20 text-center outline-none">
                        <option value="5">5 сек</option>
                        <option value="10">10 сек</option>
                    </select>
                </div>
                <input v-model="searchQuery" type="text" placeholder="Быстрый поиск" class=" placeholder:text-white placeholder:opacity-75 w-80 p-1 text-lg bg-inherit border border-lime-500 h-10 rounded-lg focus:outline-none mt-4 text-white">
                <div class="text-lg text-white mt-2">Список друзей:</div>
                <div class="w-full h-36 border border-cyan-300 overflow-y-auto px-2 pb-2">
                    <div v-if="!friendList.length" class="flex justify-center z-10 mt-8">
                        <svg class="animate-spin" width="70" height="70" viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M70 35C70 54.33 54.33 70 35 70C15.67 70 0 54.33 0 35C0 15.67 15.67 0 35 0C54.33 0 70 15.67 70 35ZM5.68172 35C5.68172 51.192 18.808 64.3183 35 64.3183C51.192 64.3183 64.3183 51.192 64.3183 35C64.3183 18.808 51.192 5.68172 35 5.68172C18.808 5.68172 5.68172 18.808 5.68172 35Z" fill="#374151"/>
                            <mask id="path-2-inside-1_47_104" fill="white">
                            <path d="M70 35C70 54.33 54.33 70 35 70C15.67 70 0 54.33 0 35C0 15.67 15.67 0 35 0C54.33 0 70 15.67 70 35ZM5.80061 35C5.80061 51.1264 18.8736 64.1994 35 64.1994C51.1264 64.1994 64.1994 51.1264 64.1994 35C64.1994 18.8736 51.1264 5.80061 35 5.80061C18.8736 5.80061 5.80061 18.8736 5.80061 35Z"/>
                            </mask>
                            <path d="M70 35C70 54.33 54.33 70 35 70C15.67 70 0 54.33 0 35C0 15.67 15.67 0 35 0C54.33 0 70 15.67 70 35ZM5.80061 35C5.80061 51.1264 18.8736 64.1994 35 64.1994C51.1264 64.1994 64.1994 51.1264 64.1994 35C64.1994 18.8736 51.1264 5.80061 35 5.80061C18.8736 5.80061 5.80061 18.8736 5.80061 35Z" stroke="#67E8F9" mask="url(#path-2-inside-1_47_104)"/>
                        </svg>
                    </div>
                    <div class="flex justify-between items-center mt-4 shadow-custom-white rounded-lg h-12 px-2" v-for="(friend, index) in filteredFriends" :key="index">
                        <Link :href="route('user.show', friend.id)">
                            <div class="text-lg text-white">{{ friend.name }}</div>
                        </Link>
                        <button @click.prevent="inviteFriend(friend.id), friednInvited.push(friend.id)" v-if="!friednInvited.includes(friend.id)" class="border-lime-500 border w-24 h-8 rounded-lg bg-inherit text-lime-500 hover:bg-lime-500 hover:text-white hover:border hover:border-lime-500 transition ease-in">пригласить</button>
                        <div v-if="friednInvited.includes(friend.id)" class="text-lime-500 text-lg">Приглашён</div>
                    </div>
                </div>
                <div class="flex justify-center items-center">
                    <button @click.prevent="closeModal" class="border-indigo-500 mt-6 border w-36 h-10 rounded-lg bg-inherit text-indigo-500 hover:bg-indigo-500 hover:text-white hover:border hover:border-indigo-500 transition ease-in">ОТМЕНА</button>                </div>
                </div>
        </div>
    </div>
</transition>
</template>
<style scoped>
@media (max-width: 520px){
    .px-24{
        padding-left: 2rem;
        padding-right: 2rem;
    }
}
@media (max-width: 390px){
    .px-24{
        padding-left: 1rem;
        padding-right: 1rem;
    }
}
@media (max-width: 355px){
    .text-xl{
        font-size: 1.1rem;
    }
    .w-80{
        width: 15rem;
    }
}
.modal-overlay {
    position: fixed;
    z-index: 4002;
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
    height: 475px;
    z-index: 4003;
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
