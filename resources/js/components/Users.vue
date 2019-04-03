<template>
    <div class="container">
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Users information</h3>

                        <div class="card-tools">
                            <button class="btn btn-success" data-toggle="modal" data-target="#add_user"><i class="fa fa-user-plus"></i>
                                Ajouter</button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <tbody><tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Registered at</th>
                                <th>Operations</th>
                            </tr>
                            <tr v-for="user in users" :key="user.id">
                                <td>{{user.id}}</td>
                                <td>{{user.name}}</td>
                                <td>{{user.email}}</td>
                                <td><span class="tag tag-success">{{user.type|upText}}</span></td>
                                <td><span class="tag tag-success">{{user.created_at|myDate}}</span></td>
                                <td><a class="btn btn-dark" href="#">
                                    <i class="fa fa-edit"></i>
                                </a>
                                    /
                                    <a class="btn btn-danger" href="#">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            </tbody></table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
        <div class="modal fade" id="add_user" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Nouvelle utilisateur</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="CreateUser">
                        <div class="modal-body">

                            <div class="form-group">
                                <label>Name</label>
                                <input v-model="form.name" type="text" name="name"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                <has-error :form="form" field="name"></has-error>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input v-model="form.email" type="email" name="email"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                                <has-error :form="form" field="email"></has-error>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input v-model="form.password" type="password" name="password"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                                <has-error :form="form" field="password"></has-error>
                            </div>
                            <div class="form-group">
                                <label>Bio</label>
                                <textarea v-model="form.bio" name="bio" id="bio" placeholder="Short bio for user (Optional)"
                                          class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }">
                                </textarea>
                                <has-error :form="form" field="bio"></has-error>
                            </div>
                            <div class="form-group">
                                <label>Type</label>
                                <select v-model="form.type" id="type" name="type"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                                    <option value="">Select User Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">Standard User</option>
                                    <option value="author">Author</option>
                                </select>
                                <has-error :form="form" field="type"></has-error>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        data(){
            return {
                users:{},
                // Create a new form instance
                form: new Form({
                    name: '',
                    email: '',
                    password: '',
                    bio: '',
                    type: '',
                    photo: ''
                })
            }
        },
        methods:{
            LoadUser(){
                this.$Progress.start();
                axios.get("api/user").then(({data})=>(this.users=data.data));
                this.$Progress.finish();
            },
           CreateUser(){
               // Submit the form via a POST request.
               this.form.post('/api/user')
                   .then(()=>{
                       Fire.$emit('EventAfterCreated')

                       $('#add_user').modal('hide');

                       toast.fire({
                           type: 'success',
                           title: 'User Created successfully'
                       })
                   })
                   .catch(()=>{

                   })
           }
        },
        created() {
           this.LoadUser();
           Fire.$on('EventAfterCreated',()=>{
               this.LoadUser();
           });
        }
    }
</script>
