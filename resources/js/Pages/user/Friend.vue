<script>
import NavComponent from "@/Layouts/Nav.vue";
import BlockedRequestComponent from "@/Components/Models/BlockedRequest.vue";
import axios from "axios";
import { router } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3';
export default {
    name: "FriendComponent",
    components: {
        NavComponent,
        BlockedRequestComponent,
        Link
    },
    props: [
        'blocked',
        'requestsFriend',
        'friendList',
        'chats'
    ],
    data(){
        return {
            cantInvite: null,
            searchQuery: '',
            friendModal: false,
            requestsFriendModal: false,
            findUserObj: {
                name: '',
                errorName: null
            },
            user: {
                id: null,
                name: null,
                successAdded: null,
                sentRequest: null,
                haveFriend: null,
            },
            blockedRequestText: null,
            blockedModal: this.blocked,
            i: 0,
            fullFriendList: null
        }
    },
    computed: {
    filteredFriends() {
      return this.friendList.filter(friend => {
        return friend.name.toLowerCase().includes(this.searchQuery.toLowerCase());
      });
    }
  },
    methods: {
        findUser(){
            axios.post('/friends/find',{
                name: this.findUserObj.name
            }).then(res => {
                this.findUserObj.errorName = null;
                this.cantInvite = null;
                if(res.data.cantAdd){
                    this.cantInvite = 'Данному пользователю нельзя отправлять запросы в друзья';
                }
                if(res.data.haveRequest){
                    this.findUserObj.errorName = res.data.haveRequest
                } else if(res.data.error){
                    this.findUserObj.errorName = res.data.error;
                }else{
                    this.user = res.data;
                } 
                if(res.data.blockRequest){
                    this.blockedRequestText = res.data.blockRequest;
                    this.friendModal = false;
                    this.blockedModal = true;
                }
            }).catch(err => {
                this.findUserObj.errorName = err.response.data.errors.name
                this.user.id = null;
                this.user.name = null;
            });
        },
        inviteFriend(){
            if(this.user.id != null){
                axios.post('/friends/invite', {
                id: this.user.id
                }).then(res => {
                    this.user.successAdded = null
                    this.user.sentRequest = null
                    this.blockedRequestText = null
                    if(res.data.success){
                        this.user.successAdded = true
                    } else if(res.data.sent){
                        this.user.sentRequest = true
                    } else if(res.data.blockRequest){
                        this.blockedRequestText = res.data.blockRequest;
                        this.friendModal = false;
                        this.blockedModal = true;
                    } else if(res.data.friendHave){
                        this.user.haveFriend = res.data.friendHave
                    }
                })
            }
        },
        addFriend(id){
            axios.post('/friends/add',{
                id: id
            }).then(res => {
                if(res.data.added){
                    router.visit('/friends');
                }
                if(res.data.fullFriendList){
                    this.fullFriendList = res.data.fullFriendList
                }
            })
        },
        deleteFriend(id){
            axios.post('/friends/delete',{
                id: id
            }).then(res =>{
                if(res.data.deleted){
                    router.visit('/friends');
                }
            })
        },

        refuseFriend(id){
            axios.post('/friends/refuse', {
                id: id
            }).then(res =>{
                if(res.data.refused){
                    router.visit('/friends');
                }
            })
        },
        getChatId(friendId) {
            const chat = this.chats.find(chat => chat.user_id === friendId);
            return chat ? chat.chat_id : 'Нет чата';
        },
        openChat(chatId, friendId){
            axios.post(`/chat/open/${chatId}`, {
                friendId: friendId
            }).then(res =>{
                router.visit(`/chat/${res.data.info.chatId}?friendId=${res.data.info.friendInChat.id}&friendName=${res.data.info.friendInChat.name}`);
            })
        }

    }
}
</script>
<template>
<div class="h-full flex flex-col content">
    <div style="height: 64px">
    <NavComponent></NavComponent>
    </div>
    <BlockedRequestComponent v-if="blockedRequestText" :message="blockedRequestText"></BlockedRequestComponent>
    <div class="container border-r border-l border-cyan-300 h-full">
        <div class="flex justify-between items-center px-4 pt-6 mb-6 up-content">
            <div>
                <div class="text-white text-xl">Ваш список друзей</div>
                <input v-model="searchQuery" type="text" placeholder="Быстрый поиск" class=" placeholder:text-white placeholder:opacity-75 w-80 p-1 text-lg bg-inherit border border-lime-500 h-10 rounded-lg focus:outline-none mt-4 text-white">
            </div>
            <div class="btns flex">
                <button v-if="requestsFriend.length" @click.prevent="requestsFriendModal = true" class="have-request mr-4 border-indigo-500 bg-indigo-500 text-white text-lg pb-1 w-60 h-10 rounded-lg hover:bg-inherit hover:text-indigo-500 hover:border hover:border-indigo-500 transition ease-in">Есть предложения в друзья</button>
                <button @click.prevent="friendModal = true" class="border btn-find border-lime-500 text-lime-500 text-lg pb-1 w-60 h-10 rounded-lg bg-inherit hover:bg-lime-500 hover:text-white transition ease-in">Добавить в друзья</button>
            </div>
        </div>
        <div v-if="friendList.length">
            <div class="flex justify-between items-center border-t border-cyan-300 p-4" v-for="(friend, index) in filteredFriends" :key="index">
                <div>
                    <Link :href="route('user.show', friend.id)">
                        <div class="text-white text-xl">{{ $truncate(friend.name, 20, '...') }}</div>
                    </Link>
                </div>
                <div class="btns-m-del flex">
                    <button @click.prevent="openChat(getChatId(friend.id), friend.id)" v-if="friend.settings[0].message_can" class="mr-4 pb-1 border-indigo-500 bg-indigo-500 text-white text-lg w-32 h-10 rounded-lg hover:bg-inherit hover:text-indigo-500 hover:border hover:border-indigo-500 transition ease-in">Сообщения</button>
                    <div v-else class="text-orange-300 text-sm inline mr-4">Пользователь ограничил отправку сообщений</div>
                    <button @click.prevent="deleteFriend(friend.id)" class="border btn-del-fr pb-1 border-red-500 text-red-500 text-lg w-32 h-10 rounded-lg bg-inherit hover:bg-red-500 hover:text-white transition ease-in">Удалить</button>
                </div>
            </div>
        </div>
        <div v-else class="border-t border-cyan-300">
            <div class="text-white text-xl text-center mt-6">Список друзей пуст</div>
        </div>
    </div>
</div>
    <transition name="modal">
        <div v-if="friendModal && blockedModal != true" class="modal-overlay" @click.self="friendModal = false">
            <div class="modal-window modal-friend">
                <div class="flex justify-end">
                    <div class="p-4 cursor-pointer" @click="friendModal = false">
                        <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.405643 0.44083C0.665712 0.181455 1.01825 0.0357677 1.38581 0.0357677C1.75338 0.0357677 2.10592 0.181455 2.36599 0.44083L12.4821 10.5425L22.5982 0.44083C22.7252 0.30475 22.8783 0.195604 23.0484 0.119903C23.2186 0.0442016 23.4023 0.0034968 23.5885 0.000215555C23.7747 -0.00306569 23.9597 0.0311439 24.1324 0.100805C24.3051 0.170465 24.462 0.274149 24.5937 0.405671C24.7255 0.537194 24.8293 0.693859 24.8991 0.866322C24.9688 1.03878 25.0031 1.22351 24.9998 1.40948C24.9965 1.59546 24.9557 1.77886 24.8799 1.94876C24.8041 2.11866 24.6948 2.27157 24.5585 2.39837L14.4424 12.5L24.5585 22.6016C24.6948 22.7284 24.8041 22.8813 24.8799 23.0512C24.9557 23.2211 24.9965 23.4045 24.9998 23.5905C25.0031 23.7765 24.9688 23.9612 24.8991 24.1337C24.8293 24.3061 24.7255 24.4628 24.5937 24.5943C24.462 24.7259 24.3051 24.8295 24.1324 24.8992C23.9597 24.9689 23.7747 25.0031 23.5885 24.9998C23.4023 24.9965 23.2186 24.9558 23.0484 24.8801C22.8783 24.8044 22.7252 24.6953 22.5982 24.5592L12.4821 14.4575L2.36599 24.5592C2.10305 24.8038 1.75528 24.937 1.39594 24.9307C1.03661 24.9244 0.69376 24.779 0.439631 24.5252C0.185502 24.2715 0.0399343 23.9291 0.0335942 23.5703C0.0272541 23.2115 0.160637 22.8642 0.405643 22.6016L10.5217 12.5L0.405643 2.39837C0.145896 2.13867 0 1.78664 0 1.4196C0 1.05256 0.145896 0.700527 0.405643 0.44083Z" fill="white" />
                        </svg>
                    </div>
                </div>
                <div class="px-4">
                    <div class="flex justify-between items-center search-items">
                        <div class="text-white text-xl">Поиск игроков</div>
                        <div>
                            <input v-model="findUserObj.name" type="text" placeholder="Впишите ник" class="input-search-user block w-80 focus:outline-none text-white text-xl p-1 pb-1 bg-inherit border border-cyan-300 h-10 rounded-lg">
                            <div v-if="findUserObj.errorName" class="text-red-500 text-sm absolute">{{ findUserObj.errorName[0] }}</div>
                            <div class="text-orange-300 text-sm absolute" > {{ cantInvite }}</div>
                        </div>
                        <button @click.prevent="findUser" class="btn-find-user border border-lime-500 text-lime-500 text-xl pb-1 w-32 h-10 rounded-lg bg-inherit hover:bg-lime-500 hover:text-white transition ease-in">найти</button>
                    </div>
                </div>
                <div v-if="user.id && user.name">
                    <div class="text-white text-xl px-4 mt-4">Результат:</div>
                    <div class="border-t border-white mx-4 mt-2"></div>
                    <div class="p-4 flex items-center mt-2">
                        <div class="text-white text-xl mr-6">{{ $truncate(user.name, 15, '...') }}</div>
                        <Link :href="route('user.show', user.id)">
                            <button class="mr-6 border-indigo-500 bg-indigo-500 text-white text-lg pb-1 w-32 h-10 rounded-lg hover:bg-inherit hover:text-indigo-500 hover:border hover:border-indigo-500 transition ease-in">профиль</button>
                        </Link>
                        <button @click.prevent="inviteFriend" class="border-lime-500 bg-lime-500 text-white text-lg pb-1 w-32 h-10 rounded-lg hover:bg-inherit hover:text-lime-500 hover:border hover:border-lime-500 transition ease-in">добавить</button>
                    </div>
                    <div class="text-lime-500 text-sm ml-4" v-if="user.successAdded">Предложение дружбы отправлено</div>
                    <div class="text-orange-300 text-sm ml-4" v-if="user.sentRequest">Вы уже отправляли предложение в друзья этому пользователю</div>
                    <div class="text-orange-300 text-sm ml-4" v-if="user.haveFriend"> {{ user.haveFriend[0] }}</div>
                </div>
            </div>
        </div>
    </transition>
    <transition name="modal">
        <div v-if="requestsFriendModal" class="modal-overlay" @click.self="requestsFriendModal = false">
            <div class="modal-window" style="height: 380px;">
                <div class="text-xl text-white py-2 border-b border-cyan-300 text-center">предложения в друзья</div>
                <div class="overflow-y-auto" style="height: 275px;">
                    <div class="grid grid-cols-3 p-4 relative" v-for="(requests, index) in requestsFriend" :key="index">
                        <Link :href="route('user.show', requests.user.id)">
                            <div class="text-xl text-white">{{ $truncate(requests.user.name, 12, '...') }}</div>
                        </Link>
                        <button @click.prevent="refuseFriend(requests.user.id)" class="justify-self-center border border-red-500 pb-1 text-red-500 text-lg w-32 h-10 rounded-lg bg-inherit hover:bg-red-500 hover:text-white transition ease-in">отказать</button>
                        <button @click.prevent="addFriend(requests.user.id)" class="justify-self-end border-lime-500 pb-1 bg-lime-500 text-white text-lg w-32 h-10 rounded-lg hover:bg-inherit hover:text-lime-500 hover:border hover:border-lime-500 transition ease-in">принять</button>
                        <p v-if="this.fullFriendList" class="text-red-500 text-sm absolute top-3/4">{{ fullFriendList }}</p>
                    </div>
                    
                </div>
                <div class="border-t border-cyan-200 py-2 text-center">
                    <button @click.prevent="requestsFriendModal = false" class="border-indigo-500 bg-indigo-500 text-white text-lg pb-1 w-44 h-10 rounded-lg hover:bg-inherit hover:text-indigo-500 hover:border hover:border-indigo-500 transition ease-in">закрыть</button>
                </div>
            </div>
        </div>
    </transition>
</template>
<style scoped>
.h-full{
    min-height: 100vh;
    height: auto;
}
@media (max-width: 1023px){
    .btns{
        flex-direction: column;
    }
    .btn-find{
        margin-top: 10px;
    }
}
@media (max-width: 640px){
    .have-request{
        margin-right: 0;
        margin-top: 10px;
    }
    .btn-find{
        margin-top: 10px;
    }
    .container{
        width: 400px;
    }
    .up-content{
        flex-direction: column;
    }
    .btns{
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .text-xl{
        font-size: 1.1rem;
    }
    .text-lg{
        font-size: 1rem;
    }
    .w-32 {
        width: 6rem;
    }
}
@media (max-width: 400px){
    .container{
        width: 320px;
    }
    .btns-m-del{
        flex-direction: column;
    }
    .btn-del-fr{
        margin-top: 10px;
    }
}
@media (max-width: 525px){
    .input-search-user{
        width: 15rem;
    }
}
@media (max-width: 435px){
    .search-items{
        flex-direction: column;
    }
    .modal-friend{
        height: 300px;
    }
    .btn-find-user{
        margin-top: 10px;
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
  width: 700px;
  height: 327px;
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