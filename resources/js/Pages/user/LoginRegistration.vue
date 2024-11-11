<script>
import NavComponent from "@/Layouts/Nav.vue";
import BlockedRequestComponent from "@/Components/Models/BlockedRequest.vue";
import axios from "axios";
import { router } from '@inertiajs/vue3'
export default {
    name: "LoginRegistrationComponent",
    components: {
        NavComponent,
        BlockedRequestComponent
    },
    data() {
        return {
            loginModal: true,
            resetPass: false,
            registration: {
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                errors: {
                    name: null,
                    email: null,
                    password: null,
                    password_confirmation: null
                }
            },
            login: {
                email: '',
                password: '',
                errors: {
                    email: null,
                    password: null,
                    badPassword: null
                }
            },
            blockAuthEmail: null,
            resetPasswordEmail: '',
            resetPasswordEmailErrors: {
                text: null,
                good: null
            }
        }
    },
    methods: {
        registrationStore(){
            axios.post('/registration', {
                name: this.registration.name,
                email: this.registration.email,
                password: this.registration.password,
                password_confirmation: this.registration.password_confirmation
            }).then(res =>{
                if (res.data.redirect){
                    router.visit('/profile');
                }
                this.errosFun();
            }).catch(err =>{
                this.errosFun(err.response.data.errors.name, err.response.data.errors.email, err.response.data.errors.password, err.response.data.errors.password_confirmation)
            })
        },

        loginStore(){
            axios.post('/login', {
                email: this.login.email,
                password: this.login.password
            }).then(res =>{
                if (res.data.password){
                    this.login.errors.badPassword = res.data.password
                    this.login.errors.email = null
                    this.login.errors.password = null
                }
                if(res.data.blockAuthEmail){
                    this.blockAuthEmail = res.data.blockAuthEmail
                }
                if(res.data.authenticated){
                    router.visit('/profile');
                }

            }).catch(err =>{
                this.login.errors.badPassword = null
                this.login.errors.email = err.response.data.errors.email
                this.login.errors.password = err.response.data.errors.password
            })
        },

        errosFun(name = null, email = null, password = null, password_confirmation = null){
            this.registration.errors.name = name
            this.registration.errors.email = email
            this.registration.errors.password = password
            this.registration.errors.password_confirmation = password_confirmation
        },

        resetPassword(){
            axios.post('/resetPassword', {
                email: this.resetPasswordEmail
            }).then(res => {
                this.resetPasswordEmailErrors.text = null;
                if(res.data.sent){
                    this.resetPasswordEmailErrors.good = null;
                    this.resetPasswordEmailErrors.text = res.data.sent
                } else{
                    this.resetPasswordEmailErrors.good = 'Письмо отправлено';
                }
            }).catch(err => {
                this.resetPasswordEmailErrors.text = null;
                this.resetPasswordEmailErrors.text = err.response.data.errors.email
            })
        }
    }
}
</script>
<template>
    <NavComponent />
    <BlockedRequestComponent v-if="blockAuthEmail" :message="'Слишком много неудачных попыток входа, попробуйте через час'"></BlockedRequestComponent>
    <div class="log-reg" v-if="!resetPass">
        <div class="grid grid-cols-2 text-center border-b border-cyan-300">
            <div class="text-xl border-r border-cyan-300 p-1 cursor-pointer" @click.prevent="loginModal = true" :class="{'text-lime-500': loginModal, 'text-white hover:text-lime-100': !loginModal}">ВХОД</div>
            <div class="text-xl p-1 cursor-pointer" @click.prevent="loginModal = false" :class="{'text-lime-500': !loginModal, 'text-white hover:text-lime-100': loginModal}">ЗАРЕГИСТРИРОВАТЬСЯ</div>
        </div>
        <div class="mt-24 px-6" v-if="loginModal">
            <div>
                <div class="text-white text-lg">ПОЧТА:</div>
                <input v-model="login.email" type="text" placeholder="Впишите почту" class="text-white w-full h-9 bg-inherit rounded-lg mt-1 border pl-2 border-cyan-300 focus:outline-none">
                <div class="text-sm text-red-500 absolute" v-if="login.errors.email"> {{ login.errors.email[0] }}</div>
            </div>
            <div class="mt-4">
                <div class="text-white text-lg">ПАРОЛЬ:</div>
                <input v-model="login.password" type="password" placeholder="Впишите пароль" class="text-white w-full h-9 bg-inherit rounded-lg mt-1 border border-cyan-300 pl-2 focus:outline-none">
                <div class="text-sm text-red-500 absolute" v-if="login.errors.password"> {{ login.errors.password[0] }}</div>
                <div class="text-sm text-red-500 absolute" v-if="login.errors.badPassword"> {{ login.errors.badPassword }}</div>
            </div>
            <div class="flex justify-between items-center mt-6">
                <button v-if="blockAuthEmail == null" @click.prevent="loginStore" class="w-40 h-10 text-lime-500 bg-none border border-lime-500 rounded-lg hover:bg-lime-500 hover:text-white transition ease-in">ВОЙТИ</button>
                <div @click.prevent="resetPass = true" class="text-sky-800 text-sm underline cursor-pointer">Забыли пароль?</div>
            </div>
        </div>
        <div v-else class="mt-4 px-6">
            <div>
                <div class="text-white text-lg">ПРИДУМАЙТЕ ЛОГИН:</div>
                <input v-model="registration.name" type="text" placeholder="Впишите логин" class="text-white w-full h-9 bg-inherit rounded-lg mt-1 border pl-2 border-cyan-300 focus:outline-none">
                <div class="text-sm text-red-500 absolute" v-if="registration.errors.name"> {{ registration.errors.name[0] }}</div>
            </div>
            <div class="mt-4">
                <div class="text-white text-lg">ПОЧТА:</div>
                <input v-model="registration.email" type="text" placeholder="Впишите почту" class="text-white w-full h-9 bg-inherit rounded-lg mt-1 border pl-2 border-cyan-300 focus:outline-none">
                <div class="text-sm text-red-500 absolute" v-if="registration.errors.email"> {{ registration.errors.email[0] }}</div>
            </div>
            <div class="mt-4">
                <div class="text-white text-lg">ПАРОЛЬ:</div>
                <input v-model="registration.password" type="password" placeholder="Придумайте пароль" class="text-white w-full h-9 bg-inherit rounded-lg mt-1 border pl-2 border-cyan-300 focus:outline-none">
                <div class="text-sm text-red-500 absolute" v-if="registration.errors.password"> {{ registration.errors.password[0] }}</div>
            </div>
            <div class="mt-4">
                <div class="text-white text-lg">ПОВТОРИТЕ ПАРОЛЬ:</div>
                <input v-model="registration.password_confirmation" type="password" placeholder="Повторите пароль" class="text-white w-full h-9 bg-inherit rounded-lg mt-1 border pl-2 border-cyan-300 focus:outline-none">
                <div class="text-sm text-red-500 absolute" v-if="registration.errors.password_confirmation"> {{ registration.errors.password_confirmation[0] }}</div>
            </div>
            <button @click.prevent="registrationStore" class="w-48 h-10 mt-6 text-lime-500 bg-none border border-lime-500 rounded-lg hover:bg-lime-500 hover:text-white transition ease-in">ЗАРЕГИСТРИРОВАТЬСЯ</button>
        </div>
    </div>
    <div class="reset-pass" v-if="resetPass">
            <div class="text-white text-xl">
                Для сброса пароля впишите почту, которая<br>
                привязана к вашему аккаунту
            </div>
            <input v-model="resetPasswordEmail" type="text" placeholder="Впишите почту" class="text-white w-full h-9 bg-inherit rounded-lg mt-4 border pl-2 border-cyan-300 focus:outline-none">
            <p v-if="resetPasswordEmailErrors.text" class="text-sm text-red-500 absolute">{{ resetPasswordEmailErrors.text[0] }}</p>
            <p v-if="resetPasswordEmailErrors.good" class="text-sm text-lime-500 absolute">{{ resetPasswordEmailErrors.good }}</p>
            <div class="flex justify-between items-center mt-6 btns">
                <button @click.prevent="resetPass = false" class="border-sky-800 w-32 h-9 bg-sky-800 text-white rounded-lg hover:bg-inherit hover:text-sky-800 hover:border hover:border-sky-800 transition ease-in">ОТМЕНА</button>
                <button @click.prevent="resetPassword" class="border-lime-500 btn text-white w-32 h-9 bg-lime-500 rounded-lg hover:bg-opacity-0 hover:text-lime-500 hover:border hover:border-lime-500 transition ease-in">ОТПРАВИТЬ</button>
            </div>
    </div>
</template>
<style scoped>
.log-reg{
    width: 500px;
    height: 450px;
    border-radius: 20px;
    border: 1px solid #67e8f9;
    margin: 0 auto;
    margin-top: 225px;
}
.reset-pass{
    width: 500px;
    height: 200px;
    border-radius: 20px;
    border: 1px solid #67e8f9;
    margin: 0 auto;
    margin-top: 225px;
    padding: 20px;
}
@media (max-width: 510px){
    .log-reg{
        width: auto;
    }
    .reset-pass{
        width: auto;
    }
    .text-xl{
        font-size: 1rem;
    }
    .text-lg{
        font-size: 1rem;
    }
}
@media (max-width: 375px){
    .log-reg{
        width: auto;
    }
    .text-xl{
        font-size: 0.9rem;
    }
    .text-lg{
        font-size: 0.9rem;
    }
    .btns{
        flex-direction: column;
    }
    .reset-pass{
        height: 245px;
    }
    .btn{
        margin-top: 10px;
    }
}
</style>