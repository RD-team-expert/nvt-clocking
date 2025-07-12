<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast ,Toaster } from 'vue-sonner';

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
import { type BreadcrumbItem, type User } from '@/types';

interface Props {
    users: User[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User Management',
        href: '/settings/user-management',
    },
];

// Form for creating a new user
const createUserForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

// Form for editing an existing user
const editUserForm = useForm({
    id: '',
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const showCreateDialog = ref(false);
const showEditDialog = ref(false);
const showDeleteDialog = ref(false);
const selectedUser = ref<User | null>(null);

// Frontend validation errors
const createErrors = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const editErrors = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
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
    };

    // Name validation
    if (!createUserForm.name.trim()) {
        createErrors.value.name = 'Name is required';
        isValid = false;
    } else if (createUserForm.name.length < 2) {
        createErrors.value.name = 'Name must be at least 2 characters';
        isValid = false;
    }

    // Email validation
    if (!createUserForm.email.trim()) {
        createErrors.value.email = 'Email is required';
        isValid = false;
    } else if (!validateEmail(createUserForm.email)) {
        createErrors.value.email = 'Please enter a valid email address';
        isValid = false;
    }

    // Password validation
    if (!createUserForm.password) {
        createErrors.value.password = 'Password is required';
        isValid = false;
    } else if (createUserForm.password.length < 8) {
        createErrors.value.password = 'Password must be at least 8 characters';
        isValid = false;
    }

    // Password confirmation validation
    if (createUserForm.password !== createUserForm.password_confirmation) {
        createErrors.value.password_confirmation = 'Passwords do not match';
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
    };

    // Name validation
    if (!editUserForm.name.trim()) {
        editErrors.value.name = 'Name is required';
        isValid = false;
    } else if (editUserForm.name.length < 2) {
        editErrors.value.name = 'Name must be at least 2 characters';
        isValid = false;
    }

    // Email validation
    if (!editUserForm.email.trim()) {
        editErrors.value.email = 'Email is required';
        isValid = false;
    } else if (!validateEmail(editUserForm.email)) {
        editErrors.value.email = 'Please enter a valid email address';
        isValid = false;
    }

    // Password validation (only if provided)
    if (editUserForm.password) {
        if (editUserForm.password.length < 8) {
            editErrors.value.password = 'Password must be at least 8 characters';
            isValid = false;
        }

        // Password confirmation validation
        if (editUserForm.password !== editUserForm.password_confirmation) {
            editErrors.value.password_confirmation = 'Passwords do not match';
            isValid = false;
        }
    }

    return isValid;
};

// Submit new user form
const submitCreateForm = () => {
    if (!validateCreateForm()) {
        return;
    }

    createUserForm.post(route('users.store'), {
        onSuccess: () => {
            showCreateDialog.value = false;
            createUserForm.reset();
            toast.success('User created successfully');
        },
    });
};

// Edit user
const editUser = (user: User) => {
    selectedUser.value = user;
    editUserForm.id = String(user.id);
    editUserForm.name = user.name;
    editUserForm.email = user.email;
    editUserForm.password = '';
    editUserForm.password_confirmation = '';
    showEditDialog.value = true;
};

// Submit edit user form
const submitEditForm = () => {
    if (!validateEditForm()) {
        return;
    }

    editUserForm.put(route('users.update', { id: editUserForm.id }), {
        onSuccess: () => {
            showEditDialog.value = false;
            editUserForm.reset();
            toast.success('User updated successfully');
        },
    });
};

// Delete user
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
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="User Management" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
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
                                    <TableHead class="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="user in props.users" :key="user.id">
                                    <TableCell>{{ user.name }}</TableCell>
                                    <TableCell>{{ user.email }}</TableCell>
                                    <TableCell class="text-right">
                                        <Button variant="ghost" size="sm" @click="editUser(user)" class="mr-2">Edit</Button>
                                        <Button variant="destructive" size="sm" @click="confirmDeleteUser(user)">Delete</Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <!-- Edit User Dialog -->
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

                            <DialogFooter>
                                <Button type="button" variant="outline" @click="showEditDialog = false">Cancel</Button>
                                <Button type="submit" :disabled="editUserForm.processing">Update User</Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>

                <!-- Delete User Dialog -->
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
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
