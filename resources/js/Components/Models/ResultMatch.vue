<script>
import { Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
export default {
    name: "ResultMatchComponent",
    components: {
        Link
    },
    data() {
        return{
            sendReset: false
        }
    },
    props: [
        'winnerName',
        'gameId',
        'isDrawMatch'
    ],
    methods: {
        resetGame(){
            this.sendReset = true;
            axios.post('/game/reset',{
                game_id: this.gameId
            }).then(res => {
                if(res.data.no_game){
                    router.visit('/');
                }
            }).catch(err => {
                
            });
        },
        restartGame(board, current, winner){
            this.$emit('restart-game', board, current, winner);
        },
        exitGame(url){
            axios.post('/game/end', {
                game_id: this.gameId
            }).then(res =>{
                if(res.data.deleted){
                    router.visit(url);
                }
            })
        }
    },
    mounted(){
        window.Echo.channel(`restart-game.${this.gameId}`).listen('.restart-game', res => {
            this.restartGame(res.board, res.current, res.winner);
        });
    }
}
</script>
<template>
    <transition name="modal">
    <div v-if="winnerName || isDrawMatch" class="modal-overlay">
        <div class="modal-window">
            <div class="text-cyan-300 text-xl text-center p-2">Игра окончена</div>
            <div v-if="winnerName">
                <div class="text-white text-xl border-t border-b border-cyan-300 p-4">Победа игрока: <span class="text-lime-500">{{ $truncate(winnerName, 33, '...') }}</span></div>
            </div>
            <div v-if="isDrawMatch">
                <div class="text-white text-xl border-t border-b border-cyan-300 p-4">Ничья!</div>
            </div>
            <div class="flex justify-between items-center mt-4 px-4">
                <button @click.prevent="exitGame('/')" class="block border w-40 h-10 border-white bg-inherit text-white rounded-lg hover:bg-white hover:text-black transition ease-in">НА ГЛАВНУЮ</button>
                <button v-if="!sendReset" @click.prevent="resetGame" class="btn-one-more block w-40 h-10 border border-lime-500 text-lime-500 bg-inherit rounded-lg hover:bg-lime-500 hover:text-white hover:border hover:border-lime-500 transition ease-in">СЫГРАТЬ ЕЩЁ РАЗ</button>
                <div v-if="sendReset" class="text-lime-500 text-xl text-center">Предложение отправлено</div>
                <button @click.prevent="exitGame('/profile')" class="btn-profile block w-40 h-10 border border-cyan-500 text-cyan-500 bg-inherit rounded-lg hover:bg-cyan-500 hover:text-white hover:border hover:border-cyan-500 transition ease-in">В ПРОФИЛЬ</button>
            </div>
        </div>
    </div>
</transition>
</template>
<style scoped>
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
    width: 571px;
    height: 190px;
    z-index: 1001;
    border-radius: 20px;
    box-shadow: 0 4px 24px 0 rgba(255, 255, 255, 0.25);
    background: #1e1e1e;
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
@media(max-width: 515px){
    .flex{
        flex-direction: column;
    }
    .modal-window{
        height: 278px;
    }
    .btn-one-more{
        margin-top: 10px;
    }
    .btn-profile{
        margin-top: 10px;
    }
}
</style>