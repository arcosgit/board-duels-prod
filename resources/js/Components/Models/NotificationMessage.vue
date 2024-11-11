<script>
import { Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3'
export default {
    name: "NotificationMessageComponent",
    props: [
        'openModel',
        'notificationMessageInfo'
    ],
    components: {
        Link
    },
    methods: {
        closeModal() {
            this.$emit('close-modal-message', true);
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
    <transition name="modal">
    <div v-if="openModel" class="modal-overlay" @click.self="closeModal">
        <div class="modal-window">
            <div class="text-white text-xl text-center border-b border-cyan-300 p-2">УВЕДОМЛЕНИЯ ИЗ ЧАТОВ</div>
            <div class="h-96 pr-4 pl-4 pb-4 overflow-y-auto">
                <div v-if="notificationMessageInfo.length">
                    <div class="w-full h-20 rounded-lg bg-custom-black shadow-custom-white mt-6" v-for="notification in notificationMessageInfo" :key="notification">
                        <div class="text-white text-lg pl-4">Сообщение от <Link :href="route('user.show', notification.user_id)" class="text-indigo-500 text-lg">{{ $truncate(notification.userName, 12, '...') }}</Link></div>
                        <div class="flex justify-between items-center px-4 mt-2">
                            <div class="text-lg text-white">Текст: <span class="text-cyan-300 text-base">{{ $truncate(notification.message, 33, '...') }}</span></div>
                            <button @click.prevent="openChat(notification.chat_id, notification.user_id)" class="border border-lime-500 pb-1 bg-lime-500 text-white text-lg w-28 h-8 rounded-lg hover:bg-inherit hover:text-lime-500 hover:border hover:border-lime-500 transition ease-in">в чат</button>
                        </div>
                    </div>
                </div>
                <div v-else class="text-white text-center text-lg mt-4">
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
        height: 6rem;
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