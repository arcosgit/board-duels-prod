<script>
import NavComponent from "@/Layouts/Nav.vue";
export default {
    name: "AboutComponent",
    components: {
        NavComponent
    }
}
</script>
<template>
    <NavComponent />
    
    <div class="container">
        <div class="mt-6">
            <div class="text-white text-xl">Данный сайт разработан одним разработчиком основной стек технологий это Laravel + Vue, про другие технологии писать не буду.</div>
            <div class="text-white text-xl">Связь с разработчиком - <span class="text-lime-500">kolyn050604@yandex.ru</span></div>
            <div class="text-white text-xl">Поддержать развитие сайта и разработчика - <a href="https://www.donationalerts.com/r/1_arcos_1" class="text-indigo-500">https://www.donationalerts.com/r/1_arcos_1</a></div>
            <div class="mt-4">
                <div class="text-white text-2xl text-center uppercase">Правила проекта</div>
                <div class="text-xl text-orange-300">ВАЖНОЕ!!!</div>
                <div class="w-full h-1 bg-orange-300 mt-2"></div>
                <ul class="ml-12">
                    <li class="text-white text-lg mt-2">Во время игры, НЕ покидайте её, даже если вы играете с другом! Вы рискуете получить постоянную блокировку.</li>
                    <li class="text-white text-lg mt-2">Во время подтверждения игры НЕ закрывайте сайт, а также не пытайтесь куда-либо переключится. Вы рискуете получить постоянную блокировку, если вы просто не успеете принять игру ничего страшного не будет.</li>
                    <li class="text-white text-lg mt-2">Во время поиска игры НЕ покидайте сайт, а так же текущую страницу! Вы рискуете получить постоянную блокировку. За отмену никаких последствий нет.</li>
                </ul>
                <div class="w-full h-1 bg-orange-300 mt-2"></div>
                <div class="text-white text-2xl mt-4">Основные правила</div>
                <div class="text-white text-xl mt-4">На сайте запрещены любые виды спама</div>
                <ul class="ml-12">
                    <li class="text-white text-lg mt-2">Запрещено приглашать в друзья слишком часто, если в течении 5 минут вы пригласили больше 20 человек, данный функционал заблокируется на 1 час.</li>
                    <li class="text-white text-lg mt-2">Запрещено спаминить в личные сообщения, максимум 30 сообщений за 5 минут, в случаи нарушения блокировка на ВСЕ чаты 1 час.</li>
                    <li class="text-white text-lg mt-2">Запрещено часто менять логин, почту и настройки, в случаи превышения лимита блокировка на 1 час.</li>
                    <li class="text-white text-lg mt-2">Просьба, если вы же всё таки получили ВРЕМЕННУЮ блокировку не пишите в поддержку, дождитесь её окончания (необаятельно, чтобы сайт был открыт).</li>
                </ul>
                <div class="text-white text-xl mt-4">На игры ограничений нет, вы можете играть сколько угодно, этаже касается и созданий лобби.</div>
                <div class="text-white text-xl mt-2">Как работают лобби?</div>
                <ul class="ml-12">
                    <li class="text-white text-lg mt-2">Приглашать в лобби можно только друзей!</li>
                    <li class="text-white text-lg mt-2">Когда вы пригласите друга(можно сразу несколько и кто первым примет приглашение с тем и будет игра).</li>
                    <li class="text-white text-lg mt-2">После того, как приглашение отправлено, вы можете спокойно закрыть окно и выполнять любую навигацию по сайту, когда ваш друг примет приглашение вас автоматически перенесёт в лобби, где сразу начнётся игра, никаких подтверждений нет!</li>
                    <li class="text-white text-lg mt-2">Так же разрешено использовать "быстрый поиск", даже если вы отправили приглашение, не переживайте, если вы будете в игре и друг примет приглашение, вас не выкинет из текущей игры, НО нужно быть осторожным, если друг примет приглашение во время поиска игры или во время подтверждения, вы рискуете получить блокировку.</li>
                </ul>
                <div class="text-white text-xl mt-4">Общие ограничения</div>
                <ul class="ml-12">
                    <li class="text-white text-lg mt-2">Максимум друзей 50</li>
                </ul>
                <div class="text-white text-xl mt-4">Итоги</div>
                <div class="w-full h-1 bg-lime-500 mt-2"></div>
                <div class="text-white text-lg mt-2">
                    Просьба соблюдать правила, особенно когда вы пользуетесь "быстрым поиском игры". На создание лобби блокировку можно получить только одним способом, это предварительно покинуть игру.
                    Что делать в случаи получения ПОСТОЯННОЙ блокировки? Напишите разработчику на почту, обязательно указав логин и почту, а так же причину получения.
                </div>
                <div class="w-full h-1 bg-lime-500 mt-2 mb-4"></div>
            </div>
        </div>
    </div>
</template>
<style scoped>
li{
    list-style-type: disc;
}
@media (max-width: 790px){
    .text-xl{
        font-size: 1.15rem;
    }
    .text-lg{
        font-size: 1.1rem;
    }
    .container{
        width: 400px;
    }
}
@media (max-width: 417px){
    .text-xl{
        font-size: 1rem;
    }
    .text-lg{
        font-size: 0.8rem;
    }
    .container{
        width: 320px;
    }
}
</style>