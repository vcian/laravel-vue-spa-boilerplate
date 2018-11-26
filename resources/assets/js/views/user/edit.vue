<template>
    <div>
        <user-form :id="id"></user-form>
    </div>
</template>

<script>
    import UserForm from './form';
    import helper from '../../services/helper'

    export default {
        components : { UserForm },
        data() {
            return {
                id:this.$route.params.id
            }
        }
    }
</script>