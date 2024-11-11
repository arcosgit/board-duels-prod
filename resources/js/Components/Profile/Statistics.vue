<script>
export default {
    name: "StatisticsProfileComponent",
    props: [
        'statistics'
    ],
    methods: {
        chanceVictory(){
            if (this.statistics.count_game == 0) return 0;
            return parseInt((this.statistics.count_wins / this.statistics.count_game) * 100)
        }
    }
}
</script>
<template>
<div class="border-2 border-lime-500 rounded-lg mt-20 mx-48">
    <div class="flex justify-between items-center p-4">
        <div>
            <div class="text-white text-lg">Всего игр:</div>
            <div class="text-white text-lg text-center">{{ statistics.count_game }}</div>
        </div>
        <div>
            <div class="text-white text-lg">Число побед:</div>
            <div class="text-white text-lg text-center">{{ statistics.count_wins }}</div>
        </div>
        <div>
            <div class="text-white text-lg">Число поражений:</div>
            <div class="text-white text-lg text-center">{{ statistics.count_loss }}</div>
        </div>
        <div>
            <div class="text-white text-lg">Шанс победы:</div>
            <div class="text-white text-lg text-center">{{ chanceVictory() }}%</div>
        </div>
    </div>
</div>
</template>
<style scoped>
@media (max-width: 1023px){
    .mx-48 {
        margin-left: 4rem;
        margin-right: 4rem;
    }
}
@media (max-width: 767px){
    .mx-48 {
        margin-left: 2rem;
        margin-right: 2rem;
    }
    .text-lg{
        font-size: 1rem;
    }
}
@media (max-width: 640px){
    .flex{
        flex-direction: column;
    }
}
</style>
