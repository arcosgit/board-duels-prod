<script>
import axios from 'axios';
import { router } from '@inertiajs/vue3'

export default {
    name: "PersonalDataProfileComponent",
    props: [
        'user'
    ],
    data(){
        return {
            email: this.user.email,
            name: this.user.name,
            deleteUserModal: false,
            changeEmail: false,
            changePassword: false,
            password: '',
            changeErors:{
                login: null,
                email: null,
                password: null,
            },
            changePasswordToNew: {
                oldPassword: '',
                newPassword: '',
                newPasswordConfirm: '',
                errors: {
                    badOldPassword: null,
                    oldPassword: null,
                    newPassword: null,
                    newPasswordConfirm: null,
                }
            },
            deleteUser:{
                badPassword: null,
                password: '',
            },
            blockChanges:{
                blockName: null
            }     
        }
    },
    methods: {
        update(what) {
            if(what == 'name' && this.user.name != this.name) {
                axios.post(`/profile/update/${what}`,{
                    name: this.name
                }).then(res => {
                    this.blockChanges.blockName = null;
                    this.changeErors.login = null;
                    if(res.data.blockChangeName){
                        this.blockChanges.blockName = res.data.blockChangeName;
                    }
                    router.reload();
                }).catch(err =>{
                    this.changeErors.login = err.response.data.errors.name;

                });
            } else if(what == 'email' && this.user.email != this.email) {
                axios.post(`/profile/update/${what}`,{
                    email: this.email,
                    password: this.password
                }).then(res =>{
                    if(res.data.password){
                        this.changeErors.password = res.data.password;
                    }else{
                        this.changeEmail = null;
                        this.changeErors.password = null;
                        router.reload();
                    }
                }).catch(err =>{
                    this.changeErors.email = err.response.data.errors.email;
                    this.changeErors.password = err.response.data.errors.password;
                });
            } else if(what == 'password') {
                axios.post(`/profile/update/${what}`,{
                    oldPassword: this.changePasswordToNew.oldPassword,
                    newPassword: this.changePasswordToNew.newPassword,
                    newPasswordConfirm: this.changePasswordToNew.newPasswordConfirm
                }).then(res => {
                    if(res.data.badPassword){
                        this.changePasswordToNew.errors.badOldPassword = res.data.badPassword;
                    } else{
                        router.visit('/');
                    }
                }).catch(err =>{
                    this.changePasswordToNew.errors.oldPassword = err.response.data.errors.oldPassword;
                    this.changePasswordToNew.errors.newPassword = err.response.data.errors.newPassword;
                    this.changePasswordToNew.errors.newPasswordConfirm = err.response.data.errors.newPasswordConfirm;
                    console.log(err);
                });
            }
        },
        logout(){
            axios.post('/logout').then(res =>{
                router.visit('/');
            })
        },

        deleteUserFun(){
            axios.post('/profile/delete',{
                password: this.deleteUser.password
            }).then(res =>{
                if(res.data.password){
                    this.deleteUser.badPassword = res.data.password;
                } else{
                    router.visit('/');
                }
            }).catch(err =>{
                this.deleteUser.badPassword = err.response.data.errors.password;
            })
        }
    }
}
</script>
<template>
    <div class="border-2 border-lime-500 rounded-lg mt-20 mx-64 p-6">
        <div class="flex justify-between items-center relative line">
            <div class="text-white text-xl">Логин</div>
            <div class="relative">
                <input v-model="name" class="block w-72 focus:outline-none text-white text-xl p-1 bg-inherit border border-cyan-300 h-10 rounded-lg">
                <div v-if="changeErors.login" class="text-red-500 text-sm absolute">{{ changeErors.login[0] }}</div>
                <div v-if="blockChanges.blockName" class="text-red-500 text-sm absolute">{{ blockChanges.blockName }}</div>
            </div>
            <button @click.prevent="update('name')" class="btn-change border-lime-500 w-24 h-10 pb-1 text-xl text-white bg-lime-500 rounded-lg hover:bg-inherit hover:text-lime-500 hover:border hover:border-lime-500 transition ease-in">сменить</button>
        </div>
        <div class="flex justify-between items-center mt-6 line">
            <div class="text-white text-xl">Почта</div>
            <input v-model="email" class="w-72 focus:outline-none text-white text-xl p-1 bg-inherit border border-cyan-300 h-10 rounded-lg">
            <button @click.prevent="changeEmail = true" class="btn-change border-lime-500 w-24 h-10 text-xl pb-1 text-white bg-lime-500 rounded-lg hover:bg-inherit hover:text-lime-500 hover:border hover:border-lime-500 transition ease-in">сменить</button>
        </div>
        <div class="flex items-center mt-6 line">
            <div class="text-white text-xl">Пароль</div>
            <button @click.prevent="changePassword = true" class="btn-change border-lime-500 ml-14 pb-1 w-24 h-10 text-xl text-white bg-lime-500 rounded-lg hover:bg-inherit hover:text-lime-500 hover:border hover:border-lime-500 transition ease-in">сменить</button>
        </div>
        <div class="flex justify-between items-center mt-6 line">
            <button @click.prevent="deleteUserModal = true" class="border-red-500 text-white text-xl bg-red-500 w-48 h-10 pb-1 rounded-lg hover:bg-inherit hover:text-red-500 hover:border hover:border-red-500 transition ease-in">Удалить аккаунт</button>
            <button @click.prevent="logout" class="btn-change border-indigo-500 bg-indigo-500 text-white text-xl pb-1 w-48 h-10 rounded-lg hover:bg-inherit hover:text-indigo-500 hover:border hover:border-indigo-500 transition ease-in">Выйти из аккаунта</button>
        </div>
    </div>

    <!-- модальные окна -->
    <transition name="modal">
        <div v-if="changeEmail" class="modal-overlay" @click.self="changeEmail = false, changeErors.email = null, password = '', changeErors.password = null">
            <div style="height: 315px" class="modal-window">
                <div class="text-white text-lg">
                    Для смены почты требуется вписать <br>
                    пароль от аккаунта <br>
                    новая почта: 
                </div>
                <div class="text-cyan-300 text-lg mt-1" v-if="user.email != email">{{ email }}</div>
                <div v-else class="text-red-500 text-lg">новая почта не отличается от старой, нельзя сменить</div>
                <div class="realative" v-if="changeErors.email">
                    <div class="text-red-500 text-sm absolute">{{ changeErors.email[0] }}</div>
                </div>
                <input v-model="password" placeholder="Впишите пароль" type="password" class="block w-full focus:outline-none text-white text-xl p-1 pb-2 bg-inherit border border-cyan-300 h-10 rounded-lg mt-6">
                <div class="realative" v-if="changeErors.password">
                    <div class="text-red-500 text-sm absolute"> {{ changeErors.password[0] }}</div>
                </div>
                <div class="flex justify-between items-center mt-6">
                    <button @click.prevent="changeEmail = false, changeErors.email = null, password = '', changeErors.password = null" class="border-indigo-500 bg-indigo-500 text-white text-xl pb-1 w-24 h-10 rounded-lg hover:bg-inherit hover:text-indigo-500 hover:border hover:border-indigo-500 transition ease-in">отмена</button>
                    <button @click.prevent="update('email')" class="w-24 h-10 pb-1 text-xl text-white bg-lime-500 rounded-lg hover:bg-inherit hover:text-lime-500 hover:border hover:border-lime-500 transition ease-in">сменить</button>
                </div>
            </div>
        </div>
    </transition>
    <transition name="modal">
        <div v-if="changePassword" class="modal-overlay" @click.self="changePassword = false, changePasswordToNew.oldPassword = '', changePasswordToNew.newPassword = '', changePasswordToNew.newPasswordConfirm = '', changePasswordToNew.errors = []">
            <div style="height: 295px" class="modal-window">
                <input v-model="changePasswordToNew.oldPassword" placeholder="Впишите старый пароль" type="password" class="block w-full focus:outline-none text-white text-xl p-1 pb-2 bg-inherit border border-cyan-300 h-10 rounded-lg mt-4">
                <div class="text-sm text-red-500 absolute" v-if="changePasswordToNew.errors.oldPassword"> {{ changePasswordToNew.errors.oldPassword[0] }}</div>
                <div class="text-sm text-red-500 absolute" v-if="changePasswordToNew.errors.badOldPassword"> {{ changePasswordToNew.errors.badOldPassword[0] }}</div>
                <input v-model="changePasswordToNew.newPassword" placeholder="Придумайте новый пароль" type="password" class="block w-full focus:outline-none text-white text-xl p-1 pb-2 bg-inherit border border-cyan-300 h-10 rounded-lg mt-6">
                <div class="text-sm text-red-500 absolute" v-if="changePasswordToNew.errors.newPassword"> {{ changePasswordToNew.errors.newPassword[0] }}</div>
                <input v-model="changePasswordToNew.newPasswordConfirm" placeholder="Повторите новый пароль" type="password" class="block w-full focus:outline-none text-white text-xl p-1 pb-2 bg-inherit border border-cyan-300 h-10 rounded-lg mt-6">
                <div class="text-sm text-red-500 absolute" v-if="changePasswordToNew.errors.newPasswordConfirm"> {{ changePasswordToNew.errors.newPasswordConfirm[0] }}</div>
                <div class="flex justify-between items-center mt-6">
                    <button @click.prevent="changePassword = false, changePasswordToNew.oldPassword = '', changePasswordToNew.newPassword = '', changePasswordToNew.newPasswordConfirm = '', changePasswordToNew.errors = []" class="border-indigo-500 bg-indigo-500 text-white text-xl pb-1 w-24 h-10 rounded-lg hover:bg-inherit hover:text-indigo-500 hover:border hover:border-indigo-500 transition ease-in">отмена</button>
                    <button @click.prevent="update('password')" class="w-24 h-10 pb-1 text-xl text-white bg-lime-500 rounded-lg hover:bg-inherit hover:text-lime-500 hover:border hover:border-lime-500 transition ease-in">сменить</button>
                </div>
            </div>
        </div>
    </transition>
    <transition name="modal">
        <div v-if="deleteUserModal" class="modal-overlay" @click.self="deleteUserModal = false, deleteUser.password = '', deleteUser.badPassword = null">
            <div class="modal-window" style="height: 223px">
                <div class="text-white text-lg">
                    Если вы удалите аккаунт, его больше <br>
                    нельзя будет восстановить
                </div>
                <input v-model="deleteUser.password" placeholder="Впишите пароль для удаления" type="password" class="w-full h-10 p-1 pb-2 border border-red-500 focus:outline-none bg-inherit text-white text-xl rounded-lg mt-4">
                <div v-if="deleteUser.badPassword" class="text-sm text-red-500 absolute"> {{ deleteUser.badPassword[0] }}</div>
                <div class="flex justify-between items-center mt-6">
                    <button @click.prevent="deleteUserModal = false, deleteUser.password = '', deleteUser.badPassword = null" class="border border-lime-500 text-lime-500 text-xl pb-1 w-24 h-10 rounded-lg bg-inherit hover:bg-lime-500 hover:text-white transition ease-in">отмена</button>
                    <button @click.prevent="deleteUserFun" class="w-24 h-10 pb-1 text-xl text-white border-red-500 bg-red-500 rounded-lg hover:bg-inherit hover:text-red-500 hover:border hover:border-red-500 transition ease-in">удалить</button>
                </div>
            </div>
        </div>
    </transition>
</template>
<style scoped>
@media (max-width: 1279px){
    .mx-64 {
        margin-left: 11rem;
        margin-right: 14rem;
    }
}
@media (max-width: 1023px){
    .mx-64 {
        margin-left: 5rem;
        margin-right: 4rem;
    }
}
@media (max-width: 767px){
    .w-72 {
        width: 14rem;
    }
    .ml-14 {
    margin-left: 1.5rem;
    }
}
@media (max-width: 640px){
    .w-48 {
        width: 14rem;
    }
    .mx-64 {
        margin-left: 2rem;
        margin-right: 2rem;
    }
    .line{
        flex-direction: column;
    }
    .w-72{
        margin-top: 0.5rem;
    }
    .btn-change{
        margin: 0 auto;
        margin-top: 0.5rem;
    }
}
@media (max-width: 400px){
    .w-72 {
        width: 12rem;
    }
    .w-48 {
        width: 12rem;
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
  width: 400px;
  height: 280px;
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