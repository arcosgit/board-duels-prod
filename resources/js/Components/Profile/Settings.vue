<script>
import axios from 'axios';
import { router } from '@inertiajs/vue3'
import BlockedRequestComponent from "@/Components/Models/BlockedRequest.vue";

export default {
    name: "SettingsProfileComponent",
    components: {
      BlockedRequestComponent
    },
    props:[
        'settings',
        'blockedRequestSettings',
    ],
    data() {
    return {
        localSettings: {
            statistics: this.settings.statistics,
            freind_add_can: this.settings.freind_add_can,
            lobby_can: this.settings.lobby_can,
            message_can: this.settings.message_can,
        },
        changedData: false,
        blockedRequestSettingsLocal: this.blockedRequestSettings
    }
  },
  methods: {
     changeCheckbox() {
        this.localSettings.statistics = !this.localSettings.statistics
     },
     changeSettings() {
         axios.post('/profile/settings',{
             statistics: this.localSettings.statistics,
             freind_add_can: this.localSettings.freind_add_can, 
             lobby_can: this.localSettings.lobby_can,
             message_can: this.localSettings.message_can
         }).then(res =>{
            if(res.data.changed){
                this.changedData = true
            } else{
                this.blockedRequestSettingsLocal = true
            }
         });
     },
     reload(){
        router.reload();
     }
  }
}
</script>
<template>
  <BlockedRequestComponent v-if="blockedRequestSettingsLocal" :message="'Слишком частое смена настроек, данный функционал заблокирован на 1 час'"></BlockedRequestComponent>
    <transition name="modal">
        <div v-if="changedData" class="modal-overlay" @click.self="changedData = false, reload()">
            <div class="modal-window">
                <div class="text-white text-xl text-center">Данные успешно изменены</div>
                <div class="text-center">
                    <button @click="changedData = false, reload()" class="w-32 h-10 rounded-lg bg-lime-500 mt-6 hover:bg-inherit hover:text-lime-500 hover:border hover:border-lime-500 transition ease-in">Закрыть</button>
                </div>
            </div>
        </div>
    </transition>
    <div class="mt-20">
      <div class="flex justify-between items-center p-4 border-2 border-lime-500 rounded-lg mx-60">
        <div class="text-white text-lg">Скрыть статистику</div>
        <label class="relative inline-flex items-center cursor-pointer" @click.prevent="changeCheckbox">
          <input 
            type="checkbox" 
            class="sr-only peer"
            :checked="localSettings.statistics"
          />
          <div class="group peer bg-white rounded-full duration-300 w-20 h-6 ring-2 ring-red-500 after:duration-300 after:bg-red-500 peer-checked:after:bg-green-500 peer-checked:ring-green-500 after:rounded-full after:absolute after:h-5 after:w-5 after:top-0.5 after:left-1 after:flex after:justify-center after:items-center peer-checked:after:translate-x-12 peer-hover:after:scale-95"></div>
        </label>
      </div>

      <div class="flex justify-between items-center p-4 border-2 border-lime-500 rounded-lg mx-60 mt-6">
        <div class="text-white text-lg">Кто может добавлять меня в друзья</div>
        <select v-model="localSettings.freind_add_can" class="outline-none w-20 rounded-md cursor-pointer">
          <option value="1">Все</option>
          <option value="0">Никто</option>
        </select>
      </div>

      <div class="flex justify-between items-center p-4 border-2 border-lime-500 rounded-lg mx-60 mt-6">
        <div class="text-white text-lg">Кто может приглашать меня в лобби</div>
        <select v-model="localSettings.lobby_can" class="outline-none w-20 rounded-md cursor-pointer">
          <option value="1">Друзья</option>
          <option value="0">Никто</option>
        </select>
      </div>

      <div class="flex justify-between items-center p-4 border-2 border-lime-500 rounded-lg mx-60 mt-6">
        <div class="text-white text-lg">Кто может отправлять мне сообщения</div>
        <select v-model="localSettings.message_can" class="outline-none w-20 rounded-md cursor-pointer">
          <option value="1">Друзья</option>
          <option value="0">Никто</option>
        </select>
      </div>

      <button v-if="blockedRequestSettingsLocal != true" @click.prevent="changeSettings" class="btn-accept border-lime-500 w-32 h-10 ml-60 rounded-lg bg-lime-500 mt-6 hover:bg-opacity-0 hover:text-lime-500 hover:border hover:border-lime-500 transition ease-in" >
        ПРИМЕНИТЬ
      </button>
    </div>
  </template>

<style scoped>
@media (max-width: 1023px){
  .btn-accept{
    display: block;
    margin: 0 auto;
    margin-top: 1.5rem;
  }
    .mx-60 {
        margin-left: 4rem;
        margin-right: 4rem;
    }
}
@media (max-width: 767px){
    .mx-60 {
        margin-left: 2rem;
        margin-right: 2rem;
    }
    .text-lg{
        font-size: 1rem;
    }
}
@media (max-width: 640px){
  .text-lg{
        font-size: 0.8rem;
    }
}
@media (max-width: 400px){
  .mt-20{
        margin-top: 2rem;
    }
    .px-10{
        padding-left: 1rem;
        padding-right: 1rem;
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
  width: 300px;
  height: 145px;
  z-index: 1001;
  background: #1e1e1e;
  border-radius: 20px;
  box-shadow: 0 4px 24px 0 rgba(103, 232, 249, 0.5);
  padding: 20px;
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