<template>
    <Form columns="5">
        <FormSection title="Primary info" col-span="3">
            <div class="sm:col-span-3">
                <InputText
                    v-model="form.name"
                    v-model:error="errors.name"
                    name="name"
                    label="Name"
                />
            </div>
            <div class="sm:col-span-3">
                <InputText
                    v-model="form.email"
                    v-model:error="errors.email"
                    name="email"
                    label="Email address"
                />
            </div>
            <div class="sm:col-span-3">
                <InputPasswordGenerator
                    v-model="form.password"
                    v-model:error="errors.password"
                    name="password"
                    label="Password"
                    placeholder="Leave blank to remain the same"
                />
            </div>
            <div class="sm:col-span-3">
                <InputPassword
                    v-model="form.password_confirmation"
                    v-model:error="errors.password_confirmation"
                    name="password_confirmation"
                    label="Confirm password"
                />
            </div>

            <template v-if="hasPermission">
                <FormDivider title="Access info" />

                <div class="sm:col-span-3">
                    <InputSelect
                        v-model="form.active"
                        v-model:error="errors.active"
                        :options="{1: 'Yes', 0: 'No'}"
                        name="active"
                        label="Is active?"
                    />
                </div>
                <div class="sm:col-span-3">
                    <InputSelect
                        v-model="form.role"
                        v-model:error="errors.role"
                        :options="$page.props.options.roles"
                        name="role"
                        label="Role"
                    />
                </div>
            </template>
        </FormSection>

        <FormSection title="Avatar photo" col-span="2">
            <div class="sm:col-span-6">
                <InputMedia
                    v-model="form.avatars"
                    collection-name="avatars"
                    :accepted-mime-types="['image/jpeg', 'image/png', 'image/gif', 'image/webp']"
                />
            </div>
        </FormSection>
    </Form>
</template>

<script>
import Form from "crudhub/Components/Form.vue";
import FormDivider from "crudhub/Components/FormDivider.vue";
import FormSection from "crudhub/Components/FormSection.vue";
import InputMedia from "crudhub/Components/InputMedia.vue";
import InputPassword from "crudhub/Components/InputPassword.vue";
import InputPasswordGenerator from "crudhub/Components/InputPasswordGenerator.vue";
import InputSelect from "crudhub/Components/InputSelect.vue";
import InputText from "crudhub/Components/InputText.vue";

export default {
    props: {
        form: {
            type: Object,
            required: true,
        },
        errors: {
            type: Object,
            required: false,
            default: {},
        }
    },
    components: {
        Form,
        FormDivider,
        FormSection,
        InputMedia,
        InputSelect,
        InputPassword,
        InputPasswordGenerator,
        InputText,
    },
    computed: {
        hasPermission() {
            const auth = this.$page?.props?.auth?.data ?? {};

            return (auth.roles?.includes('Root') ?? false) || (auth.permissions?.includes('admins-edit') ?? false);
        }
    }
}
</script>
