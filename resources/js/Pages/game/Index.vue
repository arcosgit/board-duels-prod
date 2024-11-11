<script>
import NavComponent from "@/Layouts/Nav.vue";
import ResultMatchComponent from "@/Components/Models/ResultMatch.vue";
import { Link } from '@inertiajs/vue3';
import axios from "axios";
import { onBeforeUnmount } from "vue";
export default {
    name: "IndexComponent",
    components: {
        NavComponent,
        Link,
        ResultMatchComponent
    },
    props:[
        'game',
        'winnerUserName'
    ],
    data() {
    return {
      board: this.game.board, // Игровое поле 3x3
      localBoard: Array(9).fill(null),
      currentPlayer: this.game.currnet, // Текущий игрок
      currentPlayerName: this.game.user1.name, // Текущий игрок
      user1_current: this.game.user1_current, // x
      user2_current: this.game.user2_current, // o
      winner: null, // Победитель игры
      isDraw: false, // Флаг для ничьи
      timer: null,
      countdown: Number(this.game.time)
    }
  },
  setup(props) {
      const deleteRecord = () => {
        axios.post('/game/left', { game_id: props.game.id })
          .then(response => {
            
          })
          .catch(error => {
            
          });
      };

      // Удаляем запись при размонтировании компонента
      onBeforeUnmount(deleteRecord);
    
},
  mounted() {
    if(this.winnerUserName != null){
        this.winner = this.winnerUserName;
        clearInterval(this.timer);
        this.countdown = 0;
    }
    this.startCountdown();
    window.Echo.channel(`game.${this.game.id}`).listen('.game', res => {
          this.board = res.board;
          this.currentPlayer = res.current;
          this.localBoard = res.board;
          this.currentPlayerName = this.currentPlayerName === this.game.user1.name ? this.game.user2.name : this.game.user1.name;
          this.countdown = this.game.time
          if(res.winner != null){
            this.winner = res.winner;
            clearInterval(this.timer);
            this.countdown = 0;
          }
          if(res.isDraw != false){
            this.isDraw = true;
            clearInterval(this.timer);
            this.countdown = 0;
          }
        });
    window.Echo.channel(`game-left.${this.game.id}`).listen('.game-left' , res =>{
      if(res.winner != null){
        this.winner = res.winner;
        clearInterval(this.timer);
        this.countdown = 0;
      }
    });
      
},
methods: {
    startCountdown() {
      this.timer = setInterval(() => {
      if (this.countdown > 0) {
        this.countdown--;
      } else {
          this.timeout();
        }
      }, 1200);
    },
    timeout(){
          clearInterval(this.timer);
          axios.post('/game/timeout',{
            game_id: this.game.id,
            winner: this.currentPlayer == 'x' ? 'o' : 'x'
          }).then(res =>{
             this.winner = res.data.winner
          }).catch(err =>{
            
          });
      },
      makeMove(index) {
      // Если ячейка пустая и еще нет победителя
      if (!this.board[index] && !this.winner) {
        this.localBoard.splice(index, 1, this.currentPlayer) // Сделать ход текущего игрока
        this.move();
      }
    },
    move(){
          axios.post('/game/move',{
            game_id: this.game.id,
            board: this.localBoard,
            currnet: this.currentPlayer
          }).then(res =>{
            if(res.data.error){
                this.localBoard = res.data.error
                this.board = res.data.error
            }
          })
        },
    resetGameNow(board, current, winner) {
      this.board = board;
      this.localBoard = board;
      this.currentPlayer = current;
      this.winner = winner;
      this.isDraw = false;
      this.currentPlayerName = this.currentPlayerName === this.game.user1.name ? this.game.user2.name : this.game.user1.name;
      this.startCountdown();
    },
  },
}
</script>
<template>
    <NavComponent />
    <ResultMatchComponent v-if="winner || isDraw" :winnerName="winner" :gameId="game.id" :isDrawMatch="isDraw" @restart-game="resetGameNow"></ResultMatchComponent>
    <div class="flex flex-col justify-center items-center mt-52">
        <div class="w-80 h-14 rounded-lg bg-custom-black text-white grid grid-cols-3 items-center shadow-custom-blue">
            <a :href="route('user.show', game.user1.id)" target="_blank">
                <div class="text-white text-lg justify-self-center">{{ $truncate(game.user1.name, 10, '...') }}</div>
            </a>
            <div class="text-red-500 text-2xl justify-self-center">VS</div>
            <a :href="route('user.show', game.user2.id)" target="_blank">
                <div class="text-white text-lg justify-self-center">{{ $truncate(game.user2.name, 10, '...') }}</div>
            </a>
        </div>
        <div class="mt-6">
            <div class="board">
                <div class="cell" v-for="(cell, index) in board" :key="index" @click.prevent="makeMove(index)" :class="{'text-cyan-300': cell === 'x', 'text-lime-500': cell === 'o',}">
                    {{ cell }}
                </div>
            </div>
        </div>
        <div class="w-80 h-14 rounded-lg bg-custom-black text-white flex flex-col shadow-custom-blue mt-6">
            <div class="text-white text-lg ml-4">Ход игрока: <span class="text-lime-500">{{ $truncate(currentPlayerName, 10, '...') }}</span></div>
            <div class="text-white text-lg ml-4">время: {{ countdown }} сек</div>
        </div>
    </div>
</template>
<style scoped>
.board {
  display: grid;
  grid-template-columns: repeat(3, 100px);
  grid-template-rows: repeat(3, 100px);
}
.cell:nth-child(n) {
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: none;
  border-bottom: white 2px solid;
  text-transform: uppercase;
  font-size: 32px;
  cursor: pointer;
}
.cell:nth-child(1n + 7) {
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: none;
  border-bottom: none;
  text-transform: uppercase;
  font-size: 32px;
  cursor: pointer;
}
.cell:nth-child(3n + 2) {
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: none;
  border-right: white 2px solid;
  border-left: white 2px solid;
  text-transform: uppercase;
  font-size: 32px;
  cursor: pointer;
}
button {
  padding: 10px 20px;
  font-size: 16px;
  cursor: pointer;
}
</style>