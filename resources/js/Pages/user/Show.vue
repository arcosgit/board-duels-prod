<script>
import NavComponent from '@/Layouts/Nav.vue';
import StatisticsProfileComponent from '@/Components/Profile/Statistics.vue';
import HistoryProfileComponent from '@/Components/Profile/History.vue';
import BlockedRequestComponent from '@/Components/Models/BlockedRequest.vue';
import { router } from '@inertiajs/vue3'
export default {
    name: "ShowComponent",
    components: {
        NavComponent,
        StatisticsProfileComponent,
        HistoryProfileComponent,
        BlockedRequestComponent
    },
    props: [
        'user',
        'friends',
        'chat'
    ],
    data(){
        return {
            choice: {
                statistics: true,
                history: false,
            },
            resultInvite: {
                successAdded: null,
                sentRequest: null,
                haveRequest: null,
            },
            blockedRequestText: null,
        }
    },
    methods:{
        truncate(str, len, symbol = '') {
            return str.length > len ? str.slice(0, len) + symbol : str
        },
        choiceItem(item){
            for(let elem in this.choice){
                if(elem == item){
                    this.choice[elem] = true
                }else{
                    this.choice[elem] = false
                }
            }
        },
        inviteFriend(){
            if(this.user.id != null){
                axios.post('/friends/invite', {
                id: this.user.id
                }).then(res => {
                    this.resultInvite.successAdded = null
                    this.resultInvite.sentRequest = null
                    this.blockedRequestText = null
                    if(res.data.success){
                        this.resultInvite.successAdded = true
                    } else if(res.data.sent){
                        this.resultInvite.sentRequest = true
                    } else if(res.data.blockRequest){
                        this.blockedRequestText = res.data.blockRequest;
                    } else if(res.data.haveRequest){
                        this.resultInvite.haveRequest = res.data.haveRequest
                    }
                })
            }
        },
        openChat(chatId, friendId){
            axios.post(`/chat/open/${chatId}`, {
                friendId: friendId
            }).then(res =>{
               router.visit(`/chat/${res.data.info.chatId}?friendId=${res.data.info.friendInChat.id}&friendName=${res.data.info.friendInChat.name}`);
            }).catch(err =>{
            })
        }
    },
}
</script>
<template>
    <NavComponent></NavComponent>
    <BlockedRequestComponent v-if="blockedRequestText" :message="blockedRequestText"></BlockedRequestComponent>
    <div class="container border-r border-l border-cyan-300 h-full">
        <div class="upper-content flex justify-between items-center">
            <div class="mt-6 ml-6">
                <div class="text-white text-xl"> {{ truncate(user.name, 35, '...') }} <span v-if="friends.length" class="text-lime-500 text-lg ml-4">✓ в друзьях</span></div>
                <div class="text-white text-lg mt-4">На сайте с {{ user.created }}</div>

            </div>
            <div class="pr-4 pt-6">
                <button @click.prevent="openChat(chat[0].id, user.id)" v-if="user.settings.message_can && friends.length" class="btn-msg mr-4 border-indigo-500 bg-indigo-500 text-white text-lg pb-1 w-60 h-10 rounded-lg hover:bg-inherit hover:text-indigo-500 hover:border hover:border-indigo-500 transition ease-in">Сообщения</button>
                <button @click="inviteFriend" v-if="!friends.length && user.settings.freind_add_can && blockedRequestText == null" class="border border-lime-500 text-lime-500 text-lg pb-1 w-60 h-10 rounded-lg bg-inherit hover:bg-lime-500 hover:text-white transition ease-in">Добавить в друзья</button>
                <div v-if="resultInvite.successAdded" class="w-60 h-auto text-wrap break-words absolute text-sm text-lime-500">Предложение дружбы отправлено</div>
                <div v-if="resultInvite.sentRequest" class="w-60 h-auto text-wrap break-words absolute text-sm text-orange-300">Вы уже отправляли предложение в друзья этому пользователю</div>
                <div v-if="resultInvite.haveRequest" class="w-60 h-auto text-wrap break-words absolute text-sm text-orange-300">Данный пользователь уже отправлял вам запрос в друзья</div>
            </div>
        </div>
        <div class="mt-6 w-full border-b border-t border-cyan-300">
            <div class="flex justify-between items-center">
                <div @click.prevent="choiceItem('statistics')" class="text-xl p-2 border-r border-cyan-300 text-center w-full cursor-pointer" :class="{'text-lime-500': choice.statistics, 'text-white hover:text-lime-100': !choice.statistics}">СТАТИСТИКА</div>
                <div @click.prevent="choiceItem('history')" class="text-xl p-2 w-full text-center cursor-pointer" :class="{'text-lime-500': choice.history, 'text-white hover:text-lime-100': !choice.history}">ИСТОРИЯ</div>
            </div>
        </div>
        <div v-if="!user.settings.statistics">
            <div v-if="choice.statistics">
                <StatisticsProfileComponent :statistics="user.statistics"></StatisticsProfileComponent>
            </div>
            <div v-if="choice.history">
                <HistoryProfileComponent :userId="user.id"></HistoryProfileComponent>
            </div>
        </div>
        <div v-else class="text-white text-xl mt-6 text-center">
            Пользователь скрыл свою статистику
        </div>
    </div>
</template>
<style scoped>
@media (max-width: 767px){
    .h-full{
        min-height: 100vh;
        height: auto;
    }
    .text-xl{
        font-size: 1rem;
    }
    .date{
        font-size: 16px;
    }
}
@media (max-width: 640px){
    .text-xl{
        font-size: 1rem;
    }
    .date{
        font-size: 16px;
    }
    .tabs{
        flex-direction: column;
    }
    .tab{
        border-right: none;
        border-bottom: 1px solid #67e8f9;
    }
    .container{
        width: 400px;
    }
    .upper-content{
        flex-direction: column;
    }
    .btn-msg{
        display: block;
        margin: 0 auto;
    }
    .ml-6{
        margin-left: 0;
    }
}
@media (max-width: 400px){
    .container{
        width: 320px;
    }

}
</style>