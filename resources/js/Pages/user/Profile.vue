<script>
import NavComponent from "@/Layouts/Nav.vue";
import StatisticsProfileComponent from "@/Components/Profile/Statistics.vue";
import HistoryProfileComponent from "@/Components/Profile/History.vue";
import SettingsProfileComponent from "@/Components/Profile/Settings.vue";
import PersonalDataProfileComponent from "@/Components/Profile/PersonalData.vue";
export default {
    name: "ProfileComponent",
    components: {
        NavComponent,
        StatisticsProfileComponent,
        HistoryProfileComponent,
        SettingsProfileComponent,
        PersonalDataProfileComponent
    },
    props: [
        'user',
        'blockedRequestSettings'
    ],
    data(){
        return {
            choice: {
                statistics: true,
                history: false,
                settings: false,
                personal: false
            }
        }
    },
    methods:{
        choiceItem(item){
            for(let elem in this.choice){
                if(elem == item){
                    this.choice[elem] = true
                }else{
                    this.choice[elem] = false
                }
            }
        }
    },
}
</script>
<template>
<div class="h-full flex flex-col">
    <div style="height: 64px">
    <NavComponent />
    </div>
    <div class="container border-r border-l border-cyan-300 h-full">
        <div class="flex">
            <div class="mt-6 ml-6">
                <div class="text-white text-xl"> {{ $truncate(user.name, 40, '...') }}</div>
                <div class="text-white text-lg mt-4 date">На сайте с {{ user.created }}</div>
            </div>
        </div>
        <div class="mt-6 w-full border-b border-t border-cyan-300">
            <div class="flex justify-between items-center tabs">
                <div @click.prevent="choiceItem('statistics')" class="text-xl tab p-2 border-r border-cyan-300 text-center w-full cursor-pointer" :class="{'text-lime-500': choice.statistics, 'text-white hover:text-lime-100': !choice.statistics}">СТАТИСТИКА</div>
                <div @click.prevent="choiceItem('history')" class="text-xl tab p-2 border-r border-cyan-300 w-full text-center cursor-pointer" :class="{'text-lime-500': choice.history, 'text-white hover:text-lime-100': !choice.history}">ИСТОРИЯ</div>
                <div @click.prevent="choiceItem('settings')" class="text-xl tab p-2 border-r border-cyan-300 w-full text-center cursor-pointer" :class="{'text-lime-500': choice.settings, 'text-white hover:text-lime-100': !choice.settings}">НАСТРОЙКИ</div>
                <div @click.prevent="choiceItem('personal')" class="text-xl p-2 w-full text-center cursor-pointer" :class="{'text-lime-500': choice.personal, 'text-white hover:text-lime-100': !choice.personal}">ЛИЧНЫЕ ДАННЫЕ</div>
            </div>
        </div>
        <div v-if="choice.statistics">
            <StatisticsProfileComponent :statistics="user.statistics"></StatisticsProfileComponent>
        </div>
        <div v-if="choice.history">
            <HistoryProfileComponent :userId="user.id"></HistoryProfileComponent>
        </div>
        <div v-if="choice.settings">
            <SettingsProfileComponent :settings="user.settings" :blockedRequestSettings="blockedRequestSettings"></SettingsProfileComponent>
        </div>
        <div v-if="choice.personal">
            <PersonalDataProfileComponent :user="user"></PersonalDataProfileComponent>
        </div>
    </div>
</div>
</template>
<style scoped>
@media (max-width: 767px){
    .h-full{
        min-height: 100vh;
        height: auto;
    }
    .text-xl{
        font-size: 1rem;
    }
    .date{
        font-size: 16px;
    }
}
@media (max-width: 640px){
    .text-xl{
        font-size: 1rem;
    }
    .date{
        font-size: 16px;
    }
    .tabs{
        flex-direction: column;
    }
    .tab{
        border-right: none;
        border-bottom: 1px solid #67e8f9;
    }
    .container{
        width: 400px;
    }
}
@media (max-width: 400px){
    .container{
        width: 320px;
    }

}
</style>