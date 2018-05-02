<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Add new Task</h4>
                    <form @submit.prevent="proceed">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input class="form-control" type="text" value="" v-model="userForm.first_name"/>
                                </div>
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input class="form-control" type="text" value="" v-model="userForm.last_name"/>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input class="form-control" type="text" value="" v-model="userForm.email"/>
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input class="form-control" type="password" value="" v-model="userForm.password"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date of Birth</label>
                                    <date-picker v-model="userForm.date_of_birth" :bootstrapStyling="true"></date-picker>
                                </div>
                                <div class="form-group">
                                    <label>Gender</label>
                                    <select class="form-control" v-model="userForm.gender">
                                        <option value="">--- Select Gander ---</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <!--<div class="form-group">-->
                                <!--<label>Avatar</label>-->
                                <!--<input class="form-control" type="file" value="" v-model="userForm.avatar"/>-->
                                <!--</div>-->
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" v-model="userForm.status">
                                        <option value="">--- Select Status ---</option>
                                        <option value="activated">Activated</option>
                                        <option value="pending_activation">Pending Activation</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info waves-effect waves-light m-t-10">
                            <span v-if="id">Update</span>
                            <span v-else>Save</span>
                        </button>
                        <router-link to="/user" class="btn btn-danger waves-effect waves-light m-t-10">Cancel</router-link>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import datePicker from 'vuejs-datepicker'
    import helper from '../../services/helper'
    import router from 'vue-router'

    export default {
        data() {
            return {
                userForm: new Form({
                    'first_name' : '',
                    'last_name' : '',
                    'email' : '',
                    'password' : '',
                    'date_of_birth' : '',
                    'gender' : '',
                    'status' : ''
                })
            }
        },
        components : { datePicker },
        props : ['id'],
        mounted() {
            if (this.id)
                this.getUsers();
        },
        methods : {
            proceed() {
                this.userForm.date_of_birth = moment(this.userForm.date_of_birth).format('YYYY-MM-DD');
                if (this.id)
                    this.updateUser();
                else
                    this.storeUser();
            },
            storeUser() {
                this.userForm.post('api/v1/user')
                    .then(response => {
                        toastr['success'](response.message);
                        this.$router.push('user')
                    })
                    .catch(response => {
                        toastr['error'](response.message);
                    })
            },
            getUsers() {
                axios.get('/api/v1/user/'+this.id)
                    .then(response => {
                        this.userForm.first_name = response.data.first_name;
                        this.userForm.last_name = response.data.last_name;
                        this.userForm.date_of_birth = response.data.date_of_birth;
                        this.userForm.gender = response.data.gender;
                        this.userForm.email = response.data.email;
                        this.userForm.status = response.data.status;
                    })
                    .catch(response => {
                        toastr['error'](response.message);
                    })
            },
            updateUser() {
                this.taskForm.patch('/api/v1/user/'+this.id)
                    .then(response => {
                        if (response.type === 'error')
                            toastr['error'](response.message);
                        else
                            this.$router.push('/user')
                    })
                    .catch(response => {
                        toastr['error'](response.message);
                    })
            }
        }
    }
</script>
