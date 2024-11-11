<script>
import NavComponent from "@/Layouts/Nav.vue";
export default{
    name: "OfflineComponent",
    components: {
        NavComponent
    },
    data() {
    return {
      board: Array(9).fill(null), // Игровое поле 3x3
      currentPlayer: 'X', // Текущий игрок
      winner: null, // Победитель игры
      isDraw: false // Флаг для ничьи
    }
  },
  methods: {
    makeMove(index) {
      // Если ячейка пустая и еще нет победителя
      if (!this.board[index] && !this.winner) {
        this.board.splice(index, 1, this.currentPlayer) // Сделать ход текущего игрока

        if (this.checkWinner()) {
          this.winner = this.currentPlayer // Если есть победитель
        } else if (this.board.every((cell) => cell !== null)) {
          this.isDraw = true // Если все ячейки заполнены и нет победителя — ничья
        } else {
          // Переключаем игрока
          this.currentPlayer = this.currentPlayer === 'X' ? 'O' : 'X'
        }
      }
    },
    checkWinner() {
      const winningCombinations = [
        [0, 1, 2],
        [3, 4, 5],
        [6, 7, 8],
        [0, 3, 6],
        [1, 4, 7],
        [2, 5, 8],
        [0, 4, 8],
        [2, 4, 6]
      ]
      return winningCombinations.some((combination) => {
        const [a, b, c] = combination
        return this.board[a] && this.board[a] === this.board[b] && this.board[a] === this.board[c]
      })
    },
    resetGame() {
      // Сброс игры
      this.board = Array(9).fill(null)
      this.currentPlayer = 'X'
      this.winner = null
      this.isDraw = false
    },
  }
}
</script>
<template>
    <NavComponent />
    <div class="flex flex-col justify-center items-center mt-52">
        <div class="w-80 h-14 rounded-lg bg-custom-black text-white flex justify-center items-center shadow-custom-blue">
            оффлайн игра
        </div>
        <div class="mt-6">
            <div class="board">
                <div class="cell" v-for="(cell, index) in board" :key="index" @click="makeMove(index)" :class="{'text-cyan-300': cell === 'X', 'text-lime-500': cell === 'O',}">
                    {{ cell }}
                </div>
            </div>
        </div>
        <div class="w-80 h-14 rounded-lg bg-custom-black text-white flex justify-center items-center shadow-custom-blue mt-6">
            оффлайн игра
        </div>
        <div class="w-80 mt-6" v-if="winner || isDraw">
            <div v-if="winner" class="text-white text-xl">Победил <span :class="{'text-cyan-300': winner === 'X', 'text-lime-500': winner === 'O'}">{{ winner }}</span></div>
            <span v-if="isDraw && !winner" class="text-white text-xl">Ничья!</span>
        </div>
        <button v-if="winner || isDraw" @click="resetGame" class="w-80 h-12 text-center rounded-lg bg-lime-500 mt-10 hover:bg-opacity-0 hover:text-lime-500 hover:border hover:border-lime-500 transition ease-in">ИГРАТЬ ЗАНОВО</button>
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
  font-size: 32px;
  cursor: pointer;
}
.cell:nth-child(1n + 7) {
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: none;
  border-bottom: none;
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
  font-size: 32px;
  cursor: pointer;
}
button {
  padding: 10px 20px;
  font-size: 16px;
  cursor: pointer;
}
</style>