<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';

defineProps<{
    passwordRules: string;
}>();

defineOptions({
    layout: {
        title: 'Daftar Akun Mahasiswa',
        description: 'Lengkapi data diri Anda untuk mulai belajar',
    },
});
</script>

<template>
    <Head title="Daftar" />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="name">Nama lengkap</Label>
                <Input
                    id="name"
                    type="text"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="name"
                    name="name"
                    placeholder="Nama sesuai data kampus"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid grid-cols-[1fr_8rem] gap-4">
                <div class="grid gap-2">
                    <Label for="nim">NIM</Label>
                    <Input
                        id="nim"
                        type="text"
                        required
                        :tabindex="2"
                        name="nim"
                        inputmode="numeric"
                        placeholder="Nomor Induk Mahasiswa"
                    />
                    <InputError :message="errors.nim" />
                </div>
                <div class="grid gap-2">
                    <Label for="semester">Semester</Label>
                    <select
                        id="semester"
                        name="semester"
                        required
                        :tabindex="3"
                        class="border-input focus-visible:border-ring focus-visible:ring-ring/50 h-9 w-full rounded-md border bg-transparent px-3 text-sm shadow-xs outline-none focus-visible:ring-[3px]"
                    >
                        <option value="" disabled selected>Pilih</option>
                        <option v-for="n in 14" :key="n" :value="n">
                            {{ n }}
                        </option>
                    </select>
                    <InputError :message="errors.semester" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="username">Username</Label>
                <Input
                    id="username"
                    type="text"
                    required
                    :tabindex="4"
                    autocomplete="username"
                    name="username"
                    placeholder="mis. aisyah_putri"
                />
                <InputError :message="errors.username" />
            </div>

            <div class="grid gap-2">
                <Label for="email">Email</Label>
                <Input
                    id="email"
                    type="email"
                    required
                    :tabindex="5"
                    autocomplete="email"
                    name="email"
                    placeholder="email@contoh.com"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <Label for="password">Password</Label>
                <PasswordInput
                    id="password"
                    required
                    :tabindex="6"
                    autocomplete="new-password"
                    name="password"
                    placeholder="Password"
                    :passwordrules="passwordRules"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation">Konfirmasi password</Label>
                <PasswordInput
                    id="password_confirmation"
                    required
                    :tabindex="7"
                    autocomplete="new-password"
                    name="password_confirmation"
                    placeholder="Ulangi password"
                    :passwordrules="passwordRules"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                class="mt-2 w-full"
                tabindex="8"
                :disabled="processing"
                data-test="register-user-button"
            >
                <Spinner v-if="processing" />
                Daftar
            </Button>
        </div>

        <div class="text-muted-foreground text-center text-sm">
            Sudah punya akun?
            <TextLink
                :href="login()"
                class="underline underline-offset-4"
                :tabindex="9"
                >Masuk</TextLink
            >
        </div>
    </Form>
</template>
