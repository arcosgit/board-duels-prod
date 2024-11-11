<script>
export default {
    name: "BlockedRequestComponent",
    props: [
        'message'
    ],
    data() {
        return {
            message: this.message,
        }
    }
}
</script>
<template>
    <transition name="modal">
        <div v-if="message" class="modal-overlay" @click.self="message = false">
            <div class="modal-window">
                <div class="text-red-500 text-xl text-center">{{ message }}</div>
                <button @click.prevent="message = false" class="mt-4 text-center border-orange-300 w-32 h-10 rounded-lg bg-orange-300 hover:bg-inherit hover:border hover:border-orange-300 hover:text-orange-300 transition ease-in">Закрыть</button>
            </div>
        </div>
    </transition>
</template>
<style scoped>
.modal-overlay {
  position: fixed;
  z-index: 9999;
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
  width: 500px;
  height: 165px;
  z-index: 1006;
  background: #1e1e1e;
  border-radius: 20px;
  padding: 20px;
  text-align: center;
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