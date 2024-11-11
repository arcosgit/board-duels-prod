<script>
import NavComponent from "@/Layouts/Nav.vue";
import AcceptGameComponent from "@/Components/Models/AcceptGame.vue";
import NoAuthComponent from "@/Components/Models/NoAuth.vue";
import { Link } from '@inertiajs/vue3';
import axios from "axios";
export default{
    name: "StartComponent",
    components: {
        NavComponent,
        Link,
        AcceptGameComponent,
        NoAuthComponent
    },
    props: [
        'userId',
        'searchGame',
        'acceptingGame',
        'isPlaying'
    ],
    data() {
        return {
            runningTime: "5",
            sizeBoard: "3",
            btnFindGame: true,
            noAuth: false,
            findedGame: false
        };
  },
  methods: {
        requestToGame(){
            if (this.userId != null) {
                this.btnFindGame = false;
                axios.post('/requestToGame', {
                    user_id: this.userId,
                    time: this.runningTime,
                    size_board: this.sizeBoard
                }).then(res => {
                    
                }).catch(err => {
                    
                })
            } else{
                this.noAuth = true;
            }
        },
        refuserGame(){
            axios.post('/refuseGame',{
                user_id: this.userId
            }).then(res => {
                this.btnFindGame = true;
            });
        },
  },
  mounted() {
    if (this.searchGame != null) {
        this.btnFindGame = false;
    }
    window.Echo.channel(`accept-game.${this.userId}`).listen('.accept-game', res => {
        if(res.accept_game){
            this.findedGame = true;
        }
    });
  }

}
</script>
<template>
    <NavComponent />
    <AcceptGameComponent v-if="findedGame" :user_id="userId"></AcceptGameComponent>
    <NoAuthComponent :open="noAuth" @close-modal="noAuth = false"></NoAuthComponent>
    <div class="container">
        <div class="flex justify-between mt-96 px-14 main">
            <div class="text-white text-2xl text-wrap break-all">
            Board-Duels - игровая платформа, где вы<br>
            сможете поиграть в крестики нолики,<br>
            с другом или со случайным человеком<br>
            <Link :href="route('about')" class="text-sm text-orange-300 pt-6">
                читать правила проекта
            </Link>
            </div>
            <div class="right-content">
                <div class="flex justify-between items-center selects">
                    <div class="text-white text-xl mr-6">Выберите размер поля: </div>
                    <select v-model="sizeBoard" class="rounded-md w-20 text-center outline-none">
                        <option value="3">3 на 3</option>
                    </select>
                </div>
                <div class="flex justify-between items-center mt-6 selects">
                    <div class="text-white text-xl mr-6">Выберите время хода: </div>
                    <select v-model="runningTime" class="rounded-md w-20 text-center outline-none">
                        <option value="5">5 сек</option>
                        <option value="10">10 сек</option>
                    </select>
                </div>
                <div class="btns">
                    <button v-if="btnFindGame && acceptingGame == null && isPlaying == null" @click.prevent="requestToGame" class="border-lime-500 text-white border w-full h-10 rounded-lg bg-lime-500 mt-10 hover:bg-opacity-0 hover:text-lime-500 hover:border hover:border-lime-500 transition ease-in">БЫСТРЫЙ ПОИСК</button>
                    <div v-if="acceptingGame != null" class="text-red-500 text-lg mt-10">Поиск игры ограничен из-за того,<br> что вы не приняли игру</div>
                    <div v-if="isPlaying != null" class="text-red-500 text-lg mt-10">Вы находитесь в другой игре</div>
                    <button v-if="!btnFindGame" @click.prevent="refuserGame" class="border-red-500 border w-full h-10 rounded-lg bg-red-500 mt-10 hover:bg-opacity-0 hover:text-red-500 hover:border hover:border-red-500 transition ease-in">ОТМЕНА</button>
                
                    <Link :href="route('offline')">
                        <button class="mt-6 w-full h-10 rounded-lg border border-cyan-300 text-cyan-300 hover:bg-cyan-300 hover:text-black transition ease-in">ОФФЛАЙН ИГРА</button>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
@media (max-width: 1023px){
    .mt-96{
        margin-top: 250px;
    }
    .px-14{
        padding-left: 0;
        padding-right: 0;
    }
    .main{
        flex-direction: column;
    }
    .right-content{
        margin-top: 20px;
    }
}
@media (max-width: 650px){
    .text-2xl{
        font-size: 1.25rem;
    }
    .container{
        width: 400px;
    }
    button{
        width: 300px;
    }
    .btns{
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }
    .selects{
        margin: 0 auto;
        margin-top: 1.5rem;
        width: 378px;
    }
    .main{
        display: flex;
        justify-content: center;
        align-items: center;
    }
}
@media (max-width: 401px){
    .text-2xl{
        font-size: 1.05rem;
    }
    .text-xl{
        font-size: 1.05rem;
    }
    .container{
        width: 320px;
    }
    .selects{
        margin: 0 auto;
        margin-top: 1.5rem;
        width: 300px;
    }
}
</style>