<script>
import NavComponent from "@/Layouts/Nav.vue";
import { Link, router } from '@inertiajs/vue3';
import axios from "axios";

export default {
    name: "ShowComponent",
    components: {
        NavComponent,
        Link
    },
    props:[
        'friendId',
        'friendName',
        'id',
        'blockMessage',
        'userLimitedMessages'
    ],
    data() {
        return {
            i: 1,
            maxMessage: false,
            message: '',
            errorMessage: null,
            allMessages: [],
            blockBtnSend: this.blockMessage,
            loading: true
        }
    },
    methods: {
        handlePaste(event){
            event.preventDefault();
            const text = (event.clipboardData || window.clipboardData).getData('text');

            // Вставляем чистый текст в блок contenteditable
            document.execCommand("insertText", false, text);
        },
        updateMessage(e){
            this.message = e.target.innerText; // Обновляем содержимое блока
        },
        store() {
            axios.post('/chat', {
                chatId: this.id,
                message: this.message,
                receiverId: this.friendId
            }).then(res => {
                this.errorMessage = null;
                this.$refs.messageBlock.innerText = '';
                if(res.data.permission){
                    this.errorMessage = res.data.permission;
                    this.blockBtnSend = true;
                    
                }else if(res.data.blockMessage){
                    this.errorMessage = res.data.blockMessage;
                    this.blockBtnSend = true;
                }else{
                    this.allMessages.unshift(res.data.message);
                }
            }).catch(err =>{
                this.errorMessage = err.response.data.errors.message;
                if(err.response.data.errors.chatId){
                    router.visit('/friends');
                }
            })
        },
        getMessages(){
            if(!this.maxMessage){
                axios.get(`/getmessages?page=${this.i}&chatId=${this.id}`).then(res =>{
                this.allMessages = [...this.allMessages, ...res.data.messages.data];
                this.i += 1;
                this.loading = false;
                if(res.data.messages.data.length <= 0){
                    this.maxMessage = true;
                    this.loading = false;
                }
                });
            }

        },
        handleScroll() {
            const scrollContainer = this.$refs.scrollContainer;

            // Определяем, достиг ли скролл низа
            if (scrollContainer.scrollTop + scrollContainer.clientHeight >= scrollContainer.scrollHeight) {
                this.loading = true;
                this.getMessages(); // Загружаем новые сообщения
            }
        }
    },
    mounted(){
        window.Echo.channel(`store-message.${this.id}`).listen('.store-message', res => {
            if (res.message.chat_id != this.id){
                console.log('Сообщение из другого чата');
            } else{
                this.allMessages.unshift(res.message);
            }
        });
        this.getMessages();
    }
}
</script>
<template>
    <NavComponent :nowChat="id"></NavComponent>
    <div class="main border-r border-l border-t border-cyan-300 m-auto mt-10 relative">
        <div class="w-full bg-indigo-500 flex items-center px-4" style="height: 60px;">
            <div class="text-white text-xl">Чат с <span class="italic">{{ friendName }}</span></div>
            <Link :href="route('user.show', friendId)">
                <button class="ml-4 w-24 h-10 bg-inherit border border-lime-500 text-lime-500 rounded-lg hover:bg-lime-500 hover:text-white transition ease-in">Профиль</button>
            </Link>
        </div>
        <div ref="scrollContainer" @scroll="handleScroll" style="height: 620px" class="overflow-y-auto">
            <div class="pr-4 pl-4 pb-4">
                <div class="w-full min-h-10 rounded-lg bg-custom-black mt-4 shadow-custom-blue" v-for="message in allMessages" :key="message.id">
                    <div class="text-lg p-2 text-wrap break-words" :class="message.user_id == friendId ? 'text-white' : 'text-lime-500'">{{ message.message }}</div>
                </div>
            </div>
            <div v-if="loading && maxMessage != true" class="flex justify-center absolute z-10 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 ">
                <svg class="animate-spin" width="70" height="70" viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M70 35C70 54.33 54.33 70 35 70C15.67 70 0 54.33 0 35C0 15.67 15.67 0 35 0C54.33 0 70 15.67 70 35ZM5.68172 35C5.68172 51.192 18.808 64.3183 35 64.3183C51.192 64.3183 64.3183 51.192 64.3183 35C64.3183 18.808 51.192 5.68172 35 5.68172C18.808 5.68172 5.68172 18.808 5.68172 35Z" fill="#374151"/>
                    <mask id="path-2-inside-1_47_104" fill="white">
                    <path d="M70 35C70 54.33 54.33 70 35 70C15.67 70 0 54.33 0 35C0 15.67 15.67 0 35 0C54.33 0 70 15.67 70 35ZM5.80061 35C5.80061 51.1264 18.8736 64.1994 35 64.1994C51.1264 64.1994 64.1994 51.1264 64.1994 35C64.1994 18.8736 51.1264 5.80061 35 5.80061C18.8736 5.80061 5.80061 18.8736 5.80061 35Z"/>
                    </mask>
                    <path d="M70 35C70 54.33 54.33 70 35 70C15.67 70 0 54.33 0 35C0 15.67 15.67 0 35 0C54.33 0 70 15.67 70 35ZM5.80061 35C5.80061 51.1264 18.8736 64.1994 35 64.1994C51.1264 64.1994 64.1994 51.1264 64.1994 35C64.1994 18.8736 51.1264 5.80061 35 5.80061C18.8736 5.80061 5.80061 18.8736 5.80061 35Z" stroke="#67E8F9" mask="url(#path-2-inside-1_47_104)"/>
                </svg>
            </div>
        </div>
    </div>
    <div class="down-content border-r border-l border-t border-b relative z-10 border-cyan-300 p-6 m-auto">
        <div class="flex justify-between items-center">
            <div ref="messageBlock" @paste="handlePaste" @input="updateMessage" style="width: 770px;"
            contenteditable="true"
            class="block mr-6 max-h-28 overflow-y-auto p-1 resize-none text-white rounded-lg bg-inherit border border-lime-500 focus:outline-none">
            </div>
            <button @click.prevent="store" v-if="blockBtnSend != true && userLimitedMessages != 0 && blockMessage != true" class="border-lime-500 bg-lime-500 pb-1 text-white text-lg w-32 h-10 rounded-lg hover:bg-inherit hover:text-lime-500 hover:border hover:border-lime-500 transition ease-in">Отправить</button>
        </div>
        <div v-if="errorMessage" class="text-red-500 text-sm absolute">{{ errorMessage[0]  }}</div>
        <div v-if="userLimitedMessages == 0" class="text-red-500 text-sm absolute">Пользователь ограничил возможность отправки сообщений</div>
        <div v-if="blockMessage && userLimitedMessages == 1" class="text-red-500 text-sm absolute">Максимум 30 сообщений за 5 минут, чаты временно заблокированы на 1 час</div>
    </div>

</template>
<style scoped>
.main{
    width: 900px;
    height: 680px;
}
.down-content{
    width: 900px;
}
@media (max-width: 900px){
    .main{
        width: auto;
    }
    .down-content{
        width: auto;
    }
}
</style>

