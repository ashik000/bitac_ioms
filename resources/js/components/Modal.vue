<template>
    <transition name="modal">
        <div class="mask">
            <div class="modal-wrap" :class="{ 'modal-lg' : size == 'lg' }">
                <header class="modal-header">
                    <slot name="header"></slot>
                </header>
                <div class="content">
                    <slot name="content"></slot>
                </div>
                <footer class="footer">
                    <slot name="footer"></slot>
                </footer>
                <button type="button" class="btn-close" aria-label="Close" @click="$emit('close')"></button>
            </div>
        </div>
    </transition>
</template>

<script>
    export default {
        name: "Modal",
        props: ['size']
    }
</script>

<style scoped lang="scss">
    .modal-enter-active, .modal-leave-active {
        transition: opacity .5s
    }
    .modal-enter, .modal-leave-to {
        opacity: 0
    }

    .mask {
        position: fixed;
        top: 0;
        left: 0;

        width: 100vw;
        height: 100vh;

        background: rgba(0, 0, 0, 0.5);

        z-index: 1080;

        display: flex;
        justify-content: center;
        align-items: center;

        .modal-wrap {
            max-width: 80%;
            max-height: 80%;

            min-width: 30%;

            background: #FFFFFF;
            box-shadow: 2px 2px 20px 1px;
            border-radius: 0.25rem;
            overflow-y: auto;

            position: relative;
            display: flex;
            flex-direction: column;

            &.modal-lg {
                width: 600px;
                min-height: 80%;
            }

            .btn-close {
                width: 0.25em;
                height: 0.25em;
                position: absolute;
                right: 1.75rem;
                top: 1rem;
            }

            .header, .content, .footer {
                padding: 0.75rem 1rem;
                margin-top: 1rem;
            }

            .header {
                font-size: 1rem;
            }

            .content {
                padding: 1rem 2rem;
            }

            .footer {
                display: flex;
                flex-direction: row;
                justify-content: flex-end;

                button {
                    background: transparent;
                    border: none;
                }
            }

        }
    }
</style>
