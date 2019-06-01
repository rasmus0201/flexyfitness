<template>
    <div class="my-2">
        <p class="mb-1 font-weight-bold" v-html="question"></p>

        <template v-if="typeof answer === 'string'">
            <p v-html="answer"></p>
        </template>
        <template v-else-if="typeof answer === 'object'">
            <component :is="answer.wrapper">
                <component v-for="(item, index) in answer.items" :key="index" :is="answer.tag">
                    {{ item }}
                </component>
            </component>
        </template>
    </div>
</template>

<script>
    export default {
        props: ['question', 'answer']
    }
</script>
