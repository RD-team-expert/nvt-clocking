<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast, Toaster } from 'vue-sonner';

import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type User, type Role, type Permission } from '@/types';

interface Props {
    users: User[];
    roles: Role[];
    permissions: Permission[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User Management',
        href: '/settings/user-management',
    },
];

// Forms for users
const createUserForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role_id: '',
    roles: [] as string[],
});

const editUserForm = useForm({
    id: '',
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role_id: '',
    roles: [] as string[],
});

// Forms for roles
const createRoleForm = useForm({
    name: '',
    permissions: [] as string[],
});

const editRoleForm = useForm({
    id: '',
    name: '',
    permissions: [] as string[],
});

// Forms for permissions
const createPermissionForm = useForm({
    name: '',
});

const editPermissionForm = useForm({
    id: '',
    name: '',
});

// Dialog states
const showCreateDialog = ref(false);
const showEditDialog = ref(false);
const showDeleteDialog = ref(false);
const showCreateRoleDialog = ref(false);
const showEditRoleDialog = ref(false);
const showDeleteRoleDialog = ref(false);
const showCreatePermissionDialog = ref(false);
const showEditPermissionDialog = ref(false);
const showDeletePermissionDialog = ref(false);

const selectedUser = ref<User | null>(null);
const selectedRole = ref<Role | null>(null);
const selectedPermission = ref<Permission | null>(null);

// Validation errors
const createErrors = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role_id: '',
});

const editErrors = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role_id: '',
});

const roleErrors = ref({
    name: '',
    permissions: '',
});

const permissionErrors = ref({
    name: '',
});

// Validation functions
const validateEmail = (email: string): boolean => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
};

const validateCreateForm = (): boolean => {
    let isValid = true;
    createErrors.value = {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role_id: '',
    };

    if (!createUserForm.name.trim()) {
        createErrors.value.name = 'Name is required';
        isValid = false;
    } else if (createUserForm.name.length < 2) {
        createErrors.value.name = 'Name must be at least 2 characters';
        isValid = false;
    }

    if (!createUserForm.email.trim()) {
        createErrors.value.email = 'Email is required';
        isValid = false;
    } else if (!validateEmail(createUserForm.email)) {
        createErrors.value.email = 'Please enter a valid email address';
        isValid = false;
    }

    if (!createUserForm.password) {
        createErrors.value.password = 'Password is required';
        isValid = false;
    } else if (createUserForm.password.length < 8) {
        createErrors.value.password = 'Password must be at least 8 characters';
        isValid = false;
    }

    if (createUserForm.password !== createUserForm.password_confirmation) {
        createErrors.value.password_confirmation = 'Passwords do not match';
        isValid = false;
    }

    if (!createUserForm.role_id) {
        createErrors.value.role_id = 'Role is required';
        isValid = false;
    }

    return isValid;
};

const validateEditForm = (): boolean => {
    let isValid = true;
    editErrors.value = {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role_id: '',
    };

    if (!editUserForm.name.trim()) {
        editErrors.value.name = 'Name is required';
        isValid = false;
    } else if (editUserForm.name.length < 2) {
        editErrors.value.name = 'Name must be at least 2 characters';
        isValid = false;
    }

    if (!editUserForm.email.trim()) {
        editErrors.value.email = 'Email is required';
        isValid = false;
    } else if (!validateEmail(editUserForm.email)) {
        editErrors.value.email = 'Please enter a valid email address';
        isValid = false;
    }

    if (editUserForm.password) {
        if (editUserForm.password.length < 8) {
            editErrors.value.password = 'Password must be at least 8 characters';
            isValid = false;
        }

        if (editUserForm.password !== editUserForm.password_confirmation) {
            editErrors.value.password_confirmation = 'Passwords do not match';
            isValid = false;
        }
    }

    if (!editUserForm.role_id) {
        editErrors.value.role_id = 'Role is required';
        isValid = false;
    }

    return isValid;
};

const validateRoleForm = (form: typeof createRoleForm | typeof editRoleForm): boolean => {
    let isValid = true;
    roleErrors.value = {
        name: '',
        permissions: '',
    };

    if (!form.name.trim()) {
        roleErrors.value.name = 'Name is required';
        isValid = false;
    } else if (form.name.length < 2) {
        roleErrors.value.name = 'Name must be at least 2 characters';
        isValid = false;
    }

    return isValid;
};

const validatePermissionForm = (form: typeof createPermissionForm | typeof editPermissionForm): boolean => {
    let isValid = true;
    permissionErrors.value = {
        name: '',
    };

    if (!form.name.trim()) {
        permissionErrors.value.name = 'Name is required';
        isValid = false;
    } else if (form.name.length < 2) {
        permissionErrors.value.name = 'Name must be at least 2 characters';
        isValid = false;
    }

    return isValid;
};

// User operations
const submitCreateForm = () => {
    if (!validateCreateForm()) return;

    // Find the role name from the ID
    const role = props.roles.find(r => r.id === Number(createUserForm.role_id));
    const roleName = role ? role.name : '';

    // Prepare the data to send
    const formData = {
        name: createUserForm.name,
        email: createUserForm.email,
        password: createUserForm.password,
        password_confirmation: createUserForm.password_confirmation,
        role_id: createUserForm.role_id,
        roles: roleName ? [roleName] : []
    };

    createUserForm.post(route('users.store'), {
        data: formData,
        onSuccess: () => {
            showCreateDialog.value = false;
            createUserForm.reset();
            toast.success('User created successfully');
        },
    });
};

const editUser = (user: User) => {
    selectedUser.value = user;
    editUserForm.id = String(user.id);
    editUserForm.name = user.name;
    editUserForm.email = user.email;
    editUserForm.password = '';
    editUserForm.password_confirmation = '';
    editUserForm.role_id = user.role_id ? String(user.role_id) : '';
    editUserForm.roles = user.roles?.map(r => r.name) || [];
    showEditDialog.value = true;
};

const submitEditForm = () => {
    if (!validateEditForm()) return;

    // Find the role name from the ID
    const role = props.roles.find(r => r.id === Number(editUserForm.role_id));
    const roleName = role ? role.name : '';

    // Prepare the data to send
    const formData = {
        name: editUserForm.name,
        email: editUserForm.email,
        password: editUserForm.password,
        password_confirmation: editUserForm.password_confirmation,
        role_id: editUserForm.role_id,
        roles: roleName ? [roleName] : []
    };

    editUserForm.put(route('users.update', { id: editUserForm.id }), {
        data: formData,
        onSuccess: () => {
            showEditDialog.value = false;
            toast.success('User updated successfully');
        },
    });
};

const confirmDeleteUser = (user: User) => {
    selectedUser.value = user;
    showDeleteDialog.value = true;
};

const deleteUser = () => {
    if (selectedUser.value) {
        useForm({}).delete(route('users.destroy', { id: selectedUser.value.id }), {
            onSuccess: () => {
                showDeleteDialog.value = false;
                selectedUser.value = null;
                toast.success('User deleted successfully');
            },
        });
    }
};

// Role operations
const createRole = () => {
    showCreateRoleDialog.value = true;
    createRoleForm.reset();
};

const editRole = (role: Role) => {
    selectedRole.value = role;
    editRoleForm.id = String(role.id);
    editRoleForm.name = role.name;
    editRoleForm.permissions = role.permissions?.map(p => String(p.name)) || [];
    showEditRoleDialog.value = true;
};

const submitCreateRole = () => {
    if (!validateRoleForm(createRoleForm)) return;

    createRoleForm.post(route('roles.store'), {
        onSuccess: () => {
            showCreateRoleDialog.value = false;
            createRoleForm.reset();
            toast.success('Role created successfully');
        },
    });
};

const submitEditRole = () => {
    if (!validateRoleForm(editRoleForm)) return;

    editRoleForm.put(route('roles.update', { id: editRoleForm.id }), {
        onSuccess: () => {
            showEditRoleDialog.value = false;
            editRoleForm.reset();
            toast.success('Role updated successfully');
        },
    });
};

const confirmDeleteRole = (role: Role) => {
    selectedRole.value = role;
    showDeleteRoleDialog.value = true;
};

const deleteRole = () => {
    if (selectedRole.value) {
        useForm({}).delete(route('roles.destroy', { id: selectedRole.value.id }), {
            onSuccess: () => {
                showDeleteRoleDialog.value = false;
                selectedRole.value = null;
                toast.success('Role deleted successfully');
            },
        });
    }
};

// Permission operations
const createPermission = () => {
    showCreatePermissionDialog.value = true;
    createPermissionForm.reset();
};

const editPermission = (permission: Permission) => {
    selectedPermission.value = permission;
    editPermissionForm.id = String(permission.id);
    editPermissionForm.name = permission.name;
    showEditPermissionDialog.value = true;
};

const submitCreatePermission = () => {
    if (!validatePermissionForm(createPermissionForm)) return;

    createPermissionForm.post(route('permissions.store'), {
        onSuccess: () => {
            showCreatePermissionDialog.value = false;
            createPermissionForm.reset();
            toast.success('Permission created successfully');
        },
    });
};

const submitEditPermission = () => {
    if (!validatePermissionForm(editPermissionForm)) return;

    editPermissionForm.put(route('permissions.update', { id: editPermissionForm.id }), {
        onSuccess: () => {
            showEditPermissionDialog.value = false;
            editPermissionForm.reset();
            toast.success('Permission updated successfully');
        },
    });
};

const confirmDeletePermission = (permission: Permission) => {
    selectedPermission.value = permission;
    showDeletePermissionDialog.value = true;
};

const deletePermission = () => {
    if (selectedPermission.value) {
        useForm({}).delete(route('permissions.destroy', { id: selectedPermission.value.id }), {
            onSuccess: () => {
                showDeletePermissionDialog.value = false;
                selectedPermission.value = null;
                toast.success('Permission deleted successfully');
            },
        });
    }
};

// Toggle permission selection
const togglePermission = (permissionId: string, form: typeof createRoleForm | typeof editRoleForm) => {
    const index = form.permissions.indexOf(permissionId);
    if (index === -1) {
        form.permissions.push(permissionId);
    } else {
        form.permissions.splice(index, 1);
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="User Management" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <!-- Users Section -->
                <div class="flex items-center justify-between">
                    <HeadingSmall title="User Management" description="Create, edit and manage users" />

                    <Dialog v-model:open="showCreateDialog">
                        <DialogTrigger asChild>
                            <Button>Add New User</Button>
                        </DialogTrigger>
                        <DialogContent>
                            <DialogHeader>
                                <DialogTitle>Create New User</DialogTitle>
                                <DialogDescription> Fill in the details to create a new user account. </DialogDescription>
                            </DialogHeader>

                            <form @submit.prevent="submitCreateForm" class="space-y-4">
                                <div>
                                    <Label for="name">Name</Label>
                                    <Input id="name" v-model="createUserForm.name" type="text" />
                                    <InputError :message="createUserForm.errors.name || createErrors.name" />
                                </div>

                                <div>
                                    <Label for="email">Email</Label>
                                    <Input id="email" v-model="createUserForm.email" type="email" />
                                    <InputError :message="createUserForm.errors.email || createErrors.email" />
                                </div>

                                <div>
                                    <Label for="password">Password</Label>
                                    <Input id="password" v-model="createUserForm.password" type="password" />
                                    <InputError :message="createUserForm.errors.password || createErrors.password" />
                                </div>

                                <div>
                                    <Label for="password_confirmation">Confirm Password</Label>
                                    <Input id="password_confirmation" v-model="createUserForm.password_confirmation" type="password" />
                                    <InputError :message="createUserForm.errors.password_confirmation || createErrors.password_confirmation" />
                                </div>

                                <div>
                                    <Label for="role_id">Role</Label>
                                    <select id="role_id" v-model="createUserForm.role_id" class="w-full border rounded-md p-2">
                                        <option value="">Select a role</option>
                                        <option v-for="role in props.roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                                    </select>
                                    <InputError :message="createUserForm.errors.role_id || createErrors.role_id" />
                                </div>

                                <DialogFooter>
                                    <Button type="button" variant="outline" @click="showCreateDialog = false">Cancel</Button>
                                    <Button type="submit" :disabled="createUserForm.processing">Create User</Button>
                                </DialogFooter>
                            </form>
                        </DialogContent>
                    </Dialog>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Users</CardTitle>
                        <CardDescription>Manage your system users</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Name</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>Role</TableHead>
                                    <TableHead class="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="user in props.users" :key="user.id">
                                    <TableCell>{{ user.name }}</TableCell>
                                    <TableCell>{{ user.email }}</TableCell>
                                    <TableCell>{{ user.roles[0]?.name || 'No role' }}</TableCell>
                                    <TableCell class="text-right">
                                        <Button variant="ghost" size="sm" @click="editUser(user)" class="mr-2">Edit</Button>
                                        <Button variant="destructive" size="sm" @click="confirmDeleteUser(user)">Delete</Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <!-- Roles Section -->
                <div class="flex items-center justify-between">
                    <HeadingSmall title="Role Management" description="Create, edit and manage roles" />

                    <Dialog v-model:open="showCreateRoleDialog">
                        <DialogTrigger asChild>
                            <Button @click="createRole">Add New Role</Button>
                        </DialogTrigger>
                        <DialogContent>
                            <DialogHeader>
                                <DialogTitle>Create New Role</DialogTitle>
                                <DialogDescription> Fill in the details to create a new role. </DialogDescription>
                            </DialogHeader>

                            <form @submit.prevent="submitCreateRole" class="space-y-4">
                                <div>
                                    <Label for="role-name">Name</Label>
                                    <Input id="role-name" v-model="createRoleForm.name" type="text" />
                                    <InputError :message="createRoleForm.errors.name || roleErrors.name" />
                                </div>

                                <div>
                                    <Label>Permissions</Label>
                                    <div class="grid grid-cols-2 gap-2 mt-2">
                                        <div v-for="permission in props.permissions" :key="permission.id" class="flex items-center space-x-2">
                                            <input 
                                                type="checkbox" 
                                                :id="`permission-${permission.id}`" 
                                                :value="permission.name" 
                                                v-model="createRoleForm.permissions"
                                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            />
                                            <Label :for="`permission-${permission.id}`">{{ permission.name }}</Label>
                                        </div>
                                    </div>
                                    <InputError :message="createRoleForm.errors.permissions || roleErrors.permissions" />
                                </div>

                                <DialogFooter>
                                    <Button type="button" variant="outline" @click="showCreateRoleDialog = false">Cancel</Button>
                                    <Button type="submit" :disabled="createRoleForm.processing">Create Role</Button>
                                </DialogFooter>
                            </form>
                        </DialogContent>
                    </Dialog>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Roles</CardTitle>
                        <CardDescription>Manage your system roles</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Name</TableHead>
                                    <TableHead>Permissions</TableHead>
                                    <TableHead class="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="role in props.roles" :key="role.id">
                                    <TableCell>{{ role.name }}</TableCell>
                                    <TableCell>
                                        <div class="flex flex-wrap gap-1">
                                            <span v-for="permission in role.permissions" :key="permission.id" class="px-2 py-1 text-xs rounded-full text-black bg-gray-100">
                                                {{ permission.name }}
                                            </span>
                                            <span v-if="!role.permissions?.length" class="text-gray-400">No permissions</span>
                                        </div>
                                    </TableCell>
                                    <TableCell class="text-right">
                                        <Button variant="ghost" size="sm" @click="editRole(role)" class="mr-2">Edit</Button>
                                        <Button variant="destructive" size="sm" @click="confirmDeleteRole(role)">Delete</Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <!-- Dialogs for Users -->
                <Dialog v-model:open="showEditDialog">
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Edit User</DialogTitle>
                            <DialogDescription> Update user information. </DialogDescription>
                        </DialogHeader>

                        <form @submit.prevent="submitEditForm" class="space-y-4">
                            <div>
                                <Label for="edit-name">Name</Label>
                                <Input id="edit-name" v-model="editUserForm.name" type="text" />
                                <InputError :message="editUserForm.errors.name || editErrors.name" />
                            </div>

                            <div>
                                <Label for="edit-email">Email</Label>
                                <Input id="edit-email" v-model="editUserForm.email" type="email" />
                                <InputError :message="editUserForm.errors.email || editErrors.email" />
                            </div>

                            <div>
                                <Label for="edit-password">Password (leave blank to keep current)</Label>
                                <Input id="edit-password" v-model="editUserForm.password" type="password" />
                                <InputError :message="editUserForm.errors.password || editErrors.password" />
                            </div>

                            <div>
                                <Label for="edit-password_confirmation">Confirm Password</Label>
                                <Input id="edit-password_confirmation" v-model="editUserForm.password_confirmation" type="password" />
                                <InputError :message="editUserForm.errors.password_confirmation || editErrors.password_confirmation" />
                            </div>

                            <div>
                                <Label for="edit-role_id">Role</Label>
                                <select id="edit-role_id" v-model="editUserForm.role_id" class="w-full border rounded-md p-2">
                                    <option value="">Select a role</option>
                                    <option v-for="role in props.roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                                </select>
                                <InputError :message="editUserForm.errors.role_id || editErrors.role_id" />
                            </div>

                            <DialogFooter>
                                <Button type="button" variant="outline" @click="showEditDialog = false">Cancel</Button>
                                <Button type="submit" :disabled="editUserForm.processing">Update User</Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>

                <Dialog v-model:open="showDeleteDialog">
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Delete User</DialogTitle>
                            <DialogDescription> Are you sure you want to delete this user? This action cannot be undone. </DialogDescription>
                        </DialogHeader>

                        <DialogFooter>
                            <Button type="button" variant="outline" @click="showDeleteDialog = false">Cancel</Button>
                            <Button type="button" variant="destructive" @click="deleteUser">Delete User</Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>

                <!-- Dialogs for Roles -->
                <Dialog v-model:open="showEditRoleDialog">
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Edit Role</DialogTitle>
                            <DialogDescription> Update role information. </DialogDescription>
                        </DialogHeader>

                        <form @submit.prevent="submitEditRole" class="space-y-4">
                            <div>
                                <Label for="edit-role-name">Name</Label>
                                <Input id="edit-role-name" v-model="editRoleForm.name" type="text" />
                                <InputError :message="editRoleForm.errors.name || roleErrors.name" />
                            </div>

                            <div>
                                <Label>Permissions</Label>
                                <div class="grid grid-cols-2 gap-2 mt-2">
                                    <div v-for="permission in props.permissions" :key="permission.id" class="flex items-center space-x-2">
                                        <input 
                                            type="checkbox" 
                                            :id="`edit-permission-${permission.id}`" 
                                            :value="permission.name" 
                                            v-model="editRoleForm.permissions"
                                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        />
                                        <Label :for="`edit-permission-${permission.id}`">{{ permission.name }}</Label>
                                    </div>
                                </div>
                                <InputError :message="editRoleForm.errors.permissions || roleErrors.permissions" />
                            </div>

                            <DialogFooter>
                                <Button type="button" variant="outline" @click="showEditRoleDialog = false">Cancel</Button>
                                <Button type="submit" :disabled="editRoleForm.processing">Update Role</Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>

                <Dialog v-model:open="showDeleteRoleDialog">
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Delete Role</DialogTitle>
                            <DialogDescription> Are you sure you want to delete this role? This action cannot be undone. </DialogDescription>
                        </DialogHeader>

                        <DialogFooter>
                            <Button type="button" variant="outline" @click="showDeleteRoleDialog = false">Cancel</Button>
                            <Button type="button" variant="destructive" @click="deleteRole">Delete Role</Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>

                <!-- Dialogs for Permissions -->
                <Dialog v-model:open="showDeletePermissionDialog">
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Delete Permission</DialogTitle>
                            <DialogDescription> Are you sure you want to delete this permission? This action cannot be undone. </DialogDescription>
                        </DialogHeader>

                        <DialogFooter>
                            <Button type="button" variant="outline" @click="showDeletePermissionDialog = false">Cancel</Button>
                            <Button type="button" variant="destructive" @click="deletePermission">Delete Permission</Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>