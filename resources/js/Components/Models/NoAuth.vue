<script>
import { Link } from '@inertiajs/vue3';
export default{
    name: "NoAuthComponent",
    components: {
        Link
    },
    props: [
        'open'
    ],
    methods: {
        closeModal(){
            this.$emit('close-modal', true);
        }
    }

}
</script>
<template>
<transition name="modal">
    <div v-if="open" class="modal-overlay" @click.self="closeModal">
        <div class="modal-window">
            <div class="text-white text-xl text-center p-2 border-b border-cyan-300">ПОИСК НЕ ДОСТУПЕН</div>
            <div class="text-white text-lg text-center mt-2">Чтобы найтить игру, необходимо авторизоваться</div>
            <div class="flex justify-between items-center mt-4 px-4">
                <button @click.prevent="closeModal" class="border border-indigo-500 bg-indigo-500 text-white text-lg w-28 h-10 rounded-lg hover:bg-inherit hover:text-indigo-500 hover:border hover:border-indigo-500 transition ease-in">ЗАКРЫТЬ</button>
                <Link :href="route('login')">
                    <button class="border border-lime-500 bg-lime-500 text-white text-lg w-28 h-10 rounded-lg hover:bg-inherit hover:text-lime-500 hover:border hover:border-lime-500 transition ease-in">ОК</button>
                </Link>
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
    width: 442px;
    height: 158px;
    z-index: 1001;
    background: #1e1e1e;
    border-radius: 20px;
    box-shadow: 0 4px 24px 0 rgba(103, 232, 249, 0.5);
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