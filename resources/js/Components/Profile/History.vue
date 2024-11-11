<script>
import axios from 'axios';

export default {
    name: "HistoryProfileComponent",
    data() {
        return {
            historyGames: [],
            i: 1,
            loading: true,
            maxHistory: false
        }
    },
    props:[
        'userId'
    ],
    mounted() {
        this.getHistory();
    },
    methods: {
        getHistory() {
            if(!this.maxHistory){
                axios.post(`/getHistory?page=${this.i}`,{
                    user_id: this.userId
                }).then(res =>{
                    this.historyGames = [...this.historyGames, ...res.data.history.data]
                    this.i += 1;
                    this.loading = false;
                    if(res.data.history.data.length <= 0){
                        this.maxHistory = true;
                    }
                })
            }
        },
        handleScroll() {
            const scrollContainer = this.$refs.scrollContainer;

            // Определяем, достиг ли скролл низа
            if (scrollContainer.scrollTop + scrollContainer.clientHeight >= scrollContainer.scrollHeight) {
                this.getHistory(); // Загружаем новые сообщения
            }
        }
    }
}
</script>
<template>
<div ref="scrollContainer" @scroll="handleScroll" class="border-2 border-lime-500 rounded-lg mt-20 mx-56 h-96 overflow-y-auto px-10 pb-4">
    <div v-if="loading" class="flex-custom justify-center mt-36">
        <svg class="animate-spin" width="70" height="70" viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M70 35C70 54.33 54.33 70 35 70C15.67 70 0 54.33 0 35C0 15.67 15.67 0 35 0C54.33 0 70 15.67 70 35ZM5.68172 35C5.68172 51.192 18.808 64.3183 35 64.3183C51.192 64.3183 64.3183 51.192 64.3183 35C64.3183 18.808 51.192 5.68172 35 5.68172C18.808 5.68172 5.68172 18.808 5.68172 35Z" fill="#374151"/>
            <mask id="path-2-inside-1_47_104" fill="white">
            <path d="M70 35C70 54.33 54.33 70 35 70C15.67 70 0 54.33 0 35C0 15.67 15.67 0 35 0C54.33 0 70 15.67 70 35ZM5.80061 35C5.80061 51.1264 18.8736 64.1994 35 64.1994C51.1264 64.1994 64.1994 51.1264 64.1994 35C64.1994 18.8736 51.1264 5.80061 35 5.80061C18.8736 5.80061 5.80061 18.8736 5.80061 35Z"/>
            </mask>
            <path d="M70 35C70 54.33 54.33 70 35 70C15.67 70 0 54.33 0 35C0 15.67 15.67 0 35 0C54.33 0 70 15.67 70 35ZM5.80061 35C5.80061 51.1264 18.8736 64.1994 35 64.1994C51.1264 64.1994 64.1994 51.1264 64.1994 35C64.1994 18.8736 51.1264 5.80061 35 5.80061C18.8736 5.80061 5.80061 18.8736 5.80061 35Z" stroke="#67E8F9" mask="url(#path-2-inside-1_47_104)"/>
        </svg>
    </div>
    <div v-if="historyGames.length">
        <div class="grid grid-cols-2 items-center bg-custom-black shadow-custom-white rounded-lg h-14 mt-4" v-for="game in historyGames" :key="game">
            <div class="text-lg text-white justify-self-center">{{ $truncate(game.user1.name, 12, '...') }} <span class="text-red-500">VS</span> {{ $truncate(game.user2.name, 12, '...') }}</div>
            <div class="text-white text-lg justify-self-center">Победа игрока: <span class="text-lime-500">{{ $truncate(game.result, 12, '...') }}</span></div>
        </div>
    </div>
    <div v-if="!historyGames.length && loading == false">
        <div class="text-white text-center mt-6 text-xl">История пуста</div>
    </div>

</div>
</template>
<style scoped>
.flex-custom{
    display: flex;
}
@media (max-width: 1023px){
    .mx-56 {
        margin-left: 4rem;
        margin-right: 4rem;
    }
}
@media (max-width: 767px){
    .mx-56 {
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
@media (max-width: 400px){
    .px-10{
        padding-left: 1rem;
        padding-right: 1rem;
    }

}
</style>