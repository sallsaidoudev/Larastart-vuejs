<template>
    <div class="container">
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Users information</h3>

                        <div class="card-tools">
                            <button class="btn btn-success" @click="newModal"><i class="fa fa-user-plus"></i>
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
                                <td><a class="btn btn-dark" href="#" @click="editModal(user)">
                                    <i class="fa fa-edit"></i>
                                </a>
                                    /
                                    <a class="btn btn-danger" href="#" @click="deleteUser(user.id)">
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
                        <h5 v-show="!editmode" class="modal-title" id="exampleModalLabel">Nouvelle utilisateur</h5>
                        <h5 v-show="editmode" class="modal-title" id="exampleModalLabe">Mise à jour info utilisateur</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="editmode ? UpdateUser() : CreateUser()">
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
                            <button v-show="editmode" type="submit" class="btn btn-success">Modifier</button>
                            <button v-show="!editmode" type="submit" class="btn btn-primary">Enregistrer</button>
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
                editmode:false,
                users:{},
                // Create a new form instance
                form: new Form({
                    id:'',
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
            newModal(){
                this.editmode=false;
                this.form.reset();
                $('#add_user').modal('show');
            },
            UpdateUser()
            {
                this.$Progress.start();
                this.form.put('api/user/' + this.form.id).then(()=>{
                    $('#add_user').modal('hide');
                    swal.fire(
                        'Updated!',
                        'Information has been updated.',
                        'success'
                    )
                    this.$Progress.finish();
                    Fire.$emit('EventAfterCreated');
                    })
                    .catch(()=>{
                      this.$Progress.fail();
                });

            },
            editModal(user){
                this.editmode=true;
                this.form.reset();
                $('#add_user').modal('show');
                this.form.fill(user);
            },
            deleteUser(id){
                swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete!'
                }).then((result) => {
                    if (result.value) {
                        // Send request to the server
                        this.form.delete('api/user/' + id).then(() => {

                            swal.fire(
                                'Deleted!',
                                'Your file has been deleted.',
                                'success'
                            )
                            Fire.$emit('EventAfterCreated');
                        }).catch(() => {
                            swal.fire('Failed!',
                                'there was something wrong.',
                                'warning');
                        });
                    }
                })
            },
            LoadUser(){
                this.$Progress.start();
                axios.get("api/user").then(({data})=>(this.users=data.data));
                this.$Progress.finish();
            },
           CreateUser(){
               // Submit the form via a POST request.
               this.form.post('/api/user')
                   .then(()=>{
                       Fire.$emit('EventAfterCreated');

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
